<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Contract;

use Nicode\EasyForms\Domain\Diagnostic;

interface ProviderInterface
{
    public function id(): string;
    public function version(): string;
    public function metadata(): array;
    /** @return list<Diagnostic> */
    public function validateConfiguration(array $configuration, string $path): array;
}
