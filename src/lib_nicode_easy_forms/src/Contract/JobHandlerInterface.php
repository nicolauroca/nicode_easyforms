<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Contract;

use Nicode\EasyForms\Jobs\JobLease;
use Nicode\EasyForms\Jobs\JobProgress;

interface JobHandlerInterface extends ProviderInterface
{
    /** A bounded, idempotent chunk. Cursor persistence follows successful execution. */
    public function run(JobLease $job, int $limit): JobProgress;
}
