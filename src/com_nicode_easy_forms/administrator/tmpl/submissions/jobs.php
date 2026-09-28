<?php
declare(strict_types=1);
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
$capabilities = $data['job_capabilities']; $formId = (int) ($filters['form_id'] ?? 0);
?>
<p><a href="index.php?option=com_nicode_easy_forms&amp;view=jobs"><?= Text::_('COM_NICODE_EASY_FORMS_JOBS') ?></a></p>
<?php if ($formId > 0): ?>
<details data-nef-job-controls><summary><?= Text::_('COM_NICODE_EASY_FORMS_BULK_ACTIONS') ?></summary>
  <p><?= Text::_('COM_NICODE_EASY_FORMS_BULK_HELP') ?></p>
  <script type="application/json" data-nef-job-query><?= json_encode(['form_id' => $formId, 'query' => ['filters' => $filters, 'fields' => $data['field_filters'], 'sort' => $data['sort']]], JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
  <?php if (($capabilities['easyforms.submissions.export'] ?? false) && $data['export_available']): ?>
  <?php foreach (['csv', 'json'] as $exportFormat): ?>
  <form data-nef-enqueue="export-<?= $exportFormat ?>"><fieldset><legend><?= Text::_('COM_NICODE_EASY_FORMS_EXPORT_' . strtoupper($exportFormat)) ?></legend>
    <?php foreach ($data['export_fields'] as $field): ?><label><input type="checkbox" name="fields[]" value="<?= $escape($field['uuid']) ?>" <?= !$field['sensitive'] ? 'checked' : '' ?>><?= $escape($field['label']) ?><?= $field['sensitive'] ? ' (' . Text::_('COM_NICODE_EASY_FORMS_SENSITIVE') . ')' : '' ?></label><?php endforeach ?>
    <?php if ($capabilities['easyforms.submissions.view_sensitive'] ?? false): ?><label><input type="checkbox" name="include_sensitive"><?= Text::_('COM_NICODE_EASY_FORMS_EXPORT_SENSITIVE') ?></label><?php endif ?>
    <button type="submit" class="btn btn-secondary"><?= Text::_('COM_NICODE_EASY_FORMS_EXPORT_' . strtoupper($exportFormat)) ?></button>
  </fieldset></form><?php endforeach; endif ?>
  <?php if ($capabilities['easyforms.submissions.manage'] ?? false): ?><form data-nef-enqueue="submission-bulk" data-operation="state"><label><?= Text::_('COM_NICODE_EASY_FORMS_STATE') ?><select name="state" aria-label="<?= $escape(Text::_('COM_NICODE_EASY_FORMS_STATE')) ?>"><?php foreach (\Nicode\EasyForms\Application\SubmissionAdministration::STATES as $state): ?><option value="<?= $state ?>"><?= Text::_('COM_NICODE_EASY_FORMS_RESPONSE_' . strtoupper($state)) ?></option><?php endforeach ?></select></label><button type="submit" class="btn btn-secondary"><?= Text::_('COM_NICODE_EASY_FORMS_CHANGE_STATE') ?></button></form><?php endif ?>
  <?php foreach (['anonymize', 'delete'] as $operation): if ($capabilities['easyforms.submissions.' . $operation] ?? false): ?><form data-nef-enqueue="submission-bulk" data-operation="<?= $operation ?>"><button type="submit" class="btn btn-danger"><?= Text::_('COM_NICODE_EASY_FORMS_BULK_' . strtoupper($operation)) ?></button></form><?php endif; endforeach ?>
  <?php if ($capabilities['easyforms.submissions.reindex'] ?? false): ?>
  <form data-nef-enqueue="reindex"><fieldset><legend><?= Text::_('COM_NICODE_EASY_FORMS_REINDEX') ?></legend>
    <p><?= Text::_('COM_NICODE_EASY_FORMS_REINDEX_SCOPE_HELP') ?></p>
    <label><?= Text::_('COM_NICODE_EASY_FORMS_ID') ?><input type="number" min="1" step="1" name="submission_id"></label>
    <label><?= Text::_('COM_NICODE_EASY_FORMS_RECEIVED_FROM') ?><input type="text" name="received_from" placeholder="YYYY-MM-DD HH:MM:SS"></label>
    <label><?= Text::_('COM_NICODE_EASY_FORMS_RECEIVED_TO') ?><input type="text" name="received_to" placeholder="YYYY-MM-DD HH:MM:SS"></label>
    <button type="submit" class="btn btn-secondary"><?= Text::_('COM_NICODE_EASY_FORMS_REINDEX') ?></button>
  </fieldset></form><?php endif ?>
  <p role="status" aria-live="polite" data-nef-job-status></p>
</details>
<?php elseif ($data['canReindexAll'] ?? false): ?>
<script type="application/json" data-nef-job-query>{"form_id":0}</script>
<form data-nef-enqueue="reindex" data-nef-all-forms="true"><button type="submit" class="btn btn-secondary"><?= Text::_('COM_NICODE_EASY_FORMS_REINDEX_ALL') ?></button></form>
<p role="status" aria-live="polite" data-nef-job-status></p>
<?php endif ?>
