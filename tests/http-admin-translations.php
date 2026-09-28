<?php
declare(strict_types=1);

$translated = $api('create', ['name' => 'HTTP language acceptance', 'alias' => 'translation-http-' . bin2hex(random_bytes(6))]);
$translatedEdit = $api('record', query: ['id' => $translated['id']]); $translatedDraft = $translatedEdit['draft'];
$translatedUuid = 'd17198c3-465d-4ca9-9b21-8915d87634be';
$translatedDraft['elements'] = [['uuid' => $translatedUuid, 'type' => 'field', 'parent_uuid' => null]];
$translatedDraft['fields'] = [['uuid' => $translatedUuid, 'name' => 'answer', 'type' => 'text', 'config' => ['label' => 'Original answer', 'required' => true]]];
$translatedDraft['base_language'] = 'en-GB';
$translatedDraft['translations'] = ['es-ES' => ['form' => ['description' => '<script>untrusted text</script>'], 'fields' => [$translatedUuid => ['label' => 'Respuesta traducida', 'help' => 'Ayuda traducida']], 'validation' => [$translatedUuid => ['required' => 'Escribe una respuesta']]]];
$translatedSaved = $api('save', ['id' => $translated['id'], 'revision' => 0, 'draft' => $translatedDraft]);
$translatedPreview = $api('preview', query: ['id' => $translated['id'], 'locale' => 'es-ES']);
foreach (['Respuesta traducida', 'Ayuda traducida', '>Enviar</button>', '&lt;script&gt;untrusted text&lt;/script&gt;', 'Escribe una respuesta'] as $text) { $assert(str_contains($translatedPreview['html'], $text), 'Translated native preview omitted expected presentation content.'); }
$assert(!str_contains($translatedPreview['html'], '<script>untrusted text</script>'), 'Translated description became executable markup.');
$fallbackPreview = $api('preview', query: ['id' => $translated['id'], 'locale' => 'fr-FR']);
$assert(str_contains($fallbackPreview['html'], 'Original answer') && !str_contains($fallbackPreview['html'], 'Respuesta traducida'), 'Missing locale did not fall back to original content.');
$api('preview', query: ['id' => $translated['id'], 'locale' => '../es-ES'], expected: 422);
$translatedPublished = $api('publish', ['id' => $translated['id'], 'revision' => $translatedSaved['revision']]);
$translatedDraft['translations']['es-ES']['fields'][$translatedUuid]['label'] = 'New draft only';
$api('save', ['id' => $translated['id'], 'revision' => $translatedPublished['revision'], 'draft' => $translatedDraft]);
$historicTranslation = $api('preview', query: ['id' => $translated['id'], 'version' => $translatedPublished['version_id'], 'locale' => 'es-ES']);
$assert(str_contains($historicTranslation['html'], 'Respuesta traducida') && !str_contains($historicTranslation['html'], 'New draft only'), 'Historical preview leaked a later draft translation.');
echo "Native translations HTTP: saved language content, Spanish static controls, per-property fallback, safe markup, invalid locale and immutable historical preview passed.\n";
