<?php
declare(strict_types=1);
namespace Nicode\EasyForms\Submission;
final class SubmissionFailure extends \RuntimeException
{
    public function __construct(public readonly string $category, public readonly array $errors = [], public readonly int $retryAfter = 0) { parent::__construct($category); }
}
