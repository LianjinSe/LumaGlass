/** Optional integration check: node tests/toc-browser.cjs <post-with-toc-url>.
 * Playwright is a development dependency only. Fixtures live in the browser;
 * no article, comment, setting or attachment is saved on the test site.
 */
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { chromium } = require('playwright');

const postUrl = process.argv[2];
if (!postUrl || !/^https?:\/\//.test(postUrl)) {
  console.error('Usage: node tests/toc-browser.cjs <post-with-toc-url>');
  process.exit(1);
}
const source = fs.readFileSync(path.join(__dirname, '../assets/js/toc.js'), 'utf8');
const screenshots = process.env.LUMAGLASS_TOC_SCREENSHOTS;
if (screenshots) fs.mkdirSync(screenshots, { recursive: true });

function fixture(long = false) {
  const titles = long ? Array.from({ length: 36 }, (_, i) => '章节 ' + (i + 1)) : ['开头', '第二节', '与控件同名', '重复章节', '重复章节', '末尾前一节', '最后一节'];
  return titles.map((title, i) => '<h' + (i % 2 ? '3' : '2') + (i === 2 ? ' id="toc-dialog"' : '') + '>' + title + '</h' + (i % 2 ? '3' : '2') + '><p>用于检查阅读位置、目录高亮和弹窗的测试内容。</p><div style="height:' + (i < titles.length - 2 ? 480 : 8) + 'px"></div>').join('') + '<h2> </h2>';
}

async function nextPaint(page) {
  await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
}

async function activeId(page) {
  return page.locator('.toc-item[aria-current="location"]').getAttribute('data-heading-id');
}

async function waitForTarget(page, id) {
  await page.waitForFunction(id => {
    const heading = document.getElementById(id);
    const header = document.querySelector('.site-header-wrap');
    const offset = Math.max(header.getBoundingClientRect().bottom + 16, (parseFloat(getComputedStyle(document.documentElement).scrollPaddingTop) || 0) + (parseFloat(getComputedStyle(heading).scrollMarginTop) || 0));
    const target = Math.max(0, Math.min(document.documentElement.scrollHeight - innerHeight, heading.getBoundingClientRect().top + scrollY - offset));
    return Math.abs(scrollY - target) <= 2;
  }, id);
  await nextPaint(page);
  assert.equal(await activeId(page), id, 'Highlight must remain on the clicked heading after scrolling');
}

(async () => {
  const args = [];
  if (process.getuid && process.getuid() === 0) args.push('--no-sandbox');
  if (process.env.LUMAGLASS_TEST_NO_PROXY === '1') args.push('--no-proxy-server');
  if (process.env.LUMAGLASS_TEST_RESOLVE) args.push('--host-resolver-rules=' + process.env.LUMAGLASS_TEST_RESOLVE);
  const browser = await chromium.launch({ args, ...(process.env.LUMAGLASS_BROWSER_EXECUTABLE ? { executablePath: process.env.LUMAGLASS_BROWSER_EXECUTABLE } : {}) });
  try {
    const context = await browser.newContext({ viewport: { width: 1440, height: 1000 }, ignoreHTTPSErrors: true });
    context.setDefaultTimeout(10000);
    await context.route('**/toc.js*', route => route.fulfill({ contentType: 'text/javascript', body: '' }));

    async function openFixture(options = {}) {
      const page = await context.newPage();
      if (options.mobile) await page.setViewportSize({ width: 390, height: 844 });
      if (options.reduce) await page.emulateMedia({ reducedMotion: 'reduce' });
      await page.goto(postUrl, { waitUntil: 'domcontentloaded' });
      assert.equal(await page.locator('#toc-dialog').count(), 1, 'Use an article whose table of contents is enabled');
      await page.evaluate(html => {
        document.querySelector('#entry-content').innerHTML = html;
        document.querySelectorAll('.comments-section, .post-nav-grid, .site-footer-wrap').forEach(element => { element.hidden = true; });
      }, options.empty ? '<p>没有小标题的测试文章。</p>' : fixture(options.long));
      await page.addScriptTag({ content: source });
      await page.evaluate(() => document.fonts.ready);
      await nextPaint(page);
      return page;
    }

    const desktop = await openFixture();
    assert.equal(await desktop.locator('.toc-item').count(), 7);
    assert.equal(await desktop.locator('dialog#toc-dialog').count(), 1);
    assert.equal(await desktop.locator('#entry-content #toc-dialog').count(), 0, 'Heading IDs must not collide with dialog IDs');
    const ids = await desktop.locator('.toc-item').evaluateAll(items => items.map(item => item.dataset.headingId));
    assert.equal(new Set(ids).size, ids.length, 'Repeated titles need distinct IDs');
    const indentation = await desktop.locator('.toc-item').evaluateAll(items => items.slice(0, 2).map(item => parseFloat(getComputedStyle(item).paddingLeft)));
    assert(indentation[0] >= 10 && indentation[1] > indentation[0], 'Nested headings must retain readable padding and hierarchy');
    await desktop.locator('.toc-item').nth(3).click();
    assert.equal(await activeId(desktop), ids[3], 'Selection must update immediately, without an extra wheel event');
    await waitForTarget(desktop, ids[3]);
    await desktop.locator('.toc-item').nth(5).click();
    await waitForTarget(desktop, ids[5]);
    assert.equal(await activeId(desktop), ids[5], 'Clamped jumps near the document end must not select the last heading instead');
    await desktop.mouse.wheel(0, -800);
    await desktop.waitForFunction(id => document.querySelector('.toc-item.is-active').dataset.headingId !== id, ids[5]);
    await desktop.locator('.toc-item').nth(1).click();
    await waitForTarget(desktop, ids[1]);
    await desktop.locator('.toc-item').nth(4).click();
    await waitForTarget(desktop, ids[4]);
    await desktop.goBack();
    await waitForTarget(desktop, ids[1]);
    // A late-loading image/formula above the target must keep the anchor aligned.
    await desktop.evaluate(() => {
      const space = document.createElement('div');
      space.style.height = '240px';
      document.querySelector('#entry-content').prepend(space);
      window.dispatchEvent(new Event('ag:math-ready'));
    });
    await waitForTarget(desktop, ids[1]);
    console.log('PASS: desktop selection, scroll tracking, end-of-document jumps, history, layout changes and unique IDs');
    await desktop.close();

    const mobile = await openFixture({ mobile: true });
    assert.equal(await mobile.locator('.article-aside').isVisible(), false);
    assert(await mobile.locator('#toc-toggle').isVisible());
    const startingY = await mobile.evaluate(() => scrollY);
    await mobile.locator('#toc-toggle').click();
    assert.equal(await mobile.locator('#toc-toggle').getAttribute('aria-expanded'), 'true');
    assert.equal(await mobile.evaluate(() => getComputedStyle(document.documentElement).overflow), 'hidden');
    const target = await mobile.locator('.toc-item').nth(5).getAttribute('data-heading-id');
    await mobile.locator('.toc-item').nth(5).click();
    assert.equal(await activeId(mobile), target);
    await mobile.locator('#toc-dialog').waitFor({ state: 'hidden' });
    await waitForTarget(mobile, target);
    const jumpedY = await mobile.evaluate(() => scrollY);
    assert(jumpedY > startingY);
    await mobile.locator('#toc-toggle').focus();
    await mobile.keyboard.press('Space');
    await mobile.locator('#toc-dialog').waitFor({ state: 'visible' });
    assert.equal(await activeId(mobile), target, 'Space on the trigger must not be mistaken for reading-page scrolling');
    assert.equal(await mobile.evaluate(() => document.activeElement.dataset.headingId), target);
    assert.equal(await mobile.locator('#toc-container').count(), 1, 'Desktop and mobile must share one directory');
    if (screenshots) await mobile.screenshot({ path: path.join(screenshots, 'mobile-toc.png') });
    await mobile.keyboard.press('Escape');
    await mobile.locator('#toc-dialog').waitFor({ state: 'hidden' });
    assert.equal(await mobile.locator('#toc-toggle').getAttribute('aria-expanded'), 'false');
    assert.equal(await mobile.evaluate(() => document.activeElement.id), 'toc-toggle');
    assert(Math.abs(await mobile.evaluate(() => scrollY) - jumpedY) <= 2, 'Opening/closing the dialog must not move the article');
    await mobile.locator('#toc-toggle').click();
    await mobile.mouse.click(5, 5);
    await mobile.locator('#toc-dialog').waitFor({ state: 'hidden' });
    await mobile.locator('#toc-toggle').click();
    await mobile.setViewportSize({ width: 1440, height: 1000 });
    await mobile.locator('#toc-dialog').waitFor({ state: 'hidden' });
    assert(await mobile.locator('.article-aside #toc-container').isVisible());
    assert.equal(await mobile.locator('#toc-container').count(), 1);
    await mobile.setViewportSize({ width: 320, height: 700 });
    assert(await mobile.evaluate(() => document.documentElement.scrollWidth <= innerWidth));
    await mobile.close();
    console.log('PASS: mobile popup, jump/highlight sync, focus, Escape/backdrop close, scroll lock and responsive reparenting');

    const long = await openFixture({ mobile: true, long: true, reduce: true });
    await long.locator('#toc-toggle').focus();
    await long.keyboard.press('Enter');
    assert.equal(await long.locator('#toc-dialog').evaluate(element => element.getAnimations().length), 0);
    await long.evaluate(() => {
      document.querySelector('.toc-dialog-body').scrollTop = 100000;
    });
    const last = await long.locator('.toc-item').last().getAttribute('data-heading-id');
    await long.locator('.toc-item').last().click();
    await waitForTarget(long, last);
    await long.locator('#toc-toggle').click();
    const visible = await long.locator('.toc-item.is-active').evaluate(element => {
      const item = element.getBoundingClientRect();
      const panel = document.querySelector('.toc-dialog-body').getBoundingClientRect();
      return item.top >= panel.top - 1 && item.bottom <= panel.bottom + 1;
    });
    assert(visible, 'The selected chapter must be revealed when reopening a long directory');
    assert.equal(await long.evaluate(() => document.activeElement.dataset.headingId), last);
    await long.keyboard.press('Tab');
    // A native dialog may allow a browser-toolbar stop between the last and
    // first controls, but must never focus a link on the inert reading page.
    const focus = await long.evaluate(() => ({ inside: document.getElementById('toc-dialog').contains(document.activeElement), browserChrome: document.activeElement === document.body }));
    assert(focus.inside || focus.browserChrome, 'Tab must not focus the underlying reading page');
    if (focus.browserChrome) await long.keyboard.press('Tab');
    assert(await long.evaluate(() => document.getElementById('toc-dialog').contains(document.activeElement)));
    await long.setViewportSize({ width: 1440, height: 1000 });
    await long.locator('#toc-dialog').waitFor({ state: 'hidden' });
    assert(await long.locator('.toc-item.is-active').evaluate(element => {
      const item = element.getBoundingClientRect();
      const panel = element.closest('.toc-card').getBoundingClientRect();
      return item.top >= panel.top - 1 && item.bottom <= panel.bottom + 1;
    }), 'The selected item must remain visible when a long directory moves back to the sidebar');
    await long.close();

    const empty = await openFixture({ mobile: true, empty: true });
    assert.equal(await empty.locator('#toc-toggle').isVisible(), false);
    assert.equal(await empty.locator('.toc-item').count(), 0);
    assert.equal(await empty.locator('.article-aside').isVisible(), false);
    await empty.close();
    await context.close();
    console.log('PASS: long directories, reduced motion, keyboard focus and articles without headings');

    // Exercise the genuine article too, without replacing content or scripts.
    const realContext = await browser.newContext({ viewport: { width: 1440, height: 1000 }, ignoreHTTPSErrors: true });
    const real = await realContext.newPage();
    await real.goto(postUrl, { waitUntil: 'domcontentloaded' });
    await real.evaluate(() => document.fonts.ready);
    assert(await real.locator('.toc-item').count() >= 3, 'Choose an article with at least three headings for the live smoke check');
    const realId = await real.locator('.toc-item').nth(2).getAttribute('data-heading-id');
    await real.locator('.toc-item').nth(2).click();
    assert.equal(await activeId(real), realId);
    await waitForTarget(real, realId);
    await realContext.close();
    const touchContext = await browser.newContext({ viewport: { width: 390, height: 844 }, isMobile: true, hasTouch: true, ignoreHTTPSErrors: true });
    const touch = await touchContext.newPage();
    await touch.goto(postUrl, { waitUntil: 'domcontentloaded' });
    await touch.evaluate(() => document.fonts.ready);
    await touch.locator('#toc-toggle').tap();
    if (screenshots) await touch.screenshot({ path: path.join(screenshots, 'live-mobile-toc.png') });
    await touch.locator('.toc-item').last().tap();
    await touch.locator('#toc-dialog').waitFor({ state: 'hidden' });
    await waitForTarget(touch, await touch.locator('.toc-item').last().getAttribute('data-heading-id'));
    await touchContext.close();
    console.log('PASS: actual article desktop and simulated touch navigation; no site data was modified');
  } finally {
    await browser.close();
  }
})().catch(error => { console.error(error); process.exitCode = 1; });
