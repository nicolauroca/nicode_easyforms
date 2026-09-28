<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Contract;

interface FieldRendererInterface
{
    public function render(array $field, string $instance, mixed $value, array $errors = []): string;
}
