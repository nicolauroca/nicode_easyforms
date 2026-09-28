import test from 'node:test';
import assert from 'node:assert/strict';
import {actionControls} from '../../src/com_nicode_easy_forms/media/js/admin-toolbar.js';

test('native toolbar actions share the editor scope without including unrelated Joomla controls', () => {
  const local = {matches: () => true};
  const native = {matches: selector => selector === '[data-nef-command]'};
  const otherAction = {matches: () => false};
  const root = {querySelectorAll: () => [local], ownerDocument: {querySelectorAll: selector => {
    assert.equal(selector, '[data-nef-toolbar]'); return [local, native, otherAction];
  }}};
  assert.deepEqual(actionControls(root, '[data-nef-command]'), [local, native]);
});
