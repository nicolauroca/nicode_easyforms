<?php
declare(strict_types=1);
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$data = $this->data;
?>
<section class="nef-admin" data-nef-purge data-csrf="<?= $escape($data['csrf']) ?>">
  <h1><?= Text::_('COM_NICODE_EASY_FORMS_PURGE_TITLE') ?></h1>
  <p><?= Text::_('COM_NICODE_EASY_FORMS_PURGE_HELP') ?></p>
  <p><strong><?= Text::_('COM_NICODE_EASY_FORMS_PURGE_STATE_' . strtoupper($data['state'])) ?></strong></p>
  <dl><?php foreach (['forms' => 'FORMS', 'submissions' => 'SUBMISSIONS', 'submission_files' => 'FILES', 'upload_staging' => 'STAGED_UPLOADS', 'bytes' => 'DELETE_BYTES'] as $key => $label): ?><dt><?= Text::_('COM_NICODE_EASY_FORMS_' . $label) ?></dt><dd><?= (int) $data[$key] ?></dd><?php endforeach ?></dl>
  <p role="status" aria-live="polite" data-nef-purge-status></p>
  <?php if ($data['state'] !== 'ready'): ?>
  <form data-nef-purge-confirm>
    <input type="hidden" name="confirmation" value="<?= $escape($data['confirmation']) ?>">
    <label><?= Text::_('COM_NICODE_EASY_FORMS_PURGE_PHRASE') ?><input name="phrase" required autocomplete="off" spellcheck="false" pattern="DELETE EASYFORMS DATA"></label>
    <button type="submit" class="btn btn-danger"><?= Text::_('COM_NICODE_EASY_FORMS_PURGE_PREPARE') ?></button>
  </form>
  <?php else: ?><p><?= Text::_('COM_NICODE_EASY_FORMS_PURGE_READY_HELP') ?></p><a class="btn btn-danger" href="<?= $escape(Route::_('index.php?option=com_installer&view=manage&filter[search]=Nicode', false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_PURGE_UNINSTALL') ?></a><?php endif ?>
  <p><a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=jobs', false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_JOBS') ?></a> · <a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=purge', false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_REFRESH') ?></a></p>
</section>
