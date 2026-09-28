<?php
declare(strict_types=1);
for ($round = 0; $round < 3; $round++) {
$concurrentScope = hash('sha256', random_bytes(32)); $rateWorkers = [];
try {
    for ($worker = 0; $worker < 8; $worker++) {
        $command = [PHP_BINARY];
        if ($postgres) { array_push($command, '-d', 'extension=pgsql', '-d', 'extension=pdo_pgsql'); }
        array_push($command, __DIR__ . '/rate-limit-worker.php', $postgres ? 'postgresql' : ($mysql8 ? 'mysql8' : 'mysql'), $concurrentScope);
        $process = proc_open($command, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $root);
        if (!is_resource($process)) { throw new RuntimeException('Rate worker unavailable.'); }
        $rateWorkers[] = [$process, $pipes];
    }
    foreach ($rateWorkers as [$process, $pipes]) { if (trim((string) fgets($pipes[1])) !== 'ready') { throw new RuntimeException('Rate worker failed before barrier.'); } }
    foreach ($rateWorkers as [$process, $pipes]) { fwrite($pipes[0], "go\n"); fflush($pipes[0]); }
    $outcomes = [];
    foreach ($rateWorkers as [$process, $pipes]) {
        $outcome = trim(stream_get_contents($pipes[1])); $errors = stream_get_contents($pipes[2]);
        if (!in_array($outcome, ['allowed', 'denied'], true) || $errors !== '') { throw new RuntimeException('Concurrent limiter failed: ' . $errors); }
        $outcomes[] = $outcome;
    }
    if (count(array_filter($outcomes, static fn (string $state): bool => $state === 'allowed')) !== 3) { throw new RuntimeException('Concurrent first requests violated admission limit.'); }
    $counter = $connection->row('SELECT attempts FROM ' . $connection->table('rate_limits') . ' WHERE scope_hash = :scope', [':scope' => $concurrentScope]);
    if ((int) $counter['attempts'] !== 3) { throw new RuntimeException('Concurrent limiter stored an incorrect counter.'); }
    echo "Rate limit concurrency: eight synchronized processes create one window with exactly three admissions.\n";
} finally {
    foreach ($rateWorkers as [$process, $pipes]) { foreach ($pipes as $pipe) { if (is_resource($pipe)) { fclose($pipe); } } if (is_resource($process)) { proc_close($process); } }
}
}
