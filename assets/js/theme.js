(function () {
  const root = document.documentElement;
  const toggle = document.getElementById('theme-toggle');
  const system = window.matchMedia('(prefers-color-scheme: dark)');
  const modes = ['auto', 'light', 'dark'];
  const labels = { auto: '跟随系统', light: '浅色', dark: '深色' };
  let mode = root.dataset.themeMode || 'auto';
  function applyTheme() {
    if (!modes.includes(mode)) mode = 'auto';
    const actual = mode === 'auto' ? (system.matches ? 'dark' : 'light') : mode;
    root.dataset.themeMode = mode;
    root.dataset.theme = actual;
    if (toggle) {
      const next = modes[(modes.indexOf(mode) + 1) % modes.length];
      const label = labels[mode] + '模式，点击切换为' + labels[next];
      toggle.setAttribute('aria-label', label);
      toggle.title = label;
    }
    document.dispatchEvent(new Event('lg:theme-change'));
    const meta = document.querySelector('meta[name="theme-color"]');
    if (meta) meta.content = actual === 'dark' ? '#111722' : '#edf1f8';
  }
  applyTheme();
  if (toggle) toggle.addEventListener('click', () => {
    mode = modes[(modes.indexOf(mode) + 1) % modes.length];
    try { localStorage.setItem('lumaglass-theme', mode); } catch (error) {}
    applyTheme();
  });
  system.addEventListener('change', () => { if (mode === 'auto') applyTheme(); });
})();
