<?php
declare(strict_types=1);
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
$escape = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$data = $this->data; $id = (int) $data['resource']['id'];
?>
<section class="nef-admin" data-nef-resources data-nef-optionset data-csrf="<?= $escape($data['csrf']) ?>">
  <a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=resources', false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_OPTION_SETS') ?></a>
  <h1><?= $escape($data['snapshot']['name']) ?></h1>
  <p><?= Text::sprintf('COM_NICODE_EASY_FORMS_RESOURCE_REVISION_HELP', (int) $data['snapshot']['revision'], (int) $data['resource']['revision']) ?></p>
  <p><?= Text::_('COM_NICODE_EASY_FORMS_OPTION_SETS_HELP') ?></p>
  <p role="status" aria-live="polite" data-nef-resource-status></p>
  <form data-nef-resource-edit><fieldset <?= $data['can_edit'] ? '' : 'disabled' ?>>
    <legend><?= Text::_('COM_NICODE_EASY_FORMS_OPTIONS') ?></legend>
    <label><?= Text::_('COM_NICODE_EASY_FORMS_NAME') ?><input name="name" required maxlength="255" value="<?= $escape($data['snapshot']['name']) ?>"></label>
    <div data-nef-option-rows></div>
    <?php if ($data['can_edit']): ?><button type="button" class="btn btn-secondary" data-nef-option-add><?= Text::_('COM_NICODE_EASY_FORMS_ADD_OPTION') ?></button>
    <button type="submit" class="btn btn-primary"><?= Text::_('COM_NICODE_EASY_FORMS_RESOURCE_SAVE') ?></button><?php endif ?>
  </fieldset></form>
  <h2><?= Text::_('COM_NICODE_EASY_FORMS_VERSIONS') ?></h2>
  <a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=optionset&id=' . $id, false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_RESOURCE_CURRENT') ?></a>
  <ul><?php foreach ($data['history']['rows'] as $version): ?><li><a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=optionset&id=' . $id . '&revision=' . (int) $version['revision'], false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_RESOURCE_REVISION') ?> <?= (int) $version['revision'] ?></a> · <?= $escape($version['created_at']) ?> UTC</li><?php endforeach ?></ul>
  <?php if ($data['history']['next_before'] !== null): ?><a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=optionset&id=' . $id . '&revision=' . (int) $data['snapshot']['revision'] . '&before_revision=' . $data['history']['next_before'], false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_NEXT') ?></a><?php endif ?>
  <script type="application/json" data-nef-resource-data><?= json_encode(['resource' => $data['resource'], 'snapshot' => $data['snapshot'], 'can_edit' => $data['can_edit']], JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
</section>
