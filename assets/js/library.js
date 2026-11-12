(() => {
  const library = document.querySelector('.nova-library');
  const dialog = library?.querySelector('dialog');
  if (!dialog || typeof dialog.showModal !== 'function') return;

  const image = dialog.querySelector('[data-nova-dialog-image]');
  const devices = dialog.querySelector('[data-nova-devices]');
  const importArea = dialog.querySelector('[data-nova-dialog-import]');
  let trigger;

  function showDevice(device) {
    const sources = {desktop: trigger.dataset.novaPreview, tablet: trigger.dataset.novaTablet, mobile: trigger.dataset.novaMobile};
    image.src = sources[device];
    image.dataset.device = device;
    for (const button of devices.querySelectorAll('[data-nova-device]')) {
      button.hidden = !sources[button.dataset.novaDevice];
      button.setAttribute('aria-pressed', String(button.dataset.novaDevice === device));
    }
  }

  devices.addEventListener('click', (event) => {
    const button = event.target.closest('[data-nova-device]');
    if (button && trigger) showDevice(button.dataset.novaDevice);
  });

  for (const button of library.querySelectorAll('[data-nova-preview]')) {
    button.hidden = false;
    button.addEventListener('click', () => {
      const card = button.closest('[data-nova-card]');
      trigger = button;
      for (const field of ['meta', 'description', 'requirements', 'problem']) {
        const target = dialog.querySelector('[data-nova-dialog-' + field + ']');
        target.textContent = card.querySelector('[data-nova-' + field + ']')?.textContent.trim() || '';
        target.hidden = !target.textContent;
      }
      dialog.querySelector('#nova-library-preview-title').textContent = card.querySelector('[data-nova-title]').textContent;
      devices.hidden = !button.dataset.novaTablet && !button.dataset.novaMobile;
      importArea.replaceChildren(card.querySelector('[data-nova-import]').cloneNode(true));
      showDevice('desktop');
      dialog.showModal();
    });
  }

  dialog.addEventListener('close', () => {
    image.removeAttribute('src');
    importArea.replaceChildren();
    trigger?.focus();
    trigger = undefined;
  });
})();
