<?php
declare(strict_types=1);

$failed = false;
foreach (['src', 'tests', 'tools'] as $directory) {
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(dirname(__DIR__) . '/' . $directory)) as $file) {
        if ($file->getExtension() !== 'php') {
            continue;
        }
        exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file->getPathname()), $output, $code);
        if ($code !== 0) {
            echo implode("\n", $output) . "\n";
            $failed = true;
        }
        $output = [];
    }
}
echo $failed ? "PHP lint failed.\n" : "PHP lint passed.\n";
exit($failed ? 1 : 0);
