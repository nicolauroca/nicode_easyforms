<?php
declare(strict_types=1);
namespace NicodeFixture\Plugin\Easyforms\ProviderFixture\Provider;

final readonly class FixtureOperator implements \Nicode\EasyForms\Contract\RuleOperatorInterface, \Nicode\EasyForms\Contract\BrowserProviderInterface
{
    public function id(): string { return 'fixture.suffix'; }
    public function version(): string { return '1.2.0'; }
    public function metadata(): array { return ['id' => $this->id(), 'version' => $this->version(), 'datatypes' => ['text', 'selection']]; }
    public function browser(): array { return ['asset' => 'fixture.browser', 'public_config' => []]; }
    public function validateConfiguration(array $configuration, string $path): array { return []; }
    public function evaluate(mixed $left, mixed $right, string $datatype): bool { return is_string($left) && is_string($right) && str_ends_with($left, $right); }
}
