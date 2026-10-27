(function () {
  const container = document.getElementById('toc-container');
  const content = document.getElementById('entry-content');
  const headingSelector = 'h1, h2, h3, h4, h5, h6';

  if (!container || !content) return;

  const headingElements = Array.from(content.querySelectorAll(headingSelector)).filter(function (heading) {
    return normalizeHeadingText(heading.textContent) !== '';
  });

  if (!headingElements.length) {
    container.innerHTML = '<p class="muted">本页没有可生成的目录。</p>';
    const aside = container.closest('.article-aside');
    if (aside) {
      aside.hidden = true;
      const layout = aside.closest('.article-layout');
      if (layout) layout.classList.add('without-toc');
    }
    return;
  }

  const reservedIds = new Set(
    Array.from(document.querySelectorAll('[id]'))
      .filter(function (element) { return !headingElements.includes(element); })
      .map(function (element) { return element.id; })
      .filter(Boolean)
  );
  const assignedIds = new Set();
  const headings = headingElements.map(function (element, index) {
    const text = normalizeHeadingText(element.textContent);
    const level = Number(element.tagName.replace('H', '')) || 1;
    const id = ensureHeadingId(element, text, index, reservedIds, assignedIds);

    return {
      element: element,
      id: id,
      level: level,
      text: text
    };
  });

  const baseLevel = headings.reduce(function (minLevel, heading) {
    return Math.min(minLevel, heading.level);
  }, headings[0].level);
  const fragment = document.createDocumentFragment();
  const tocItems = [];

  headings.forEach(function (heading) {
    const link = document.createElement('a');
    link.className = 'toc-item';
    link.href = '#' + encodeURIComponent(heading.id);
    link.textContent = heading.text;
    link.dataset.headingId = heading.id;
    link.style.setProperty('--toc-depth', String(heading.level - baseLevel + 1));
    fragment.appendChild(link);
    tocItems.push(link);
  });

  container.innerHTML = '';
  container.appendChild(fragment);

  let frameId = 0;
  let navigationFrame = 0;
  let selection = null;
  let activeId = '';
  const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const narrowScreen = window.matchMedia('(max-width: 700px)');
  const card = container.closest('.toc-card');
  const desktopParent = card && card.parentElement;
  const desktopNext = card && card.nextSibling;
  const dialog = document.getElementById('toc-dialog');
  const dialogBody = document.getElementById('toc-dialog-body');
  const toggle = document.getElementById('toc-toggle');
  const canUseDialog = card && dialog && dialogBody && toggle && typeof dialog.showModal === 'function';
  let panelAnimation = null;

  function readingOffset() {
    const header = document.querySelector('.site-header-wrap');
    const chrome = header ? header.getBoundingClientRect().bottom + 16 : 0;
    const padding = parseFloat(getComputedStyle(document.documentElement).scrollPaddingTop) || 0;
    const margin = parseFloat(getComputedStyle(headings[0].element).scrollMarginTop) || 0;
    return Math.max(chrome, padding + margin);
  }

  function destination(heading) {
    const maximum = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
    return Math.max(0, Math.min(maximum, heading.element.getBoundingClientRect().top + window.scrollY - readingOffset()));
  }

  function revealActive(scroller) {
    const item = tocItems.find(function (link) { return link.dataset.headingId === activeId; });
    if (!item || !scroller || !scroller.clientHeight) return;
    const itemRect = item.getBoundingClientRect();
    const bounds = scroller.getBoundingClientRect();
    if (itemRect.top < bounds.top || itemRect.bottom > bounds.bottom) {
      scroller.scrollTop += itemRect.top - bounds.top - (scroller.clientHeight - itemRect.height) / 2;
    }
  }

  function markActive(id) {
    if (activeId === id) return;
    activeId = id;
    tocItems.forEach(function (item) {
      const active = item.dataset.headingId === id;
      item.classList.toggle('is-active', active);
      if (active) item.setAttribute('aria-current', 'location');
      else item.removeAttribute('aria-current');
    });
    revealActive(canUseDialog && dialog.open ? dialogBody : card);
  }

  function cancelNavigation() {
    selection = null;
    if (navigationFrame) window.cancelAnimationFrame(navigationFrame);
    navigationFrame = 0;
  }

  function trackNavigation() {
    navigationFrame = 0;
    if (!selection) return;
    const y = window.scrollY;
    const currentDestination = destination(selection.heading);
    if (Math.abs(y - currentDestination) <= 2) {
      selection.endY = y;
      return;
    }
    if (Math.abs(y - selection.destination) <= 2) {
      // Images or formulas may have changed the heading's position mid-jump.
      selection.destination = currentDestination;
      window.scrollTo({ top: currentDestination, behavior: 'instant' });
    } else {
      selection.moved = selection.moved || Math.abs(y - selection.startY) > .5;
      selection.stillFrames = Math.abs(y - selection.lastY) < .5 ? selection.stillFrames + 1 : 0;
      if (selection.moved && selection.stillFrames >= 4) {
        cancelNavigation();
        scheduleActiveHeading();
        return;
      }
    }
    selection.lastY = y;
    navigationFrame = window.requestAnimationFrame(trackNavigation);
  }

  function jumpTo(heading, behavior, updateHistory) {
    if (canUseDialog && dialog.open) closeDialog();
    cancelNavigation();
    markActive(heading.id);
    if (updateHistory && headingFromHash() !== heading) {
      const url = new URL(window.location.href);
      url.hash = heading.id;
      window.history.pushState(window.history.state, '', url);
    }
    const top = destination(heading);
    selection = { heading: heading, destination: top, endY: null, startY: window.scrollY, lastY: window.scrollY, moved: false, stillFrames: 0 };
    if (!heading.element.hasAttribute('tabindex')) {
      heading.element.setAttribute('tabindex', '-1');
      heading.element.addEventListener('blur', function () { heading.element.removeAttribute('tabindex'); }, { once: true });
    }
    heading.element.focus({ preventScroll: true });
    window.scrollTo({ top: top, behavior: behavior });
    navigationFrame = window.requestAnimationFrame(trackNavigation);
  }

  function headingFromHash() {
    let id = window.location.hash.slice(1);
    try { id = decodeURIComponent(id); } catch (error) {}
    return headings.find(function (heading) { return heading.id === id; });
  }

  function setActiveHeading() {
    frameId = 0;
    if (selection && (selection.endY === null || Math.abs(window.scrollY - selection.endY) <= 2)) {
      markActive(selection.heading.id);
      return;
    }
    selection = null;
    const activationOffset = readingOffset() + 2;
    let currentId = headings[0].id;

    headings.forEach(function (heading) {
      if (heading.element.getBoundingClientRect().top <= activationOffset) {
        currentId = heading.id;
      }
    });

    if (window.scrollY > 0 && window.scrollY + window.innerHeight >= document.documentElement.scrollHeight - 2) currentId = headings[headings.length - 1].id;
    markActive(currentId);
  }

  function scheduleActiveHeading() {
    if (frameId) return;
    frameId = window.requestAnimationFrame(setActiveHeading);
  }

  function syncContentLayout() {
    if (selection && selection.endY !== null && !(canUseDialog && dialog.open)) {
      const top = destination(selection.heading);
      if (Math.abs(window.scrollY - top) > 2) {
        selection.destination = top;
        selection.endY = null;
        window.scrollTo({ top: top, behavior: 'instant' });
        if (!navigationFrame) navigationFrame = window.requestAnimationFrame(trackNavigation);
      }
    }
    scheduleActiveHeading();
  }

  window.addEventListener('scroll', scheduleActiveHeading, { passive: true });
  window.addEventListener('resize', function () {
    if (!(canUseDialog && dialog.open)) cancelNavigation();
    scheduleActiveHeading();
  });
  window.addEventListener('hashchange', function () {
    const heading = headingFromHash();
    if (heading) jumpTo(heading, 'instant', false);
    else { cancelNavigation(); scheduleActiveHeading(); }
  });
  window.addEventListener('ag:math-ready', syncContentLayout);
  window.addEventListener('pageshow', scheduleActiveHeading);
  ['wheel', 'touchmove'].forEach(function (eventName) {
    window.addEventListener(eventName, function () {
      if (canUseDialog && dialog.open) return;
      cancelNavigation();
      scheduleActiveHeading();
    }, { passive: true });
  });
  document.addEventListener('keydown', function (event) {
    if (canUseDialog && dialog.open) return;
    if (!['ArrowUp', 'ArrowDown', 'PageUp', 'PageDown', 'Home', 'End', ' '].includes(event.key)) return;
    if (event.target instanceof Element && event.target.closest('button, [role="button"], input, textarea, select, [contenteditable]:not([contenteditable="false"])')) return;
    cancelNavigation();
    scheduleActiveHeading();
  });
  container.addEventListener('click', function (event) {
    const link = event.target instanceof Element ? event.target.closest('.toc-item') : null;
    if (!link || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
    const heading = headings.find(function (item) { return item.id === link.dataset.headingId; });
    if (!heading) return;
    event.preventDefault();
    jumpTo(heading, motion.matches || event.detail === 0 ? 'instant' : 'smooth', true);
  });

  function closeDialog() {
    if (panelAnimation) panelAnimation.cancel();
    panelAnimation = null;
    if (dialog.open) dialog.close();
    toggle.setAttribute('aria-expanded', 'false');
  }

  function syncLayout() {
    if (dialog.open) closeDialog();
    if (narrowScreen.matches) dialogBody.appendChild(card);
    else {
      desktopParent.insertBefore(card, desktopNext);
      revealActive(card);
    }
    scheduleActiveHeading();
  }

  if (canUseDialog) {
    document.body.classList.add('has-mobile-toc');
    toggle.hidden = false;
    syncLayout();
    narrowScreen.addEventListener('change', syncLayout);
    toggle.addEventListener('click', function (event) {
      if (!narrowScreen.matches || dialog.open) return;
      dialog.showModal();
      toggle.setAttribute('aria-expanded', 'true');
      setActiveHeading();
      const item = tocItems.find(function (link) { return link.dataset.headingId === activeId; });
      if (item) item.focus({ preventScroll: true });
      revealActive(dialogBody);
      if (!motion.matches && event.detail !== 0 && typeof dialog.animate === 'function') {
        const animation = dialog.animate([{ opacity: 0, transform: 'translateY(12px)' }, { opacity: 1, transform: 'none' }], { duration: 200, easing: getComputedStyle(document.documentElement).getPropertyValue('--ease').trim() });
        panelAnimation = animation;
        animation.finished.then(function () { if (panelAnimation === animation) panelAnimation = null; }).catch(function () {});
      }
    });
    document.getElementById('toc-close').addEventListener('click', closeDialog);
    dialog.addEventListener('cancel', function (event) { event.preventDefault(); closeDialog(); });
    dialog.addEventListener('close', function () { if (!dialog.open) closeDialog(); });
    dialog.addEventListener('click', function (event) {
      const rect = dialog.getBoundingClientRect();
      if (event.target === dialog && (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom)) closeDialog();
    });
  }
  window.addEventListener('pagehide', function () {
    cancelNavigation();
    if (frameId) window.cancelAnimationFrame(frameId);
    frameId = 0;
    if (canUseDialog) closeDialog();
  });
  if (typeof ResizeObserver === 'function') {
    new ResizeObserver(syncContentLayout).observe(content);
  }

  const initialHeading = headingFromHash();
  if (initialHeading) jumpTo(initialHeading, 'instant', false);
  else scheduleActiveHeading();

  function normalizeHeadingText(text) {
    return String(text || '').replace(/\s+/g, ' ').trim();
  }

  function ensureHeadingId(element, text, index, reservedIds, assignedIds) {
    const currentId = String(element.id || '').trim();

    if (currentId !== '' && !reservedIds.has(currentId) && !assignedIds.has(currentId)) {
      assignedIds.add(currentId);
      return currentId;
    }

    const baseId = slugify(text) || ('section-' + (index + 1));
    let candidateId = baseId;
    let suffix = 2;

    while (reservedIds.has(candidateId) || assignedIds.has(candidateId)) {
      candidateId = baseId + '-' + suffix;
      suffix += 1;
    }

    element.id = candidateId;
    assignedIds.add(candidateId);
    return candidateId;
  }

  function slugify(text) {
    let normalized = String(text || '').trim().toLowerCase();

    if (typeof normalized.normalize === 'function') {
      normalized = normalized.normalize('NFKD').replace(/[\u0300-\u036f]/g, '');
    }

    try {
      normalized = normalized.replace(/[^\p{Letter}\p{Number}\s-]/gu, '');
    } catch (error) {
      normalized = normalized.replace(/[^\w\u4e00-\u9fa5\s-]/g, '');
    }

    return normalized
      .replace(/[_\s-]+/g, '-')
      .replace(/^-+|-+$/g, '');
  }
})();
