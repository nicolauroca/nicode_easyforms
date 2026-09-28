<?php
declare(strict_types=1);
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
$escape = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$data = $this->data;
?>
<section class="nef-admin" data-nef-resources data-csrf="<?= $escape($data['csrf']) ?>">
  <a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=forms', false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_FORMS') ?></a>
  <h1><?= Text::_('COM_NICODE_EASY_FORMS_OPTION_SETS') ?></h1>
  <p><a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=datasources', false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_DATA_SOURCES') ?></a></p>
  <nav aria-label="<?= Text::_('COM_NICODE_EASY_FORMS_RESOURCES') ?>"><a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=templates&kind=email', false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_EMAIL_TEMPLATES') ?></a> · <a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=templates&kind=form', false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_FORM_TEMPLATES') ?></a></nav>
  <p><?= Text::_('COM_NICODE_EASY_FORMS_OPTION_SETS_HELP') ?></p>
  <p role="status" aria-live="polite" data-nef-resource-status></p>
  <?php if ($data['can_edit']): ?><form data-nef-resource-create>
    <label><?= Text::_('COM_NICODE_EASY_FORMS_NAME') ?><input name="name" required maxlength="255"></label>
    <button class="btn btn-primary" type="submit"><?= Text::_('COM_NICODE_EASY_FORMS_RESOURCE_CREATE') ?></button>
  </form><?php endif ?>
  <table class="table"><caption><?= Text::_('COM_NICODE_EASY_FORMS_OPTION_SETS') ?></caption><thead><tr><th scope="col"><?= Text::_('COM_NICODE_EASY_FORMS_NAME') ?></th><th scope="col"><?= Text::_('COM_NICODE_EASY_FORMS_RESOURCE_REVISION') ?></th></tr></thead><tbody>
    <?php foreach ($data['rows'] as $row): ?><tr><th scope="row"><a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=optionset&id=' . (int) $row['id'], false)) ?>"><?= $escape($row['name']) ?></a></th><td><?= (int) $row['revision'] ?></td></tr><?php endforeach ?>
  </tbody></table>
  <?php if ($data['next_before'] !== null): ?><a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=resources&before=' . $data['next_before'], false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_NEXT') ?></a><?php endif ?>
</section>
