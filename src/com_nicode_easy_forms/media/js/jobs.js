import {confirmAction} from './confirm-action.js';
const t = key => Joomla.Text._(`COM_NICODE_EASY_FORMS_${key.toUpperCase()}`);
for (const root of document.querySelectorAll('.nef-admin[data-csrf]')) {
  const status = root.querySelector('[data-nef-job-status]');
  const post = async (task, values) => {
    const body = new URLSearchParams({...values, [root.dataset.csrf]: '1'});
    const response = await fetch(`index.php?option=com_nicode_easy_forms&task=job.${task}`, {method: 'POST', body, credentials: 'same-origin', headers: {'Accept': 'application/json'}});
    const result = await response.json();
    if (!response.ok || !result.ok) throw new Error(t(result.error || 'unexpected_error'));
    return result.data;
  };
  let busy = false;
  const run = async action => {
    if (busy) return;
    busy = true; root.setAttribute('aria-busy', 'true');
    try { await action(); } catch (error) { if (status) status.textContent = error.message; }
    finally { busy = false; root.removeAttribute('aria-busy'); }
  };
  for (const form of root.querySelectorAll('[data-nef-enqueue]')) {
    form.addEventListener('submit', async event => {
      event.preventDefault();
      if (busy) return;
      const query = JSON.parse(root.querySelector('[data-nef-job-query]').textContent);
      const values = new FormData(form); const type = form.dataset.nefEnqueue;
      const operation = form.dataset.operation;
      if (type === 'submission-bulk' && !await confirmAction(t(`confirm_bulk_${operation}`))) return;
      if (['export-csv', 'export-json'].includes(type) && values.has('include_sensitive') && !await confirmAction(t('confirm_sensitive_export'))) return;
      run(async () => {
        const selection = {};
        if (type === 'reindex') {
          if (form.dataset.nefAllForms === 'true') selection.all_forms = true;
          for (const key of ['submission_id', 'received_from', 'received_to']) {
            const value = String(values.get(key) ?? '').trim();
            if (value) selection[key] = key === 'submission_id' && /^[1-9][0-9]*$/.test(value) && Number.isSafeInteger(Number(value)) ? Number(value) : value;
          }
        }
        await post('enqueue', {payload: JSON.stringify({...query, type, operation, state: values.get('state'), fields: values.getAll('fields[]'), include_sensitive: values.has('include_sensitive'), ...selection})});
        location.assign('index.php?option=com_nicode_easy_forms&view=jobs');
      });
    });
  }
  for (const button of root.querySelectorAll('[data-nef-job-cancel]')) button.addEventListener('click', () => run(async () => {
    await post('cancel', {id: button.dataset.nefJobCancel}); location.reload();
  }));
  root.querySelector('[data-nef-job-tick]')?.addEventListener('click', () => run(async () => {
    await post('tick', {}); location.reload();
  }));
}
