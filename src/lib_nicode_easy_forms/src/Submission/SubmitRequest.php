<?php
declare(strict_types=1);
namespace Nicode\EasyForms\Submission;
final readonly class SubmitRequest
{
    public function __construct(public int $formId, public int $versionId, public string $attempt, public array $values, public array $files = [], public string $honeypot = '', public ?string $captchaAnswer = null) {}
}
