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
    return;
  }

  const reservedIds = new Set(
    Array.from(content.querySelectorAll('[id]'))
      .filter(function (element) { return !/^H[1-6]$/.test(element.tagName); })
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
    link.href = '#' + heading.id;
    link.textContent = heading.text;
    link.dataset.headingId = heading.id;
    link.style.setProperty('--toc-depth', String(heading.level - baseLevel + 1));
    fragment.appendChild(link);
    tocItems.push(link);
  });

  container.innerHTML = '';
  container.appendChild(fragment);

  let frameId = 0;

  function setActiveHeading() {
    frameId = 0;
    const activationOffset = 160;
    let activeId = headings[0].id;

    headings.forEach(function (heading) {
      if (heading.element.getBoundingClientRect().top <= activationOffset) {
        activeId = heading.id;
      }
    });

    tocItems.forEach(function (item) {
      const active = item.dataset.headingId === activeId;
      item.classList.toggle('is-active', active);
      if (active) item.setAttribute('aria-current', 'location');
      else item.removeAttribute('aria-current');
    });
  }

  function scheduleActiveHeading() {
    if (frameId) return;
    frameId = window.requestAnimationFrame(setActiveHeading);
  }

  window.addEventListener('scroll', scheduleActiveHeading, { passive: true });
  window.addEventListener('resize', scheduleActiveHeading);
  window.addEventListener('hashchange', scheduleActiveHeading);
  window.addEventListener('ag:math-ready', scheduleActiveHeading);

  scheduleActiveHeading();

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
