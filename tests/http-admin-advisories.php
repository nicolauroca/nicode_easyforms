<?php
declare(strict_types=1);
$advisoryForm = $api('create', ['name' => 'Compiler advisory acceptance', 'alias' => 'advisory-' . bin2hex(random_bytes(6))]);
$advisoryDraft = $api('record', query: ['id' => $advisoryForm['id']])['draft'];
$advisoryField = 'f82f564b-df0c-4dfe-823a-978146410f78';
$advisoryDraft['elements'] = [['uuid' => $advisoryField, 'type' => 'field']];
$advisoryDraft['fields'] = [['uuid' => $advisoryField, 'name' => 'private_answer', 'type' => 'text', 'config' => ['label' => ' '], 'sensitive' => true, 'index' => true, 'allow_sensitive_index' => true]];
$advisoryRevision = $api('save', ['id' => $advisoryForm['id'], 'revision' => 0, 'draft' => $advisoryDraft])['revision'];
$advisoryPreview = $api('preview', query: ['id' => $advisoryForm['id']]);
$advisoryPublished = $api('publish', ['id' => $advisoryForm['id'], 'revision' => $advisoryRevision]);
foreach ([$advisoryPreview, $advisoryPublished] as $response) {
    $assert(array_column($response['diagnostics'], 'code') === ['a11y.field_label', 'security.sensitive_index'] && array_column($response['diagnostics'], 'severity') === ['WARNING', 'WARNING'], 'Preview/publication lost nonblocking advisories.');
}
$assert(is_string($advisoryPreview['html']) && $advisoryPublished['version_id'] > 0, 'Warnings prevented preview or activation.');
$advisoryRecord = $api('record', query: ['id' => $advisoryForm['id']]);
$assert((int) $advisoryRecord['form']['published_version_id'] === $advisoryPublished['version_id'], 'Warning response did not match active publication.');
echo "Native compiler advisories: preview and successful publication preserve accessibility/security warning codes and severity.\n";
$advisoryDraft['fields'][0]['validators'] = [['type' => 'confirmation', 'config' => ['fields' => [$advisoryField, 'c0885c8a-bc84-4684-8f5b-6af9de83b6cc']]]];
$scopedRevision = $api('save', ['id' => $advisoryForm['id'], 'revision' => $advisoryPublished['revision'], 'draft' => $advisoryDraft])['revision'];
$scopedRejected = $api('publish', ['id' => $advisoryForm['id'], 'revision' => $scopedRevision], expected: 422);
$assert(array_column($scopedRejected['diagnostics'], 'code') === ['validator.reference'] && array_column($scopedRejected['diagnostics'], 'path') === ['/fields/0/validators/0'], 'Field validator error lost its original location.');
$scopedRecord = $api('record', query: ['id' => $advisoryForm['id']]);
$assert((int) $scopedRecord['form']['published_version_id'] === $advisoryPublished['version_id'], 'Invalid field validator replaced the active version.');
file_put_contents($root . '/build/native-advisory-fixture.json', json_encode(['form_id' => $advisoryForm['id'], 'field' => $advisoryField], JSON_THROW_ON_ERROR));
echo "Native field validator: missing reference rejected with owning-field path and active version preserved.\n";
$comparisonField = 'c0885c8a-bc84-4684-8f5b-6af9de83b6cc';
$advisoryDraft['elements'][] = ['uuid' => $comparisonField, 'type' => 'field'];
$advisoryDraft['fields'][] = ['uuid' => $comparisonField, 'name' => 'confirmation', 'type' => 'integer', 'config' => ['label' => 'Confirmation']];
$comparisonRevision = $api('save', ['id' => $advisoryForm['id'], 'revision' => $scopedRevision, 'draft' => $advisoryDraft])['revision'];
$comparisonRejected = $api('publish', ['id' => $advisoryForm['id'], 'revision' => $comparisonRevision], expected: 422);
$assert(array_column($comparisonRejected['diagnostics'], 'code') === ['validator.compatibility'] && array_column($comparisonRejected['diagnostics'], 'path') === ['/fields/0/validators/0'], 'Incompatible comparison did not fail at its owning validator.');
$comparisonRecord = $api('record', query: ['id' => $advisoryForm['id']]);
$assert((int) $comparisonRecord['form']['published_version_id'] === $advisoryPublished['version_id'], 'Incompatible comparison replaced the active version.');
$advisoryDraft['fields'][1]['type'] = 'text';
$correctedRevision = $api('save', ['id' => $advisoryForm['id'], 'revision' => $comparisonRevision, 'draft' => $advisoryDraft])['revision'];
$correctedComparison = $api('publish', ['id' => $advisoryForm['id'], 'revision' => $correctedRevision]);
$assert($correctedComparison['version_id'] > $advisoryPublished['version_id'], 'Correcting the comparison did not permit publication.');
echo "Native validator compatibility: mixed text/numeric confirmation blocks activation; matching types publish after correction.\n";
