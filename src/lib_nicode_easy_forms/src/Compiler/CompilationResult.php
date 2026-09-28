<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Compiler;

use Nicode\EasyForms\Domain\Diagnostic;
use Nicode\EasyForms\Domain\FormSpec;

final readonly class CompilationResult
{
    /** @param list<Diagnostic> $diagnostics */
    public function __construct(public ?FormSpec $spec, public array $diagnostics)
    {
    }

    public function successful(): bool
    {
        return $this->spec !== null;
    }
}
