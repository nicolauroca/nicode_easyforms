const usable = node => node && !node.disabled && node.type !== 'hidden' && !node.closest('[hidden], [inert]');

// previous must be captured inside this form before rules change its DOM state.
export function recoverRuleFocus(form, previous) {
  if (!previous || (previous.isConnected && usable(previous))) return;
  const current = form.ownerDocument.activeElement;
  if (current !== previous && form.contains(current) && usable(current)) return;
  const candidates = [...form.querySelectorAll('[data-nef-input], [data-nef-next], [data-nef-submit], [data-nef-previous]')].filter(usable);
  const following = previous.isConnected ? candidates.find(node => previous.compareDocumentPosition(node) & 4) : candidates[0];
  const target = following || candidates.at(-1) || form.querySelector('.nef-result');
  target?.focus();
}
