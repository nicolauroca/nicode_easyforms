<?php
declare(strict_types=1);
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$data = $this->data; $kind = $data['kind'];
?>
<section class="nef-admin" data-nef-templates data-csrf="<?= $escape($data['csrf']) ?>">
  <a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=resources', false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_RESOURCES') ?></a>
  <h1><?= Text::_($kind === 'email' ? 'COM_NICODE_EASY_FORMS_EMAIL_TEMPLATES' : 'COM_NICODE_EASY_FORMS_FORM_TEMPLATES') ?></h1>
  <nav><a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=templates&kind=' . ($kind === 'email' ? 'form' : 'email'), false)) ?>"><?= Text::_($kind === 'email' ? 'COM_NICODE_EASY_FORMS_FORM_TEMPLATES' : 'COM_NICODE_EASY_FORMS_EMAIL_TEMPLATES') ?></a></nav>
  <p><?= Text::_($kind === 'email' ? 'COM_NICODE_EASY_FORMS_EMAIL_TEMPLATE_HELP' : 'COM_NICODE_EASY_FORMS_FORM_TEMPLATE_HELP') ?></p>
  <p role="status" aria-live="polite" data-nef-template-status></p>
  <?php if ($kind === 'email' && $data['can_edit']): ?><form data-nef-email-template-create>
    <label><?= Text::_('COM_NICODE_EASY_FORMS_NAME') ?><input name="name" required maxlength="255"></label>
    <button type="submit" class="btn btn-primary"><?= Text::_('COM_NICODE_EASY_FORMS_TEMPLATE_CREATE_EMAIL') ?></button>
  </form><?php endif ?>
  <table class="table"><caption><?= Text::_($kind === 'email' ? 'COM_NICODE_EASY_FORMS_EMAIL_TEMPLATES' : 'COM_NICODE_EASY_FORMS_FORM_TEMPLATES') ?></caption><thead><tr><th scope="col"><?= Text::_('COM_NICODE_EASY_FORMS_NAME') ?></th><th scope="col"><?= Text::_('COM_NICODE_EASY_FORMS_RESOURCE_REVISION') ?></th><th scope="col"><?= Text::_('COM_NICODE_EASY_FORMS_ACTIONS') ?></th></tr></thead><tbody>
  <?php foreach ($data['rows'] as $row): ?><tr><th scope="row"><?= $escape($row['name']) ?></th><td><?= (int) $row['revision'] ?></td><td>
    <?php if ($kind === 'email'): ?><a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=emailtemplate&id=' . (int) $row['id'], false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_TEMPLATE_OPEN') ?></a>
    <?php elseif ($data['canCreate']): ?><button class="btn btn-secondary" type="button" data-nef-transfer="import" data-nef-template-id="<?= (int) $row['id'] ?>" data-nef-template-revision="<?= (int) $row['revision'] ?>" data-nef-template-name="<?= $escape($row['name']) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_TEMPLATE_CREATE_FORM') ?></button><?php endif ?>
  </td></tr><?php endforeach ?>
  </tbody></table>
  <?php if ($data['next_before'] !== null): ?><a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=templates&kind=' . $kind . '&before=' . $data['next_before'], false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_NEXT') ?></a><?php endif ?>
</section>
