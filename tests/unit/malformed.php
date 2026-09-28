<?php
declare(strict_types=1);

test('malformed nested authoring data returns diagnostics without PHP warnings', function (): void {
    $mutations = [
        static function (&$d) { $d['elements'][0]['width'] = 'bad'; },
        static function (&$d) { $d['elements'][0]['type'] = []; },
        static function (&$d) { $d['fields'][0]['options'] = 'bad'; },
        static function (&$d) { $d['fields'][0]['options'] = [null]; },
        static function (&$d) { $d['fields'][0]['validators'] = 'bad'; },
        static function (&$d) { $d['fields'][0]['source'] = 'bad'; },
        static function (&$d) { $d['fields'][0]['source'] = ['type' => 'x', 'dependencies' => 'bad']; },
        static function (&$d) { $d['fields'][0]['config'] = ['required' => []]; },
        static function (&$d) { $d['fields'][0]['config'] = ['help' => []]; },
        static function (&$d) { $d['fields'][0]['config'] = ['placeholder' => []]; },
        static function (&$d) { $d['fields'][0]['config'] = ['min' => []]; },
        static function (&$d) { $d['elements'][0]['title'] = []; },
        static function (&$d) { $d['actions'] = [42]; },
        static function (&$d) { $d['rules'] = [null]; },
    ];
    set_error_handler(static function (int $severity, string $message): never { throw new ErrorException($message, 0, $severity); });
    try {
        foreach ($mutations as $mutate) { $draft = definition(); $mutate($draft); same(false, compiler()->compile($draft)->successful()); }
    } finally { restore_error_handler(); }
});

test('malformed layout parents return exact diagnostics instead of PHP offset errors', function (): void {
    set_error_handler(static function (int $severity, string $message): never { throw new ErrorException($message, 0, $severity); });
    try {
        foreach ([[], ['uuid'=>'bad'], false, true, 0, 1, 1.5, '', 'bad'] as $parent) {
            $draft = definition(); $draft['elements'][0]['parent_uuid'] = $parent;
            $result = compiler()->compile($draft); same(false, $result->successful());
            $errors = array_values(array_filter($result->diagnostics, fn ($error) => $error->code === 'element.parent'));
            same(1, count($errors)); same('/elements/0/parent_uuid', $errors[0]->path);
        }
        $draft = definition(); $draft['elements'][0]['parent_uuid'] = null;
        same(true, compiler()->compile($draft)->successful());
    } finally { restore_error_handler(); }
});
