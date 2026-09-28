<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Validation;

use Nicode\EasyForms\Rules\RuleResult;

final readonly class ValidationResult
{
    public function __construct(public array $values, public array $errors, public RuleResult $rules) {}
    public function valid(): bool { return $this->errors === []; }
}
