<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Submission;

final readonly class PersistedSubmission
{
    public function __construct(public int $id, public string $uuid, public bool $replayed) {}
}
