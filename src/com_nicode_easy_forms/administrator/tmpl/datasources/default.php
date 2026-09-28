<?php
declare(strict_types=1);
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); $data = $this->data;
?>
<section class="nef-admin">
  <a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=resources', false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_RESOURCES') ?></a>
  <h1><?= Text::_('COM_NICODE_EASY_FORMS_DATA_SOURCES') ?></h1>
  <p><?= Text::_('COM_NICODE_EASY_FORMS_SOURCE_RESOURCE_HELP') ?></p>
  <table class="table"><caption><?= Text::_('COM_NICODE_EASY_FORMS_DATA_SOURCES') ?></caption><thead><tr><th scope="col"><?= Text::_('COM_NICODE_EASY_FORMS_NAME') ?></th><th scope="col"><?= Text::_('COM_NICODE_EASY_FORMS_SOURCE_PROVIDER') ?></th><th scope="col"><?= Text::_('COM_NICODE_EASY_FORMS_RESOURCE_REVISION') ?></th><th scope="col"><?= Text::_('COM_NICODE_EASY_FORMS_ENABLED') ?></th></tr></thead><tbody>
    <?php foreach ($data['rows'] as $row): ?><tr><th scope="row"><a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=datasource&id=' . (int) $row['id'], false)) ?>"><?= $escape($row['name']) ?></a></th><td><?= $escape($row['provider']) ?></td><td><?= (int) $row['revision'] ?></td><td><?= Text::_($row['enabled'] ? 'JYES' : 'JNO') ?></td></tr><?php endforeach ?>
  </tbody></table>
  <?php if ($data['next_before'] !== null): ?><a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=datasources&before=' . (int) $data['next_before'], false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_NEXT') ?></a><?php endif ?>
</section>
