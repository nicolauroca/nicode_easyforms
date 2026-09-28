<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Application;

use Nicode\EasyForms\Export\ExportWorkspace;
use Nicode\EasyForms\Infrastructure\Database\JobRepository;
use Nicode\EasyForms\Storage\Download;

final readonly class ExportDownloads
{
    public function __construct(private JobRepository $jobs, private ExportWorkspace $workspace, private \Closure $authorize) {}
    public function open(int $jobId, int $actor): Download
    {
        $job = $this->jobs->get($jobId); $parameters = json_decode($job['parameters'], true, 512, JSON_THROW_ON_ERROR); $form = $parameters['form_id'] ?? 0;
        if ($job['state'] !== 'completed' || !in_array($job['job_type'], ['export-csv', 'export-json'], true) || $job['artifact_key'] !== $job['uuid'] || $job['expires_at'] === null || $job['expires_at'] <= gmdate('Y-m-d H:i:s')
            || !(($this->authorize)($actor, $form, 'easyforms.submissions.export'))
            || ((int) $job['creator_id'] !== $actor && !(($this->authorize)($actor, $form, 'easyforms.jobs.manage')))
            || (($parameters['include_sensitive'] ?? false) && !(($this->authorize)($actor, $form, 'easyforms.submissions.view_sensitive')))) { throw new \OutOfBoundsException('Export unavailable.'); }
        $format = $job['job_type'] === 'export-json' ? 'json' : 'csv';
        $stream = $this->workspace->open($job['uuid'], $format);
        return new Download($stream, ['Content-Type' => ($format === 'json' ? 'application/json' : 'text/csv') . '; charset=utf-8', 'Content-Disposition' => 'attachment; filename="easyforms-' . $job['uuid'] . '.' . $format . '"', 'Content-Length' => (string) fstat($stream)['size'], 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }
}
