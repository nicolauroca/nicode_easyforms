<?php
declare(strict_types=1);

test('indexed defaults and constant prefills respect portable projection limits before publication', function (): void {
    $cases = [
        ['text', str_repeat('😀',256), str_repeat('😀',255)],
        ['decimal','1.1234567890123','1.123456789012'],
        ['decimal','100000000000000000000000000','99999999999999999999999999'],
        ['date','0999-12-31','1000-01-01'],
        ['datetime-local','0999-12-31T23:59','1000-01-01T00:00'],
    ];
    foreach ($cases as [$type,$invalid,$valid]) {
        foreach (['default','constant'] as $mode) {
            $draft = definition(); $draft['fields'][0]['type'] = $type; $draft['fields'][0]['index'] = true;
            $set = static function (array &$draft, mixed $value) use ($mode): void {
                if ($mode === 'default') { $draft['fields'][0]['config']['default'] = $value; }
                else { $draft['fields'][0]['prefill'] = ['type' => 'constant','value' => $value]; }
            };
            $set($draft,$invalid); $result = compiler()->compile($draft);
            same(false,$result->successful());
            same([$mode === 'default' ? 'field.default.index' : 'field.prefill'],array_column($result->diagnostics,'code'));
            same([$mode === 'default' ? '/fields/0/config/default' : '/fields/0/prefill'],array_column($result->diagnostics,'path'));
            $draft['fields'][0]['persist'] = false; same(true,compiler()->compile($draft)->successful());
            $draft['fields'][0]['persist'] = true; $draft['fields'][0]['index'] = false; same(true,compiler()->compile($draft)->successful());
            $draft['fields'][0]['index'] = true; $set($draft,$valid); same(true,compiler()->compile($draft)->successful());
        }
    }
});
