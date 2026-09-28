<?php
declare(strict_types=1);

$normalizers = json_decode(file_get_contents(__DIR__ . '/../fixtures/normalizers.json'), true, 512, JSON_THROW_ON_ERROR);
foreach ($normalizers as $i => $fixture) {
    test('shared normalizer ' . $i . ': ' . $fixture['type'], static function () use ($fixture): void {
        $provider = registry()->get($fixture['type']);
        if ($fixture['error'] ?? false) { raises(InvalidArgumentException::class, fn () => $provider->normalize($fixture['raw'], [])); return; }
        $value = $provider->normalize($fixture['raw'], []);
        // Browser integers use exact strings to avoid binary Number precision loss.
        if ($fixture['datatype'] === 'integer' && $value !== null) { $value = (string) $value; }
        same($fixture['expected'], $value);
    });
}
test('indexed values fail validation before database precision or length errors', function (): void {
    $draft = definition(); $uuid = $draft['fields'][0]['uuid']; $draft['fields'][0]['index'] = true;
    same(['index_length'], validation()->validate(compiler()->compile($draft)->spec, [$uuid => str_repeat('a', 256)])->errors[$uuid]);
    $draft['fields'][0]['type'] = 'decimal';
    same(['index_precision'], validation()->validate(compiler()->compile($draft)->spec, [$uuid => '1.1234567890123'])->errors[$uuid]);
});
