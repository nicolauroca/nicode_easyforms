<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Compiler;

final class CompilationException extends \DomainException
{
    public function __construct(public readonly array $diagnostics) { parent::__construct('Form compilation failed.'); }
}
