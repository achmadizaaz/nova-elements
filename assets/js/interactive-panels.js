(() => {
  let sequence = 0;

  function panelId() {
    let id;
    do {
      id = 'nova-panel-' + ++sequence;
    } while (document.getElementById(id));
    return id;
  }

  function adjacentIndex(key, index, count, previous, next) {
    if (key === 'Home') return 0;
    if (key === 'End') return count - 1;
    if (key === previous) return (index - 1 + count) % count;
    if (key === next) return (index + 1) % count;
    return -1;
  }

  class NovaAccordion extends HTMLElement {
    connectedCallback() {
      if (this.initialized) return;
      const details = Array.from(this.querySelectorAll(':scope > details'));
      if (!details.length) return;
      this.initialized = true;
      const summaries = details.map((panel) => panel.firstElementChild);

      if (!this.hasAttribute('data-multiple')) {
        const group = panelId();
        details.forEach((panel) => { panel.name = group; });
        if (!('name' in HTMLDetailsElement.prototype)) {
          this.addEventListener('toggle', (event) => {
            if (!event.target.open || !details.includes(event.target)) return;
            details.forEach((panel) => { if (panel !== event.target) panel.open = false; });
          }, true);
        }
      }

      this.addEventListener('keydown', (event) => {
        const index = summaries.indexOf(event.target);
        if (index < 0) return;
        const next = adjacentIndex(event.key, index, summaries.length, 'ArrowUp', 'ArrowDown');
        if (next < 0) return;
        event.preventDefault();
        summaries[next].focus();
      });
    }
  }

  class NovaTabs extends HTMLElement {
    connectedCallback() {
      if (!this.observer) this.initialize();
      if (this.observer) {
        this.updateOrientation();
        this.observer.observe(this);
      }
    }

    disconnectedCallback() {
      this.observer?.disconnect();
    }

    initialize() {
      this.list = this.querySelector('.nova-tabs__list');
      this.buttons = Array.from(this.list?.children ?? []);
      this.panels = Array.from(this.querySelectorAll('.nova-tabs__panels > .nova-tabs__panel'));
      if (!this.buttons.length || this.buttons.length !== this.panels.length) return;

      this.list.setAttribute('role', 'tablist');
      this.buttons.forEach((button, index) => {
        const panel = this.panels[index];
        button.id = panelId();
        panel.id = panelId();
        button.setAttribute('role', 'tab');
        button.setAttribute('aria-controls', panel.id);
        panel.setAttribute('role', 'tabpanel');
        panel.setAttribute('aria-labelledby', button.id);
        panel.tabIndex = 0;
        panel.querySelector('.nova-tabs__fallback-label').hidden = true;
      });
      this.activate(Number(this.dataset.initial) - 1);
      this.list.hidden = false;
      this.list.addEventListener('click', (event) => this.activate(this.buttons.indexOf(event.target)));
      this.list.addEventListener('focusin', (event) => this.activate(this.buttons.indexOf(event.target)));
      this.list.addEventListener('keydown', (event) => {
        const index = this.buttons.indexOf(event.target);
        if (index < 0) return;
        const vertical = this.list.getAttribute('aria-orientation') === 'vertical';
        const rtl = getComputedStyle(this.list).direction === 'rtl';
        const previous = vertical ? 'ArrowUp' : rtl ? 'ArrowRight' : 'ArrowLeft';
        const next = vertical ? 'ArrowDown' : rtl ? 'ArrowLeft' : 'ArrowRight';
        const target = adjacentIndex(event.key, index, this.buttons.length, previous, next);
        if (target < 0) return;
        event.preventDefault();
        this.buttons[target].focus();
      });
      this.observer = new ResizeObserver(() => this.updateOrientation());
    }

    activate(index) {
      if (!this.buttons[index]) return;
      this.buttons.forEach((button, position) => {
        const active = position === index;
        button.setAttribute('aria-selected', String(active));
        button.tabIndex = active ? 0 : -1;
        this.panels[position].hidden = !active;
      });
    }

    updateOrientation() {
      const vertical = getComputedStyle(this.list).flexDirection === 'column';
      this.list.setAttribute('aria-orientation', vertical ? 'vertical' : 'horizontal');
    }
  }

  class NovaModal extends HTMLElement {
    connectedCallback() {
      if (this.initialized) return;
      const trigger = this.querySelector('.nova-modal__open');
      const dialog = this.querySelector('.nova-modal__dialog');
      const content = this.querySelector('.nova-modal__content');
      const close = dialog?.querySelector('.nova-modal__close');
      if (!trigger || !content || !close || typeof dialog.showModal !== 'function') return;
      this.initialized = true;
      close.after(content);
      trigger.hidden = false;
      const action = content.querySelector('.nova-modal__action');
      let restoreFocus = true;
      trigger.addEventListener('click', () => {
        if (dialog.open) return;
        restoreFocus = true;
        dialog.showModal();
      });
      close.addEventListener('click', () => dialog.close());
      action?.addEventListener('click', (event) => {
        if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        restoreFocus = false;
        dialog.close();
      });
      dialog.addEventListener('close', () => {
        if (restoreFocus && !dialog.open && trigger.isConnected) trigger.focus({ preventScroll: true });
      });
      dialog.addEventListener('keydown', (event) => {
        if (event.key !== 'Tab') return;
        const last = action ?? close;
        if (document.activeElement !== (event.shiftKey ? close : last)) return;
        event.preventDefault();
        (event.shiftKey ? last : close).focus();
      });
    }
  }

  class NovaTooltip extends HTMLElement {
    connectedCallback() {
      if (this.initialized) return;
      const trigger = this.querySelector('.nova-tooltip__trigger');
      const bubble = this.querySelector('.nova-tooltip__bubble');
      if (!trigger || !bubble) return;
      this.initialized = true;
      bubble.id = panelId();
      bubble.setAttribute('role', 'tooltip');
      trigger.setAttribute('aria-describedby', bubble.id);
      trigger.hidden = false;
      bubble.hidden = true;
      let pinned = false;
      let hovered = false;
      const show = () => { bubble.hidden = false; };
      const hide = () => { bubble.hidden = true; pinned = false; };
      trigger.addEventListener('focus', show);
      trigger.addEventListener('blur', () => { if (!hovered && !pinned) hide(); });
      trigger.addEventListener('pointerenter', (event) => {
        if (event.pointerType === 'touch') return;
        hovered = true;
        show();
      });
      this.addEventListener('pointerleave', () => {
        hovered = false;
        if (!pinned && document.activeElement !== trigger) hide();
      });
      trigger.addEventListener('click', () => {
        if (pinned) hide();
        else { pinned = true; show(); }
      });
      this.dismiss = hide;
    }
  }

  class NovaHotspot extends HTMLElement {
    connectedCallback() {
      if (this.initialized) return;
      const markers = Array.from(this.querySelectorAll('.nova-hotspot__marker'));
      const panels = Array.from(this.querySelectorAll('.nova-hotspot__panel'));
      if (!markers.length || markers.length !== panels.length) return;
      this.initialized = true;
      this.classList.add('nova-hotspot--ready');
      const reveal = (active) => markers.forEach((marker, index) => {
        marker.setAttribute('aria-expanded', String(index === active));
        panels[index].hidden = index !== active;
      });
      markers.forEach((marker, index) => {
        const panel = panels[index];
        panel.id = panelId();
        marker.setAttribute('aria-controls', panel.id);
        marker.hidden = false;
        marker.addEventListener('click', () => reveal(panel.hidden ? index : -1));
        marker.addEventListener('keydown', (event) => {
          if (event.key === 'Escape' && !panel.hidden) {
            event.preventDefault();
            reveal(-1);
          }
        });
      });
      reveal(0);
    }
  }

  class NovaComparison extends HTMLElement {
    connectedCallback() {
      if (this.initialized) return;
      const range = this.querySelector('.nova-comparison__range');
      if (!range) return;
      this.initialized = true;
      const update = () => this.style.setProperty('--ne-compare', range.value + '%');
      update();
      this.classList.add('nova-comparison--ready');
      range.hidden = false;
      this.querySelector('.nova-comparison__divider').hidden = false;
      this.querySelector('.nova-comparison__labels').hidden = false;
      range.addEventListener('input', update);
    }
  }

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') document.querySelectorAll('nova-tooltip').forEach((tooltip) => tooltip.dismiss?.());
  });

  for (const [name, element] of Object.entries({
    'nova-accordion': NovaAccordion,
    'nova-tabs': NovaTabs,
    'nova-modal': NovaModal,
    'nova-tooltip': NovaTooltip,
    'nova-hotspot': NovaHotspot,
    'nova-comparison': NovaComparison,
  })) {
    if (!customElements.get(name)) customElements.define(name, element);
  }
})();
