<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Jobs;

use Nicode\EasyForms\Infrastructure\Database\JobRepository;
use Nicode\EasyForms\Registry\JobHandlerRegistry;

final readonly class JobWorker
{
    public function __construct(private JobRepository $jobs, private JobHandlerRegistry $handlers, private ?\Nicode\EasyForms\Infrastructure\Database\Connection $db = null, private ?\Nicode\EasyForms\Infrastructure\Database\TechnicalLog $log = null) {}
    public function tick(int $chunkSize = 200): bool
    {
        if ($chunkSize < 1 || $chunkSize > 500) { throw new \InvalidArgumentException('Invalid worker chunk size.'); }
        $lease = $this->jobs->claim();
        if ($lease === null) { return false; }
        try {
            $handler = $this->handlers->get($lease->type);
            if ($handler->validateConfiguration($lease->parameters, '/job') !== []) { $this->jobs->fail($lease, 'invalid_configuration'); return true; }
            $chunk = function () use ($handler, $lease, $chunkSize): void {
                $progress = $handler->run($lease, $chunkSize);
                $this->jobs->checkpoint($lease, $progress);
            };
            if ($handler instanceof \Nicode\EasyForms\Contract\TransactionalJobHandlerInterface) {
                if ($this->db === null) { throw new \LogicException('Transactional worker connection unavailable.'); }
                $this->db->transaction($chunk);
            } else { $chunk(); }
        } catch (LeaseLost $error) {
            // Ownership was lost; this worker must never write a new state.
            throw $error;
        } catch (RetryableJobFailure $error) {
            $this->jobs->retry($lease, $error->resultCode, $error->delaySeconds);
            $this->log?->record('WARNING', 'job.retry', $lease->uuid, ['job_id' => $lease->id]);
        } catch (\Throwable $error) {
            $this->jobs->fail($lease, 'handler_failed');
            $this->log?->record('ERROR', 'job.failed', $lease->uuid, ['job_id' => $lease->id]);
            throw $error;
        }
        return true;
    }
}
