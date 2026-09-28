<?php
declare(strict_types=1);
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$data = $this->data;
?>
<section class="nef-admin" data-csrf="<?= $escape($data['csrf']) ?>">
  <h1><?= Text::_('COM_NICODE_EASY_FORMS_JOBS') ?></h1>
  <p><a href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=submissions', false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_SUBMISSIONS') ?></a></p>
  <p><?= Text::_('COM_NICODE_EASY_FORMS_JOBS_HELP') ?></p>
  <?php if ($data['can_run']): ?><button type="button" class="btn btn-primary" data-nef-job-tick><?= Text::_('COM_NICODE_EASY_FORMS_JOB_RUN') ?></button><?php endif ?>
  <a class="btn btn-secondary" href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=jobs', false)) ?>"><?= Text::_('COM_NICODE_EASY_FORMS_REFRESH') ?></a>
  <p role="status" aria-live="polite" data-nef-job-status></p>
  <div class="nef-admin-table"><table class="table"><caption><?= Text::_('COM_NICODE_EASY_FORMS_JOBS') ?></caption><thead><tr>
  <?php foreach (['ID', 'TYPE', 'FORM', 'STATE', 'PROCESSED', 'FAILED', 'CREATED', 'ACTIONS'] as $key): ?><th scope="col"><?= Text::_('COM_NICODE_EASY_FORMS_' . $key) ?></th><?php endforeach ?>
  </tr></thead><tbody><?php foreach ($data['rows'] as $row): ?><tr>
    <th scope="row"><?= (int) $row['id'] ?></th>
    <?php foreach (['job_type', 'form_id', 'state', 'processed', 'failed', 'created_at'] as $key): ?><td><?= $escape($row[$key]) ?><?php if ($key === 'state' && $row['result_code']): ?><br><small><?= $escape($row['result_code']) ?></small><?php endif ?></td><?php endforeach ?>
    <td><?php if (!in_array($row['job_type'], ['form-delete', 'package-purge'], true) && in_array($row['state'], ['pending', 'running', 'retryable'], true)): ?><button type="button" class="btn btn-secondary" data-nef-job-cancel="<?= (int) $row['id'] ?>"><?= Text::_('JCANCEL') ?></button><?php endif ?>
    <?php if (in_array($row['job_type'], ['export-csv', 'export-json'], true) && $row['state'] === 'completed' && $row['expires_at'] > gmdate('Y-m-d H:i:s')): ?><form method="post" action="index.php?option=com_nicode_easy_forms&amp;task=job.download"><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><input type="hidden" name="<?= $escape($data['csrf']) ?>" value="1"><button class="btn btn-secondary" type="submit"><?= Text::_('COM_NICODE_EASY_FORMS_DOWNLOAD') ?></button><small><?= $escape($row['expires_at']) ?> UTC</small></form><?php endif ?></td>
  </tr><?php endforeach ?></tbody></table></div>
  <?php if ($data['next_before'] !== null): ?><a rel="next" href="<?= $escape(Route::_('index.php?option=com_nicode_easy_forms&view=jobs&before=' . $data['next_before'], false)) ?>"><?= Text::_('JNEXT') ?></a><?php endif ?>
</section>
