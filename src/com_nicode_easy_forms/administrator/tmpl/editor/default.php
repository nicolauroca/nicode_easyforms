<?php
declare(strict_types=1);
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$data = $this->data;
$tabs = ['fields' => 'COM_NICODE_EASY_FORMS_TAB_FIELDS', 'logic' => 'COM_NICODE_EASY_FORMS_LOGIC', 'validation' => 'COM_NICODE_EASY_FORMS_TAB_VALIDATION', 'actions' => 'COM_NICODE_EASY_FORMS_ACTIONS', 'privacy' => 'COM_NICODE_EASY_FORMS_DATA_PRIVACY', 'security' => 'COM_NICODE_EASY_FORMS_SECURITY', 'confirmation' => 'COM_NICODE_EASY_FORMS_AFTER_SUBMIT', 'translations' => 'COM_NICODE_EASY_FORMS_TRANSLATIONS'];
if ($data['canSettings'] || $data['canPublish']) { $tabs['publication'] = 'COM_NICODE_EASY_FORMS_PUBLICATION'; }
if ($data['canPermissions']) { $tabs['permissions'] = 'COM_NICODE_EASY_FORMS_PERMISSIONS'; }
$tabs += ['preview' => 'JGLOBAL_PREVIEW', 'versions' => 'COM_NICODE_EASY_FORMS_VERSIONS'];
$panel = static function (string $name, string $attributes = ''): void {
    echo '<section id="nef-panel-' . $name . '" data-nef-tab-panel="' . $name . '" role="tabpanel" aria-labelledby="nef-tab-' . $name . '" tabindex="0"' . ($name === 'fields' ? '' : ' hidden') . ' ' . $attributes . '>';
};
?>
<section class="nef-admin" data-nef-admin data-nef-editor data-csrf="<?= $escape($data['csrf']) ?>">
  <h1 data-nef-form-title><?= $escape($data['form']['name']) ?></h1>
  <p><strong data-nef-form-state><?= Text::_('COM_NICODE_EASY_FORMS_STATE_' . strtoupper($data['form']['state'])) ?></strong> · <?= Text::_('COM_NICODE_EASY_FORMS_ID') ?> <?= (int) $data['form']['id'] ?></p>
  <p role="status" data-nef-status aria-live="polite"></p>

  <section data-nef-diagnostics hidden aria-live="polite"><h2><?= Text::_('COM_NICODE_EASY_FORMS_DIAGNOSTICS') ?></h2><ul></ul></section>
  <div class="nef-editor-tabs" role="tablist" aria-label="<?= $escape(Text::_('COM_NICODE_EASY_FORMS_EDITOR_SECTIONS')) ?>">
    <?php foreach ($tabs as $name => $label): ?><button type="button" role="tab" id="nef-tab-<?= $name ?>" data-nef-tab="<?= $name ?>" aria-controls="nef-panel-<?= $name ?>" aria-selected="<?= $name === 'fields' ? 'true' : 'false' ?>" tabindex="<?= $name === 'fields' ? '0' : '-1' ?>"><?= Text::_($label) ?></button><?php endforeach ?>
  </div>
  <?php $panel('fields'); ?>
  <label><?= Text::_('COM_NICODE_EASY_FORMS_NAME') ?><input data-nef-name required maxlength="255" value="<?= $escape($data['draft']['name']) ?>"></label>
  <p><?= Text::_('COM_NICODE_EASY_FORMS_DRAFT_HELP') ?></p>
  <div class="nef-builder">
    <section aria-labelledby="nef-palette-title"><h2 id="nef-palette-title"><?= Text::_('COM_NICODE_EASY_FORMS_PALETTE') ?></h2><div data-nef-palette></div></section>
    <section aria-labelledby="nef-tree-title"><h2 id="nef-tree-title"><?= Text::_('COM_NICODE_EASY_FORMS_STRUCTURE') ?></h2><div data-nef-tree></div></section>
    <section aria-labelledby="nef-inspector-title"><h2 id="nef-inspector-title"><?= Text::_('COM_NICODE_EASY_FORMS_PROPERTIES') ?></h2><div data-nef-inspector></div></section>
  </div>
  </section>
  <?php $panel('logic', 'data-nef-logic-panel'); ?><h2><?= Text::_('COM_NICODE_EASY_FORMS_LOGIC') ?></h2><div data-nef-logic></div></section>
  <?php $panel('validation', 'data-nef-validator-panel'); ?><h2><?= Text::_('COM_NICODE_EASY_FORMS_CROSS_VALIDATION') ?></h2><div data-nef-validators></div></section>
  <?php $panel('actions', 'data-nef-actions-panel'); ?><h2><?= Text::_('COM_NICODE_EASY_FORMS_ACTIONS') ?></h2><div data-nef-actions></div></section>
  <?php $panel('privacy', ''); ?><h2><?= Text::_('COM_NICODE_EASY_FORMS_DATA_PRIVACY') ?></h2><div data-nef-privacy></div></section>
  <?php $panel('security', ''); ?><h2><?= Text::_('COM_NICODE_EASY_FORMS_SECURITY') ?></h2><div data-nef-security></div></section>
  <?php $panel('confirmation', 'data-nef-conditional-panel'); ?>
    <h2><?= Text::_('COM_NICODE_EASY_FORMS_AFTER_SUBMIT') ?></h2><div data-nef-after-submit></div>
    <h2><?= Text::_('COM_NICODE_EASY_FORMS_CONDITIONAL_MESSAGES') ?></h2><div data-nef-conditional-messages></div>
  </section>
  <?php $panel('translations', ''); ?><h2><?= Text::_('COM_NICODE_EASY_FORMS_TRANSLATIONS') ?></h2><div data-nef-translations></div></section>
  <?php if ($data['canSettings'] || $data['canPublish']): $panel('publication'); ?>
  <h2><?= Text::_('COM_NICODE_EASY_FORMS_PUBLICATION') ?></h2>
  <?php if ($data['canPublish']): ?><label><?= Text::_('COM_NICODE_EASY_FORMS_VERSION_COMMENT') ?><input data-nef-publish-comment maxlength="4000"></label><?php endif ?>
  <?php if ($data['canSettings']): ?>
    <p><?= Text::_('COM_NICODE_EASY_FORMS_PUBLICATION_HELP') ?></p>
    <label><?= Text::_('COM_NICODE_EASY_FORMS_ALIAS') ?><input data-nef-setting="alias" required maxlength="255" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" value="<?= $escape($data['form']['alias']) ?>"></label>
    <label><?= Text::_('COM_NICODE_EASY_FORMS_ACCESS') ?><select data-nef-setting="access"><?php foreach ($data['accessLevels'] as $level): ?><option value="<?= (int) $level['id'] ?>" <?= (int) $level['id'] === (int) $data['form']['access'] ? 'selected' : '' ?>><?= $escape($level['title']) ?></option><?php endforeach ?></select></label>
    <label><?= Text::_('COM_NICODE_EASY_FORMS_LANGUAGE') ?><select data-nef-setting="language"><option value="*" <?= $data['form']['language'] === '*' ? 'selected' : '' ?>><?= Text::_('JALL') ?></option><?php foreach ($data['languages'] as $language): ?><option value="<?= $escape($language['lang_code']) ?>" <?= $language['lang_code'] === $data['form']['language'] ? 'selected' : '' ?>><?= $escape($language['title']) ?></option><?php endforeach ?></select></label>
    <?php foreach (['publish_up', 'publish_down'] as $key): ?>
    <label><?= Text::_('COM_NICODE_EASY_FORMS_' . strtoupper($key)) ?><input type="datetime-local" step="1" data-nef-setting="<?= $key ?>" value="<?= $escape($data['form'][$key] === null ? '' : str_replace(' ', 'T', substr($data['form'][$key], 0, 19))) ?>"></label>
    <?php endforeach ?>
    <button type="button" class="btn btn-secondary" data-nef-command="settings"><?= Text::_('COM_NICODE_EASY_FORMS_APPLY_SETTINGS') ?></button>
  <?php endif ?>
  </section>
  <?php endif ?>
  <?php if ($data['canPermissions']): $panel('permissions'); ?>
  <h2><?= Text::_('COM_NICODE_EASY_FORMS_PERMISSIONS') ?></h2>
    <p><?= Text::_('COM_NICODE_EASY_FORMS_PERMISSIONS_HELP') ?></p>
    <div data-nef-permissions><button type="button" class="btn btn-secondary" data-nef-command="permissions"><?= Text::_('COM_NICODE_EASY_FORMS_LOAD_PERMISSIONS') ?></button></div>
  </section>
  <?php endif ?>

  <?php $panel('versions', 'data-nef-versions'); ?><h2><?= Text::_('COM_NICODE_EASY_FORMS_VERSIONS') ?></h2><button type="button" class="btn btn-secondary" data-nef-command="history"><?= Text::_('COM_NICODE_EASY_FORMS_REFRESH') ?></button><div></div></section>
  <?php $panel('preview', 'data-nef-preview'); ?>
    <h2 class="visually-hidden"><?= Text::_('JGLOBAL_PREVIEW') ?></h2><p><?= Text::_('COM_NICODE_EASY_FORMS_PREVIEW_HELP') ?></p>
    <p data-nef-preview-empty><?= Text::_('COM_NICODE_EASY_FORMS_PREVIEW_EMPTY') ?></p>
    <div class="nef-preview-tools">
    <label><?= Text::_('COM_NICODE_EASY_FORMS_PREVIEW_VIEWPORT') ?>
      <select data-nef-preview-viewport>
        <option value="1280"><?= Text::_('COM_NICODE_EASY_FORMS_PREVIEW_DESKTOP') ?> (1280 px)</option>
        <option value="800"><?= Text::_('COM_NICODE_EASY_FORMS_PREVIEW_TABLET') ?> (800 px)</option>
        <option value="390"><?= Text::_('COM_NICODE_EASY_FORMS_PREVIEW_MOBILE') ?> (390 px)</option>
      </select>
    </label>
    <button type="button" class="btn btn-secondary" data-nef-command="preview"><?= Text::_('COM_NICODE_EASY_FORMS_REFRESH') ?></button>
    </div>
    <div class="nef-preview-viewport" hidden><iframe width="1280" sandbox="allow-same-origin" title="<?= Text::_('JGLOBAL_PREVIEW') ?>"></iframe></div>
  </section>
  <script type="application/json" data-nef-editor-data><?= json_encode($data, JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
</section>
