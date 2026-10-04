(function () {
  const navToggle = document.getElementById('nav-toggle');
  const nav = document.getElementById('site-nav');
  const mobileQuery = window.matchMedia('(max-width: 700px)');
  const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
  const backtop = document.getElementById('backtop');
  const progress = document.getElementById('reading-progress');
  const dialog = document.getElementById('search-dialog');
  const searchInput = document.getElementById('search-input');
  let dialogAnimation = null;
  let dialogVersion = 0;
  let searchTrigger = null;

  function syncNav(isOpen) {
    if (!nav || !navToggle) return;
    const expanded = mobileQuery.matches && isOpen;
    nav.classList.toggle('is-open', expanded);
    navToggle.setAttribute('aria-expanded', String(expanded));
    navToggle.setAttribute('aria-label', expanded ? '收起导航' : '展开导航');
    nav.inert = mobileQuery.matches && !expanded;
  }
  syncNav(false);
  if (navToggle) navToggle.addEventListener('click', () => syncNav(!nav.classList.contains('is-open')));
  mobileQuery.addEventListener('change', () => syncNav(false));
  document.addEventListener('click', (event) => {
    if (nav && navToggle && !event.target.closest('.site-header')) syncNav(false);
  });
  if (nav) nav.addEventListener('click', (event) => { if (event.target.closest('a')) syncNav(false); });
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && nav && nav.classList.contains('is-open')) {
      syncNav(false);
      navToggle.focus();
    }
    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
      event.preventDefault();
      setSearch(true);
    }
  });

  function setSearch(open) {
    if (!dialog || typeof dialog.showModal !== 'function') return;
    const version = ++dialogVersion;
    const current = dialog.open && dialogAnimation ? getComputedStyle(dialog) : null;
    const from = current ? { opacity: current.opacity, transform: current.transform } : null;
    if (dialogAnimation) dialogAnimation.cancel();
    if (open) {
      if (!dialog.open) {
        searchTrigger = document.activeElement;
        dialog.showModal();
      }
      syncNav(false);
      searchInput.focus({ preventScroll: true });
    } else if (!dialog.open) return;
    const finish = () => {
      if (version !== dialogVersion) return;
      if (!open) {
        dialog.close();
        if (searchTrigger && searchTrigger.isConnected) searchTrigger.focus({ preventScroll: true });
      }
      dialogAnimation = null;
    };
    if (motionQuery.matches || typeof dialog.animate !== 'function') { finish(); return; }
    // Reversals start from the presentation value; input stays available throughout.
    dialogAnimation = dialog.animate([
      from || (open ? { opacity: 0, transform: 'translateY(-8px) scale(.98)' } : { opacity: 1, transform: 'none' }),
      open ? { opacity: 1, transform: 'translateY(0) scale(1)' } : { opacity: 0, transform: 'translateY(-8px) scale(.98)' }
    ], { duration: open ? 220 : 150, easing: 'cubic-bezier(.2,.7,.2,1)' });
    dialogAnimation.finished.then(finish).catch(() => {});
  }
  document.querySelectorAll('#search-toggle, [data-open-search]').forEach(button => button.addEventListener('click', () => setSearch(true)));
  const closeButton = document.getElementById('search-close');
  if (closeButton) closeButton.addEventListener('click', () => setSearch(false));
  if (dialog) {
    dialog.addEventListener('cancel', (event) => { event.preventDefault(); setSearch(false); });
    dialog.addEventListener('click', (event) => {
      const rect = dialog.getBoundingClientRect();
      if (event.target === dialog && (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom)) setSearch(false);
    });
  }

  let scrollFrame = 0;
  function updateScrollUi() {
    scrollFrame = 0;
    const y = window.scrollY || document.documentElement.scrollTop;
    if (backtop) {
      const visible = y > 360;
      backtop.classList.toggle('is-visible', visible);
      backtop.tabIndex = visible ? 0 : -1;
    }
    if (progress) {
      const max = document.documentElement.scrollHeight - document.documentElement.clientHeight;
      progress.style.transform = 'scaleX(' + (max > 0 ? Math.min(1, Math.max(0, y / max)) : 0) + ')';
    }
  }
  function scheduleScrollUi() { if (!scrollFrame) scrollFrame = requestAnimationFrame(updateScrollUi); }
  updateScrollUi();
  window.addEventListener('scroll', scheduleScrollUi, { passive: true });
  window.addEventListener('resize', scheduleScrollUi);
  window.addEventListener('ag:math-ready', scheduleScrollUi);
  if (backtop) backtop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: motionQuery.matches ? 'instant' : 'smooth' }));
  if (typeof ResizeObserver === 'function') new ResizeObserver(scheduleScrollUi).observe(document.body);

  const bookGlyph = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 5c-3-2-6-2-9-1v15c3-1 6-1 9 1 3-2 6-2 9-1V4c-3-1-6-1-9 1Zm0 0v15"/></svg>';
  function refreshAvatar(avatar) {
    const failed = Array.from(avatar.querySelectorAll('img')).some(img => img.classList.contains('failed-image') && getComputedStyle(img).display !== 'none');
    const fallback = avatar.querySelector('.avatar-fallback');
    if (fallback) fallback.hidden = !failed;
  }
  document.querySelectorAll('.post-visual img, .avatar img, .friend-avatar img').forEach(img => {
    function onImageError() {
      img.classList.add('failed-image');
      const visual = img.closest('.post-visual');
      if (visual) {
        visual.classList.add('no-image');
        if (!visual.querySelector('.cover-glyph')) {
          const glyph = document.createElement('span'); glyph.className = 'cover-glyph'; glyph.setAttribute('aria-hidden', 'true'); glyph.innerHTML = bookGlyph; visual.appendChild(glyph);
        }
        return;
      }
      const avatar = img.closest('.avatar, .friend-avatar');
      if (!avatar.querySelector('.avatar-fallback')) {
        const fallback = document.createElement('span'); fallback.className = 'avatar-fallback'; fallback.setAttribute('aria-hidden', 'true');
        const name = avatar.dataset.initial || (avatar.closest('.friend-card')?.querySelector('strong')?.textContent) || 'L';
        fallback.textContent = Array.from(name)[0]; avatar.appendChild(fallback);
      }
      refreshAvatar(avatar);
    }
    img.addEventListener('error', onImageError, { once: true });
    if (img.complete && !img.naturalWidth) onImageError();
  });
  document.addEventListener('lg:theme-change', () => document.querySelectorAll('.avatar, .friend-avatar').forEach(refreshAvatar));

  function initWeatherCard() {
    const card = document.getElementById('hero-weather');
    if (!card) return;

    const locationQuery = (card.dataset.location || '').trim();
    const cacheKey = 'aeroglass-weather:' + locationQuery.toLowerCase();
    const cacheMaxAge = 30 * 60 * 1000;
    const elements = {
      location: document.getElementById('weather-location'),
      pill: document.getElementById('weather-pill'),
      temperature: document.getElementById('weather-temperature'),
      summary: document.getElementById('weather-summary'),
      visual: document.getElementById('weather-visual'),
      feelsLike: document.getElementById('weather-feels-like'),
      humidity: document.getElementById('weather-humidity'),
      wind: document.getElementById('weather-wind'),
      range: document.getElementById('weather-range'),
      meta: document.getElementById('weather-meta')
    };

    function setState(state, kind) {
      card.dataset.weatherState = state;
      card.dataset.weatherKind = kind || 'clear';
    }

    function setText(node, value) {
      if (node) node.textContent = value;
    }

    function renderLoading() {
      setState('loading', 'clear');
      setText(elements.location, locationQuery || '未设置地点');
      setText(elements.pill, '更新中');
      setText(elements.temperature, '--');
      setText(elements.summary, '正在获取实时天气...');
      setText(elements.visual, '--');
      setText(elements.feelsLike, '--');
      setText(elements.humidity, '--');
      setText(elements.wind, '--');
      setText(elements.range, '--');
      setText(elements.meta, '数据源：Open-Meteo');
    }

    function renderError(message) {
      setState('error', 'clear');
      setText(elements.location, locationQuery || '未设置地点');
      setText(elements.pill, '暂不可用');
      setText(elements.temperature, '--');
      setText(elements.summary, message);
      setText(elements.visual, 'N/A');
      setText(elements.feelsLike, '--');
      setText(elements.humidity, '--');
      setText(elements.wind, '--');
      setText(elements.range, '--');
      setText(elements.meta, locationQuery ? '请稍后刷新重试' : '请在主题设置里填写天气地点');
    }

    function renderWeather(payload, options) {
      const isCached = options && options.isCached;
      setState('ready', payload.kind);
      const symbol = card.querySelector('.weather-symbol');
      if (symbol) {
        const cloudy = ['cloud', 'rain', 'snow', 'storm', 'fog'].includes(payload.kind);
        if (cloudy) symbol.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M7 17a4 4 0 0 1-.4-8 5.5 5.5 0 0 1 10.6-1A4.5 4.5 0 0 1 18 17H7Z"/></svg>';
      }
      setText(elements.location, payload.location);
      setText(elements.pill, payload.period);
      setText(elements.temperature, payload.temperature);
      setText(elements.summary, payload.summary);
      setText(elements.visual, payload.shortLabel);
      setText(elements.feelsLike, payload.feelsLike);
      setText(elements.humidity, payload.humidity);
      setText(elements.wind, payload.wind);
      setText(elements.range, payload.range);
      setText(elements.meta, isCached ? '已显示缓存天气 · ' + payload.meta : payload.meta);
    }

    function readCache() {
      if (!locationQuery) return null;

      try {
        const raw = window.localStorage.getItem(cacheKey);
        if (!raw) return null;

        const parsed = JSON.parse(raw);
        if (!parsed || typeof parsed !== 'object' || !parsed.payload) {
          return null;
        }

        return parsed;
      } catch (error) {
        return null;
      }
    }

    function writeCache(payload) {
      if (!locationQuery) return;

      try {
        window.localStorage.setItem(cacheKey, JSON.stringify({
          timestamp: Date.now(),
          payload: payload
        }));
      } catch (error) {
        return;
      }
    }

    if (!locationQuery) {
      renderError('请在主题设置中填写天气地点。');
      return;
    }

    const cached = readCache();
    if (cached && cached.payload) {
      renderWeather(cached.payload, { isCached: true });

      if (Date.now() - cached.timestamp < cacheMaxAge) {
        return;
      }
    } else {
      renderLoading();
    }

    fetchWeather(locationQuery)
      .then(function (payload) {
        writeCache(payload);
        renderWeather(payload);
      })
      .catch(function (error) {
        if (!cached || !cached.payload) {
          renderError(error && error.message ? error.message : '天气加载失败，请稍后再试。');
        }
      });
  }

  function fetchWeather(query) {
    const geocodingUrl = 'https://geocoding-api.open-meteo.com/v1/search?count=1&language=zh&format=json&name=' + encodeURIComponent(query);

    return fetchJson(geocodingUrl).then(function (geoData) {
      if (!geoData || !Array.isArray(geoData.results) || !geoData.results.length) {
        throw new Error('未找到该地点，请检查天气地点拼写。');
      }

      const location = geoData.results[0];
      const params = new URLSearchParams({
        latitude: String(location.latitude),
        longitude: String(location.longitude),
        current: 'temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m,is_day',
        daily: 'temperature_2m_max,temperature_2m_min',
        forecast_days: '1',
        timezone: 'auto'
      });

      return fetchJson('https://api.open-meteo.com/v1/forecast?' + params.toString()).then(function (weatherData) {
        return normalizeWeatherPayload(query, location, weatherData);
      });
    });
  }

  function fetchJson(url) {
    if (typeof window.fetch !== 'function') {
      return Promise.reject(new Error('当前浏览器不支持天气请求，请升级浏览器后重试。'));
    }

    const timeoutMs = 8000;

    return withTimeout(
      window.fetch(url, {
        headers: {
          Accept: 'application/json'
        }
      }).then(function (response) {
        if (!response.ok) {
          throw new Error('天气服务暂时不可用，请稍后再试。');
        }

        return response.json();
      }),
      timeoutMs,
      '天气请求超时，请稍后刷新重试。'
    );
    }

  function withTimeout(promise, timeoutMs, message) {
    return new Promise(function (resolve, reject) {
      const timer = window.setTimeout(function () {
        reject(new Error(message));
      }, timeoutMs);

      promise.then(function (value) {
        window.clearTimeout(timer);
        resolve(value);
      }).catch(function (error) {
        window.clearTimeout(timer);
        reject(error);
      });
    });
  }

  function normalizeWeatherPayload(query, location, weatherData) {
    const current = weatherData && weatherData.current ? weatherData.current : {};
    const daily = weatherData && weatherData.daily ? weatherData.daily : {};
    const weather = getWeatherInfo(current.weather_code, current.is_day);
    const currentTemp = formatNumber(current.temperature_2m);
    const feelsLike = formatTemperature(current.apparent_temperature);
    const humidity = formatPercent(current.relative_humidity_2m);
    const wind = formatWind(current.wind_speed_10m);
    const high = daily.temperature_2m_max && daily.temperature_2m_max.length ? daily.temperature_2m_max[0] : null;
    const low = daily.temperature_2m_min && daily.temperature_2m_min.length ? daily.temperature_2m_min[0] : null;
    const updatedAt = formatClock(current.time);

    return {
      kind: weather.kind,
      period: current.is_day === 1 ? '白天' : '夜间',
      location: formatLocation(location, query),
      temperature: currentTemp,
      summary: weather.label + '，体感 ' + feelsLike,
      shortLabel: weather.shortLabel,
      feelsLike: feelsLike,
      humidity: humidity,
      wind: wind,
      range: formatRange(high, low),
      meta: updatedAt ? '更新于 ' + updatedAt + ' · Open-Meteo' : '数据源：Open-Meteo'
    };
  }

  function formatLocation(location, fallback) {
    const parts = [];

    [location && location.name, location && location.admin1, location && location.country].forEach(function (part) {
      if (part && parts.indexOf(part) === -1) {
        parts.push(part);
      }
    });

    return parts.length ? parts.join(' · ') : fallback;
  }

  function formatNumber(value) {
    const number = Number(value);
    return Number.isFinite(number) ? String(Math.round(number)) : '--';
  }

  function formatTemperature(value) {
    const number = formatNumber(value);
    return number === '--' ? '--' : number + '°C';
  }

  function formatPercent(value) {
    const number = formatNumber(value);
    return number === '--' ? '--' : number + '%';
  }

  function formatWind(value) {
    const number = formatNumber(value);
    return number === '--' ? '--' : number + ' km/h';
  }

  function formatRange(high, low) {
    const highText = formatTemperature(high);
    const lowText = formatTemperature(low);

    if (highText === '--' && lowText === '--') {
      return '--';
    }

    return highText + ' / ' + lowText;
  }

  function formatClock(value) {
    if (typeof value !== 'string') return '';

    const parts = value.split('T');
    if (parts.length < 2) return '';

    return parts[1].slice(0, 5);
  }

  function getWeatherInfo(code, isDay) {
    const weatherCode = Number(code);
    const daytime = Number(isDay) === 1;

    if (weatherCode === 0) {
      return {
        kind: 'clear',
        label: daytime ? '晴朗' : '晴夜',
        shortLabel: daytime ? '晴' : '夜'
      };
    }

    if (weatherCode === 1) {
      return { kind: 'cloud', label: '晴间多云', shortLabel: '云' };
    }

    if (weatherCode === 2) {
      return { kind: 'cloud', label: '局部多云', shortLabel: '云' };
    }

    if (weatherCode === 3) {
      return { kind: 'cloud', label: '阴天', shortLabel: '阴' };
    }

    if ([45, 48].indexOf(weatherCode) !== -1) {
      return { kind: 'fog', label: '有雾', shortLabel: '雾' };
    }

    if ([51, 53, 55, 56, 57].indexOf(weatherCode) !== -1) {
      return { kind: 'rain', label: '毛毛雨', shortLabel: '雨' };
    }

    if ([61, 63, 65, 66, 67, 80, 81, 82].indexOf(weatherCode) !== -1) {
      return { kind: 'rain', label: '下雨', shortLabel: '雨' };
    }

    if ([71, 73, 75, 77, 85, 86].indexOf(weatherCode) !== -1) {
      return { kind: 'snow', label: '下雪', shortLabel: '雪' };
    }

    if ([95, 96, 99].indexOf(weatherCode) !== -1) {
      return { kind: 'storm', label: '雷暴', shortLabel: '雷' };
    }

    return { kind: 'clear', label: '天气稳定', shortLabel: '晴' };
  }

  initWeatherCard();
})();
