<?php
declare(strict_types=1);
namespace Nicode\EasyForms\Submission;

use Nicode\EasyForms\Domain\RepeatedInstances;

/** Parsed envelope for the shared pipeline's addressed submission entry point. */
final readonly class RepeatedSubmitRequest
{
    public function __construct(public SubmitRequest $request, public RepeatedInstances $instances) {}
}
