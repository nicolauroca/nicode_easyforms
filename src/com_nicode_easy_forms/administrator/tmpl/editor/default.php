<?php
declare(strict_types=1);
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$data = $this->data;
?>
<section class="nef-admin" data-nef-admin data-nef-editor data-csrf="<?= $escape($data['csrf']) ?>">
  <a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=forms', false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_FORMS') ?></a>
  <h1 data-nef-form-title><?= $escape($data['form']['name']) ?></h1>
  <p><strong data-nef-form-state><?= Text::_('COM_NICODE_EASY_FORMS_STATE_' . strtoupper($data['form']['state'])) ?></strong> · <?= Text::_('COM_NICODE_EASY_FORMS_ID') ?> <?= (int) $data['form']['id'] ?></p>
  <p role="status" data-nef-status aria-live="polite"></p>
  <?php if ($data['canPublish']): ?><label><?= Text::_('COM_NICODE_EASY_FORMS_VERSION_COMMENT') ?><input data-nef-publish-comment maxlength="4000"></label><?php endif ?>
  <label><?= Text::_('COM_NICODE_EASY_FORMS_NAME') ?><input data-nef-name required maxlength="255" value="<?= $escape($data['draft']['name']) ?>"></label>
  <p><?= Text::_('COM_NICODE_EASY_FORMS_DRAFT_HELP') ?></p>
  <div class="nef-builder">
    <section aria-labelledby="nef-palette-title"><h2 id="nef-palette-title"><?= Text::_('COM_NICODE_EASY_FORMS_PALETTE') ?></h2><div data-nef-palette></div></section>
    <section aria-labelledby="nef-tree-title"><h2 id="nef-tree-title"><?= Text::_('COM_NICODE_EASY_FORMS_STRUCTURE') ?></h2><div data-nef-tree></div></section>
    <section aria-labelledby="nef-inspector-title"><h2 id="nef-inspector-title"><?= Text::_('COM_NICODE_EASY_FORMS_PROPERTIES') ?></h2><div data-nef-inspector></div></section>
  </div>
  <details data-nef-logic-panel><summary><?= Text::_('COM_NICODE_EASY_FORMS_LOGIC') ?></summary><div data-nef-logic></div></details>
  <details data-nef-validator-panel><summary><?= Text::_('COM_NICODE_EASY_FORMS_CROSS_VALIDATION') ?></summary><div data-nef-validators></div></details>
  <details data-nef-actions-panel><summary><?= Text::_('COM_NICODE_EASY_FORMS_ACTIONS') ?></summary><div data-nef-actions></div></details>
  <details><summary><?= Text::_('COM_NICODE_EASY_FORMS_DATA_PRIVACY') ?></summary><div data-nef-privacy></div></details>
  <details><summary><?= Text::_('COM_NICODE_EASY_FORMS_SECURITY') ?></summary><div data-nef-security></div></details>
  <details><summary><?= Text::_('COM_NICODE_EASY_FORMS_AFTER_SUBMIT') ?></summary><div data-nef-after-submit></div></details>
  <details data-nef-conditional-panel><summary><?= Text::_('COM_NICODE_EASY_FORMS_CONDITIONAL_MESSAGES') ?></summary><div data-nef-conditional-messages></div></details>
  <details><summary><?= Text::_('COM_NICODE_EASY_FORMS_TRANSLATIONS') ?></summary><div data-nef-translations></div></details>
  <?php if ($data['canSettings']): ?>
  <details><summary><?= Text::_('COM_NICODE_EASY_FORMS_PUBLICATION') ?></summary>
    <p><?= Text::_('COM_NICODE_EASY_FORMS_PUBLICATION_HELP') ?></p>
    <label><?= Text::_('COM_NICODE_EASY_FORMS_ALIAS') ?><input data-nef-setting="alias" required maxlength="255" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" value="<?= $escape($data['form']['alias']) ?>"></label>
    <label><?= Text::_('COM_NICODE_EASY_FORMS_ACCESS') ?><select data-nef-setting="access"><?php foreach ($data['accessLevels'] as $level): ?><option value="<?= (int) $level['id'] ?>" <?= (int) $level['id'] === (int) $data['form']['access'] ? 'selected' : '' ?>><?= $escape($level['title']) ?></option><?php endforeach ?></select></label>
    <label><?= Text::_('COM_NICODE_EASY_FORMS_LANGUAGE') ?><select data-nef-setting="language"><option value="*" <?= $data['form']['language'] === '*' ? 'selected' : '' ?>><?= Text::_('JALL') ?></option><?php foreach ($data['languages'] as $language): ?><option value="<?= $escape($language['lang_code']) ?>" <?= $language['lang_code'] === $data['form']['language'] ? 'selected' : '' ?>><?= $escape($language['title']) ?></option><?php endforeach ?></select></label>
    <?php foreach (['publish_up', 'publish_down'] as $key): ?>
    <label><?= Text::_('COM_NICODE_EASY_FORMS_' . strtoupper($key)) ?><input type="datetime-local" step="1" data-nef-setting="<?= $key ?>" value="<?= $escape($data['form'][$key] === null ? '' : str_replace(' ', 'T', substr($data['form'][$key], 0, 19))) ?>"></label>
    <?php endforeach ?>
    <button type="button" class="btn btn-secondary" data-nef-command="settings"><?= Text::_('COM_NICODE_EASY_FORMS_APPLY_SETTINGS') ?></button>
  </details>
  <?php endif ?>
  <?php if ($data['canPermissions']): ?>
  <details><summary><?= Text::_('COM_NICODE_EASY_FORMS_PERMISSIONS') ?></summary>
    <p><?= Text::_('COM_NICODE_EASY_FORMS_PERMISSIONS_HELP') ?></p>
    <div data-nef-permissions><button type="button" class="btn btn-secondary" data-nef-command="permissions"><?= Text::_('COM_NICODE_EASY_FORMS_LOAD_PERMISSIONS') ?></button></div>
  </details>
  <?php endif ?>
  <section data-nef-diagnostics hidden aria-live="polite"><h2><?= Text::_('COM_NICODE_EASY_FORMS_DIAGNOSTICS') ?></h2><ul></ul></section>
  <section data-nef-versions hidden><h2><?= Text::_('COM_NICODE_EASY_FORMS_VERSIONS') ?></h2><div></div></section>
  <section data-nef-preview hidden>
    <h2><?= Text::_('JGLOBAL_PREVIEW') ?></h2><p><?= Text::_('COM_NICODE_EASY_FORMS_PREVIEW_HELP') ?></p>
    <label><?= Text::_('COM_NICODE_EASY_FORMS_PREVIEW_VIEWPORT') ?>
      <select data-nef-preview-viewport>
        <option value="1280"><?= Text::_('COM_NICODE_EASY_FORMS_PREVIEW_DESKTOP') ?> (1280 px)</option>
        <option value="800"><?= Text::_('COM_NICODE_EASY_FORMS_PREVIEW_TABLET') ?> (800 px)</option>
        <option value="390"><?= Text::_('COM_NICODE_EASY_FORMS_PREVIEW_MOBILE') ?> (390 px)</option>
      </select>
    </label>
    <div class="nef-preview-viewport"><iframe width="1280" sandbox="allow-same-origin" title="<?= Text::_('JGLOBAL_PREVIEW') ?>"></iframe></div>
  </section>
  <script type="application/json" data-nef-editor-data><?= json_encode($data, JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
</section>
