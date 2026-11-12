(() => {
  if (customElements.get('nova-post-carousel')) return;

  class PostCarousel extends HTMLElement {
    connectedCallback() {
      queueMicrotask(() => {
        if (this.isConnected && !this.events) this.initialize();
      });
    }

    initialize() {
      this.viewport = this.querySelector('.nova-blog__grid');
      this.controls = this.querySelector('.nova-blog__carousel-controls');
      if (!this.viewport || !this.controls || this.viewport.children.length < 2) return;

      this.events = new AbortController();
      const options = { signal: this.events.signal };
      this.arrows = Array.from(this.controls.querySelectorAll('[data-step]'));
      this.picker = this.controls.querySelector('[data-label]');
      this.rotation = this.controls.querySelector('[data-rotation]');
      this.motion = matchMedia('(prefers-reduced-motion: reduce)');
      this.interval = Math.max(4000, Math.min(20000, Number(this.dataset.interval) || 6000));
      this.playing = Boolean(this.rotation && !this.motion.matches);
      this.rotationIntent = null;
      this.hovered = this.matches(':hover');
      this.visible = false;
      this.pages = [];

      this.controls.addEventListener('click', (event) => this.handleControl(event), options);
      this.viewport.addEventListener('scroll', () => {
        if (this.frame) return;
        this.frame = requestAnimationFrame(() => {
          this.frame = null;
          this.updateControls();
        });
      }, { ...options, passive: true });

      this.addEventListener('focusin', () => this.pause(), options);
      this.addEventListener('pointerdown', (event) => {
        this.rotationIntent = this.rotation?.contains(event.target) ? !this.playing : null;
        if (!this.controls.contains(event.target)) this.pause();
      }, options);
      this.addEventListener('pointerenter', (event) => {
        if (event.pointerType === 'touch') return;
        this.hovered = true;
        this.updateRotation();
      }, options);
      this.addEventListener('pointerleave', () => {
        this.hovered = false;
        this.updateRotation();
      }, options);
      this.motion.addEventListener('change', () => this.pause(), options);
      document.addEventListener('visibilitychange', () => this.updateRotation(), options);

      this.resize = new ResizeObserver(() => this.measure());
      this.resize.observe(this.viewport);
      this.intersection = new IntersectionObserver(([entry]) => {
        this.visible = entry.isIntersecting;
        this.updateRotation();
      });
      this.intersection.observe(this);
      this.measure();
    }

    disconnectedCallback() {
      this.events?.abort();
      this.events = null;
      this.resize?.disconnect();
      this.intersection?.disconnect();
      cancelAnimationFrame(this.frame);
      this.frame = null;
      clearInterval(this.timer);
      this.timer = null;
    }

    measure() {
      const viewport = this.viewport;
      const first = viewport.children[0].getBoundingClientRect();
      const second = viewport.children[1].getBoundingClientRect();
      const step = Math.abs(second.left - first.left);
      const end = viewport.scrollWidth - viewport.clientWidth;
      const pages = [0];

      if (step > 0 && end > 2) {
        const visible = Math.max(1, Math.floor((viewport.clientWidth + step - first.width + 1) / step));
        const stride = step * visible;
        for (let offset = stride; offset < end - 2; offset += stride) pages.push(offset);
        pages.push(end);
      }

      if (this.picker && (pages.length !== this.pages.length || !this.picker.childElementCount)) {
        const buttons = pages.length > 1 ? pages.map((_, index) => {
          const button = document.createElement('button');
          button.type = 'button';
          button.dataset.page = String(index);
          button.textContent = new Intl.NumberFormat(document.documentElement.lang || undefined).format(index + 1);
          button.setAttribute('aria-label', this.picker.dataset.label.replace('%s', button.textContent));
          return button;
        }) : [];
        this.picker.replaceChildren(...buttons);
      }

      this.pages = pages;
      this.controls.hidden = pages.length < 2 || !this.controls.childElementCount;
      this.updateControls();
      this.updateRotation();
    }

    updateControls() {
      const position = Math.abs(this.viewport.scrollLeft);
      const last = this.pages[this.pages.length - 1];
      this.arrows.forEach((button) => {
        const disabled = button.dataset.step === '-1' ? position < 2 : position >= last - 2;
        button.setAttribute('aria-disabled', String(disabled));
      });

      if (!this.picker) return;
      const active = this.pages.reduce((closest, offset, index) =>
        Math.abs(offset - position) < Math.abs(this.pages[closest] - position) ? index : closest, 0);
      Array.from(this.picker.children).forEach((button, index) => {
        if (index === active) button.setAttribute('aria-current', 'true');
        else button.removeAttribute('aria-current');
      });
    }

    handleControl(event) {
      const button = event.target.closest('button');
      if (!button || button.getAttribute('aria-disabled') === 'true') return;

      if (button === this.rotation) {
        if (!this.motion.matches) this.playing = event.detail > 0 ? (this.rotationIntent ?? !this.playing) : !this.playing;
        this.rotationIntent = null;
        this.updateRotation();
        return;
      }
      this.pause();
      if (button.dataset.page !== undefined) this.scrollToOffset(this.pages[Number(button.dataset.page)]);
      else this.step(Number(button.dataset.step));
    }

    step(direction, wrap = false) {
      const position = Math.abs(this.viewport.scrollLeft);
      if (direction > 0) {
        const next = this.pages.find((offset) => offset > position + 2);
        if (next !== undefined || wrap) this.scrollToOffset(next ?? 0);
      } else {
        const index = this.pages.findIndex((offset) => offset >= position - 2);
        if (index > 0) this.scrollToOffset(this.pages[index - 1]);
      }
    }

    scrollToOffset(offset) {
      const direction = getComputedStyle(this.viewport).direction === 'rtl' ? -1 : 1;
      this.viewport.scrollTo({ left: offset * direction, behavior: this.motion.matches ? 'instant' : 'smooth' });
    }

    pause() {
      this.playing = false;
      this.updateRotation();
    }

    updateRotation() {
      if (!this.rotation) return;
      this.rotation.hidden = this.motion.matches;
      this.rotation.textContent = this.playing ? this.rotation.dataset.pause : this.rotation.dataset.play;
      clearInterval(this.timer);
      this.timer = null;
      if (this.playing && !this.hovered && this.visible && !document.hidden && this.pages.length > 1) {
        this.timer = setInterval(() => this.step(1, true), this.interval);
      }
    }
  }

  customElements.define('nova-post-carousel', PostCarousel);
})();
