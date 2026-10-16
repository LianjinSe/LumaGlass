/* Parser-blocking head script: native pagereveal fires before first paint. */
(function () {
  const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
  let keyboardNavigation = false;

  document.addEventListener('click', (event) => {
    const link = event.target instanceof Element ? event.target.closest('a[href]') : null;
    if (!link || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
    keyboardNavigation = event.detail === 0;
  }, true);

  window.addEventListener('pageswap', (event) => {
    if (event.viewTransition && (motion.matches || keyboardNavigation)) {
      event.viewTransition.skipTransition();
    }
  });

  window.addEventListener('pagereveal', (event) => {
    keyboardNavigation = false;
    const transition = event.viewTransition;
    if (!transition) return;
    if (motion.matches) { transition.skipTransition(); return; }

    function clipUnderHeader() {
      const header = document.querySelector('.site-header-wrap');
      if (!header) return;
      const edge = Math.max(0, header.getBoundingClientRect().bottom);
      for (const [selector, property] of [['#main-content', '--vt-content-clip'], ['.site-footer-wrap', '--vt-footer-clip']]) {
        const element = document.querySelector(selector);
        if (element) document.documentElement.style.setProperty(property, Math.max(0, edge - element.getBoundingClientRect().top) + 'px');
      }
    }
    clipUnderHeader();
    transition.ready.then(clipUnderHeader).catch(() => {});
    transition.finished.then(() => {
      document.documentElement.style.removeProperty('--vt-content-clip');
      document.documentElement.style.removeProperty('--vt-footer-clip');
    });
  });
})();
