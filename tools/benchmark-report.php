<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$data = json_decode(file_get_contents($root . '/build/scale-results.json'), true, 512, JSON_THROW_ON_ERROR);
$text = "# Local SQL scale benchmark\n\nRecorded: " . $data['timestamp'] . ". Database: " . $data['database'] . '; PHP ' . $data['php'] . ".\n\n";
$text .= "This is a synthetic repository-level SQL benchmark on the isolated local MariaDB instance. It is not an installed-extension acceptance test, a throughput guarantee, or a result for PostgreSQL/MySQL. Fixtures are seeded directly with SQL; they do not measure submission processing or action delivery.\n\n";
$text .= "## Dataset\n\n";
foreach ($data['counts'] as $name => $count) { $text .= '- ' . $name . ': ' . number_format($count) . ".\n"; }
$text .= '- Largest form: ' . number_format($data['largest_form_rows']) . " responses.\n\n## Query timings\n\n20 sequential samples per query, 50-row page limit. Warm/cold effects are not isolated.\n\n| Query | p50 ms | p95 ms | Maximum ms |\n|---|---:|---:|---:|\n";
foreach ($data['results'] as $name => $result) {
    $text .= '| ' . $name . ' | ' . $result['p50_ms'] . ' | ' . $result['p95_ms'] . ' | ' . $result['max_ms'] . " |\n";
}
$text .= "\n## Query plans\n\n";
if (isset($data['innodb_buffer_pool_bytes'])) {
    $text .= 'InnoDB buffer pool: ' . number_format($data['innodb_buffer_pool_bytes']) . " bytes. Execution analysis below is one additional sample per query, outside the 20 timing samples.\n\n";
}
foreach ($data['results'] as $name => $result) {
    $text .= '### ' . $name . "\n\n```json\n" . json_encode($result['explain'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n```\n\n";
    if (isset($result['analysis'])) {
        $text .= 'Optimizer: ' . round($result['analysis']['query_optimization']['r_total_time_ms'] ?? 0, 4) . ' ms; execution: ' . round($result['analysis']['query_block']['r_total_time_ms'] ?? 0, 4) . " ms. The full executed plan is retained in `build/scale-results.json`.\n\n";
    }
}
$text .= "## Cursor and migration checks\n\n";
$text .= '100 keyset pages returned 5,000 distinct response IDs in ' . round($data['cursor_5000_rows_ms'], 4) . ' ms. PHP peak allocated memory: ' . number_format($data['php_peak_bytes']) . " bytes.\n\n";
if (isset($data['identity_schema_check_ms'])) { $text .= 'ADR 0018 identity schema inspection/upgrade during this run: ' . round($data['identity_schema_check_ms'], 4) . " ms. Already-upgraded fixtures measure the idempotent check, not an ALTER of three million rows.\n\n"; }
$text .= "The first ADR 0018 migration of this existing three-million-index-row fixture completed in 34,576.59 ms (runner output on 2026-09-27). Cardinalities remained unchanged. A later run showed a global-search p95 of 587.3754 ms versus 3.0174 ms on 2026-09-26. Profiling localized the delay to optimizer planning (one sample: 322.6047 ms planning, 0.3293 ms execution); this does not establish migration causality. The small buffer pool, intervening workloads, statistics and additional indexes prevent treating these runs as a controlled before/after comparison. Global planning variability remains a performance risk to measure under deployment-sized resources.\n\n";
$text .= "## Export jobs\n\nThe independent export runner reads the existing fixture without reseeding or changing responses. Each format exports one million responses across 100 form-scoped jobs; the largest individual artifact contains 500,000 responses. Jobs process at most 500 rows per claim and checkpoint. An independent unbuffered canonical-data traversal computes the expected SHA-256 for every artifact, including record order and selected values. Artifacts are removed and download metadata invalidated after verification.\n\n| Format | Recorded UTC | Rows | Bytes | Export seconds | Total seconds | Peak PHP bytes |\n|---|---|---:|---:|---:|---:|---:|\n";
foreach (['csv', 'json'] as $format) {
    $path = $root . '/build/scale-export-' . $format . '-results.json';
    if (!is_file($path)) { continue; }
    $export = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    if (($export['passed'] ?? false) !== true) { throw new RuntimeException('Cannot report a failed export benchmark.'); }
    $text .= '| ' . strtoupper($format) . ' | ' . $export['timestamp'] . ' | ' . number_format($export['rows']) . ' | ' . number_format($export['bytes']) . ' | ' . round($export['export_seconds'], 2) . ' | ' . round($export['total_seconds'], 2) . ' | ' . number_format($export['php_peak_bytes']) . " |\n";
}
$text .= "\nExport measurements use MariaDB 11.4.5, a 128 MiB PHP memory limit, and sequential synthetic jobs. Peak PHP allocation excludes database-server memory and operating-system buffers. Export seconds sum handler/claim/checkpoint time; total seconds also include independent verification and cleanup. This is neither a single million-row artifact nor an HTTP/concurrent throughput guarantee. Functional filtering, ACL and lifecycle checks on other engines are recorded separately in the requirement acceptance ledger.\n\n";
$text .= "## Reproduce\n\nStart the isolated fixture database using `tools/start-test-database.ps1`, then run `php tests/scale.php --seed`. Subsequent runs can use `php tests/scale.php`. Run `php -d memory_limit=128M tests/scale-export.php csv` and then `php -d memory_limit=128M tests/scale-export.php json` against the existing complete fixture. Regenerate this report with `php tools/benchmark-report.php`. The runners refuse other host/database targets.\n\nRemaining scale acceptance includes concurrent ingestion/workers, the complete administrative UI, operational resource budgets, and throughput on additional database engines.\n";
file_put_contents($root . '/docs/SCALE_BENCHMARK.md', $text);
echo "Scale benchmark report recorded.\n";
