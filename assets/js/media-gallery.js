(() => {
  if (customElements.get('nova-media')) return;

  class NovaMedia extends HTMLElement {
    connectedCallback() {
      if (this.initialized) {
        if (this.navigationObserver) this.navigationObserver.observe(this.querySelector('.nova-media__items'));
        return;
      }
      const items = this.querySelector('.nova-media__items');
      if (!items) return;
      this.initialized = true;
      this.setupFilters(items);
      this.setupNavigation(items);
      this.setupDialog();
    }

    disconnectedCallback() {
      this.navigationObserver?.disconnect();
    }

    setupFilters(items) {
      const filters = this.querySelector('.nova-media__filters');
      if (!filters) return;
      filters.hidden = false;
      filters.addEventListener('click', (event) => {
        const button = event.target.closest('[data-media-filter]');
        if (!button) return;
        const filter = button.dataset.mediaFilter;
        Array.from(items.children).forEach((item) => {
          item.hidden = filter !== '' && item.dataset.mediaCategory !== filter;
        });
        filters.querySelectorAll('button').forEach((candidate) => {
          candidate.setAttribute('aria-pressed', String(candidate === button));
        });
      });
    }

    setupNavigation(items) {
      const navigation = this.querySelector('.nova-media__navigation');
      if (!navigation) return;
      const update = () => { navigation.hidden = items.scrollWidth <= items.clientWidth + 1; };
      this.navigationObserver = new ResizeObserver(update);
      this.navigationObserver.observe(items);
      update();
      const step = (direction) => {
        const rtl = getComputedStyle(items).direction === 'rtl' ? -1 : 1;
        const gap = parseFloat(getComputedStyle(items).columnGap) || 0;
        const distance = items.firstElementChild.getBoundingClientRect().width + gap;
        items.scrollBy({
          left: distance * direction * rtl,
          behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth',
        });
      };
      navigation.addEventListener('click', (event) => {
        const button = event.target.closest('[data-media-step]');
        if (button) step(Number(button.dataset.mediaStep));
      });
      items.addEventListener('keydown', (event) => {
        if (event.target !== items || !['ArrowLeft', 'ArrowRight'].includes(event.key)) return;
        const rtl = getComputedStyle(items).direction === 'rtl';
        event.preventDefault();
        step((event.key === 'ArrowRight') !== rtl ? 1 : -1);
      });
    }

    setupDialog() {
      const dialog = this.querySelector('.nova-media__dialog');
      if (typeof dialog?.showModal !== 'function') return;
      let trigger;
      const close = document.createElement('button');
      close.type = 'button';
      close.className = 'nova-media__close';
      close.textContent = dialog.dataset.closeLabel;
      close.autofocus = true;
      close.addEventListener('click', () => dialog.close());
      const clear = () => {
        const video = dialog.querySelector('video');
        if (video) {
          video.pause();
          video.removeAttribute('src');
          video.load();
        }
        dialog.replaceChildren();
      };
      dialog.addEventListener('close', () => {
        if (dialog.open) return;
        clear();
        if (trigger?.isConnected) trigger.focus({ preventScroll: true });
      });
      this.querySelectorAll('.nova-media__open').forEach((link) => link.setAttribute('aria-haspopup', 'dialog'));
      this.addEventListener('click', (event) => {
        const link = event.target.closest('.nova-media__open');
        if (!link || event.defaultPrevented || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || dialog.open) return;
        event.preventDefault();
        trigger = link;
        const media = this.createMedia(link);
        dialog.setAttribute('aria-label', link.dataset.mediaTitle);
        clear();
        dialog.replaceChildren(close, media);
        dialog.showModal();
        if (media instanceof HTMLVideoElement) media.play().catch(() => {});
      });
      dialog.addEventListener('keydown', (event) => {
        if (event.key !== 'Tab' || dialog.querySelector('video, iframe')) return;
        event.preventDefault();
        close.focus();
      });
    }

    createMedia(link) {
      const { mediaType, mediaSource, mediaTitle } = link.dataset;
      if (mediaType === 'embed') {
        const iframe = document.createElement('iframe');
        iframe.title = mediaTitle;
        iframe.allow = 'autoplay; fullscreen; picture-in-picture';
        iframe.allowFullscreen = true;
        iframe.referrerPolicy = 'strict-origin-when-cross-origin';
        iframe.src = mediaSource;
        return iframe;
      }
      if (mediaType === 'video') {
        const video = document.createElement('video');
        video.controls = true;
        video.playsInline = true;
        video.preload = 'none';
        video.src = mediaSource;
        if (link.dataset.mediaTrack) {
          const track = document.createElement('track');
          track.kind = 'captions';
          track.src = link.dataset.mediaTrack;
          track.srclang = link.dataset.mediaLanguage;
          track.label = link.dataset.mediaLanguage;
          video.append(track);
        }
        return video;
      }
      const image = document.createElement('img');
      image.alt = link.querySelector('img')?.alt || mediaTitle;
      image.src = mediaSource;
      return image;
    }
  }

  customElements.define('nova-media', NovaMedia);
})();
