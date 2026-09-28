<?php
declare(strict_types=1);

test('compiler advisories preserve successful snapshots and retain code path and severity', function (): void {
    $draft = definition(); $draft['fields'][0]['config']['label'] = '  ';
    $draft['fields'][0] += ['sensitive' => true, 'index' => true, 'allow_sensitive_index' => true];
    $result = compiler()->compile($draft);
    same(true, $result->successful());
    same(['a11y.field_label', 'security.sensitive_index'], array_column($result->diagnostics, 'code'));
    same(['WARNING', 'WARNING'], array_column($result->diagnostics, 'severity'));
    same(['/fields/0/config/label', '/fields/0/index'], array_column($result->diagnostics, 'path'));
    same('  ', $result->spec->toArray()['fields'][0]['config']['label']);
    foreach (['hidden', 'system'] as $type) {
        $draft['fields'][0]['type'] = $type; $draft['fields'][0]['index'] = false;
        same([], compiler()->compile($draft)->diagnostics);
    }
    $draft = definition(); $draft['fields'][0] += ['sensitive' => true, 'index' => true];
    $blocked = compiler()->compile($draft); same(false, $blocked->successful());
    same(true, in_array('field.index.sensitive', array_column($blocked->diagnostics, 'code'), true));
});

test('advisories cannot turn malformed label data into PHP runtime failures', function (): void {
    set_error_handler(static function (int $severity, string $message): never { throw new ErrorException($message, 0, $severity); });
    try {
        foreach ([null, false, 123, [], ['nested' => 'value']] as $label) {
            $draft = definition(); $draft['fields'][0]['config']['label'] = $label;
            $result = compiler()->compile($draft);
            same(false, $result->successful());
            same(true, in_array('schema.type', array_column($result->diagnostics, 'code'), true));
        }
    } finally { restore_error_handler(); }
});

test('translated blank-label advisories identify the locale without changing the base snapshot', function (): void {
    $draft = definition(); $uuid = $draft['fields'][0]['uuid'];
    $draft['fields'][0]['config']['label'] = 'Answer';
    $draft['translations'] = ['es-ES' => ['fields' => [$uuid => ['label' => ' ']]], 'fr-FR' => ['fields' => [$uuid => ['label' => 'Réponse']]]];
    $result = compiler()->compile($draft);
    same(true, $result->successful());
    same(['a11y.field_label'], array_column($result->diagnostics, 'code'));
    same(['/translations/es-ES/fields/0/config/label'], array_column($result->diagnostics, 'path'));
    same(['WARNING'], array_column($result->diagnostics, 'severity'));
    same('Answer', $result->spec->toArray()['fields'][0]['config']['label']);
    $draft['translations']['es-ES']['fields'][$uuid]['label'] = 'Respuesta';
    same([], compiler()->compile($draft)->diagnostics);
});
