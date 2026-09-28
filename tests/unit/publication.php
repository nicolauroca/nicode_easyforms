<?php
declare(strict_types=1);

test('publication checks private storage, mail configuration and secret references without exposing values', function (): void {
    $secrets = new class implements Nicode\EasyForms\Contract\SecretStoreInterface {
        public function get(string $reference): string { if ($reference === 'AVAILABLE') { return 'private-value'; } throw new RuntimeException('private-value'); }
    };
    $policy = new Nicode\EasyForms\Application\PublicationReadiness(new Nicode\EasyForms\Registry\StorageProviderRegistry(), $secrets, static fn (): bool => false);
    $draft = definition(); $draft['fields'][0]['type'] = 'file';
    $draft['actions'] = [['type' => 'email_notification', 'config' => []], ['type' => 'webhook', 'config' => ['signing_secret' => 'MISSING']]];
    try { $policy->assert(new Nicode\EasyForms\Domain\FormSpec($draft)); throw new RuntimeException('Incomplete deployment was publishable.'); }
    catch (Nicode\EasyForms\Compiler\CompilationException $error) {
        same(3, count($error->diagnostics)); same(false, str_contains(json_encode($error->diagnostics), 'private-value'));
    }
    $draft['fields'][0]['type'] = 'text'; $draft['actions'][0]['enabled'] = false; $draft['actions'][1]['config']['signing_secret'] = 'AVAILABLE';
    $policy->assert(new Nicode\EasyForms\Domain\FormSpec($draft));
    $unavailable = new Nicode\EasyForms\Registry\StorageProviderRegistry(); $unavailable->register(new Nicode\EasyForms\Storage\UnavailableStorage());
    $missingVolume = new Nicode\EasyForms\Application\PublicationReadiness($unavailable, $secrets, static fn (): bool => true);
    $missingVolume->assert(new Nicode\EasyForms\Domain\FormSpec($draft));
    $draft['fields'][0]['type'] = 'file';
    raises(Nicode\EasyForms\Compiler\CompilationException::class, fn () => $missingVolume->assert(new Nicode\EasyForms\Domain\FormSpec($draft)));
    raises(RuntimeException::class, fn () => $unavailable->get('local')->exists(str_repeat('a', 64)));
});

test('environment secret references cannot read arbitrary environment variables', function (): void {
    $reference = 'TEST_' . strtoupper(bin2hex(random_bytes(5))); $name = 'NICODE_EASYFORMS_SECRET_' . $reference;
    putenv($name . '=synthetic-secret');
    try {
        $secrets = new Nicode\EasyForms\Security\EnvironmentSecrets(); same('synthetic-secret', $secrets->get($reference));
        raises(DomainException::class, fn () => $secrets->get('../PATH'));
        raises(DomainException::class, fn () => $secrets->get('PATH'));
    } finally { putenv($name); }
});

test('publication requires a renderer only for field types used by the form', function (): void {
    $secrets = new Nicode\EasyForms\Security\EnvironmentSecrets();
    $renderers = new Nicode\EasyForms\Rendering\FieldRendererRegistry();
    $renderers->register('text', new Nicode\EasyForms\Rendering\CoreFieldRenderer());
    $policy = new Nicode\EasyForms\Application\PublicationReadiness(new Nicode\EasyForms\Registry\StorageProviderRegistry(), $secrets, static fn (): bool => true, $renderers);
    $draft = definition(); $policy->assert(new Nicode\EasyForms\Domain\FormSpec($draft));
    $draft['fields'][0]['type'] = 'custom-without-renderer';
    try { $policy->assert(new Nicode\EasyForms\Domain\FormSpec($draft)); throw new RuntimeException('Renderer-less custom field published.'); }
    catch (Nicode\EasyForms\Compiler\CompilationException $error) { same('publication.renderer', $error->diagnostics[0]->code); }
});
