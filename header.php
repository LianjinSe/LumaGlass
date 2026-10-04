<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$options = \Helper::options();
$colorMode = ag_option('colorMode', 'auto');
$title = ag_document_title($this);
$favicon = ag_get_favicon_url();
$pages = ag_get_published_pages();
$lightBg = trim((string) ag_option('backgroundLight', ''));
$darkBg = trim((string) ag_option('backgroundDark', ''));
$hasMath = ag_archive_has_math($this);
?>
<!DOCTYPE html>
<html lang="zh-CN" data-theme-mode="<?php echo htmlspecialchars($colorMode); ?>" data-glass="<?php echo ag_option('glassStrength', 'clear') === 'frosted' ? 'frosted' : 'clear'; ?>">
<head>
  <meta charset="<?php $options->charset(); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#edf1f8">
  <title><?php echo htmlspecialchars($title); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars(ag_meta_description($this)); ?>">
  <meta property="og:title" content="<?php echo htmlspecialchars($title); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars(ag_meta_description($this)); ?>">
  <meta property="og:type" content="<?php echo $this->is('post') ? 'article' : 'website'; ?>">
  <meta property="og:url" content="<?php echo htmlspecialchars($this->is('post') || $this->is('page') ? (string) $this->permalink : rtrim($options->siteUrl, '/') . ($_SERVER['REQUEST_URI'] ?? '/')); ?>">
  <?php if (ag_get_cover($this)): ?><meta property="og:image" content="<?php echo htmlspecialchars(ag_get_cover($this)); ?>"><?php endif; ?>
  <?php if ($favicon !== ''): ?><link rel="icon" href="<?php echo htmlspecialchars($favicon); ?>"><link rel="apple-touch-icon" href="<?php echo htmlspecialchars($favicon); ?>"><?php endif; ?>
  <link rel="alternate" type="application/rss+xml" title="<?php echo htmlspecialchars($options->title); ?>" href="<?php $options->feedUrl(); ?>">
  <script>
    (function () {
      var root = document.documentElement, mode = root.dataset.themeMode;
      root.classList.add('js');
      try { mode = localStorage.getItem('lumaglass-theme') || mode; } catch (e) {}
      if (!['auto', 'light', 'dark'].includes(mode)) mode = 'auto';
      root.dataset.themeMode = mode;
      root.dataset.theme = mode === 'auto' ? (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light') : mode;
    })();
  </script>
  <?php foreach (['tokens', 'base', 'components', 'markdown', 'pages'] as $css): ?>
    <link rel="stylesheet" href="<?php echo ag_asset('assets/css/' . $css . '.css'); ?>">
  <?php endforeach; ?>
  <style>
    :root {
      --brand-light: <?php echo preg_match('/^#[0-9a-f]{3,8}$/i', (string) ag_option('accentLight', '#0071e3')) ? ag_option('accentLight', '#0071e3') : '#0071e3'; ?>;
      --brand-dark: <?php echo preg_match('/^#[0-9a-f]{3,8}$/i', (string) ag_option('accentDark', '#2997ff')) ? ag_option('accentDark', '#2997ff') : '#2997ff'; ?>;
      --hero-radius: <?php echo max(16, min(48, (int) ag_option('heroRadius', '32'))); ?>px;
      <?php if ($lightBg): ?>--light-bg-image: url(<?php echo json_encode($lightBg, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>);<?php endif; ?>
      <?php if ($darkBg): ?>--dark-bg-image: url(<?php echo json_encode($darkBg, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>);<?php endif; ?>
    }
    <?php echo ag_option('customCss', ''); ?>
  </style>
  <?php ag_render_math_assets($this); ?>
  <?php $this->header('description=&social=0&rss2='); ?>
</head>
<body class="<?php echo $this->is('post') ? 'is-post' : ($this->is('page') ? 'is-page' : 'is-list'); ?><?php if ($hasMath): ?> mathjax-ignore<?php endif; ?>">
  <a class="skip-link" href="#main-content">跳到内容</a>
  <div class="page-bg" aria-hidden="true"><div class="landscape"></div><div class="page-bg-image"></div><div class="page-grain"></div></div>
  <?php if (ag_option_bool('showReadingProgress', true)): ?><div class="reading-progress" id="reading-progress" aria-hidden="true"></div><?php endif; ?>
  <header class="site-header-wrap">
    <div class="site-header glass-chrome">
      <a class="site-brand" href="<?php $options->siteUrl(); ?>" aria-label="<?php echo htmlspecialchars($options->title); ?>，首页">
        <?php lg_avatar('header-avatar'); ?>
        <span class="brand-text"><strong><?php echo htmlspecialchars($options->title); ?></strong><small><?php echo htmlspecialchars(ag_option('brandTagline', $options->description)); ?></small></span>
      </a>
      <nav class="site-nav" id="site-nav" aria-label="主导航">
        <a class="nav-link<?php if ($this->is('index')): ?> is-active<?php endif; ?>" href="<?php $options->siteUrl(); ?>"<?php if ($this->is('index')): ?> aria-current="page"<?php endif; ?>>首页</a>
        <?php $renderedNav = []; foreach ($pages as $page): $renderedNav[] = ag_normalize_url_key($page['permalink']); $active = $this->is('page', $page['slug']); ?>
          <a class="nav-link<?php if ($active): ?> is-active<?php endif; ?>" href="<?php echo htmlspecialchars($page['permalink']); ?>"<?php if ($active): ?> aria-current="page"<?php endif; ?>><?php echo htmlspecialchars($page['title']); ?></a>
        <?php endforeach; ?>
        <?php foreach (ag_get_nav_links() as $item): $key = ag_normalize_url_key($item['url']); if (in_array($key, $renderedNav, true)) continue; $renderedNav[] = $key; $active = ag_is_current_nav_link($this, $item['url']); ?>
          <a class="nav-link<?php if ($active): ?> is-active<?php endif; ?>" href="<?php echo htmlspecialchars($item['url']); ?>"<?php if ($active): ?> aria-current="page"<?php endif; ?><?php if (!empty($item['newtab'])): ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>><?php echo htmlspecialchars($item['name']); ?></a>
        <?php endforeach; ?>
      </nav>
      <div class="header-actions">
        <button class="icon-btn search-trigger js-only" id="search-toggle" type="button" aria-label="搜索文章" aria-haspopup="dialog"><?php echo ag_icon('search'); ?></button>
        <button class="icon-btn js-only" id="theme-toggle" type="button" aria-label="切换颜色模式">
          <span class="theme-icon theme-icon-moon"><?php echo ag_icon('moon'); ?></span><span class="theme-icon theme-icon-sun"><?php echo ag_icon('sun'); ?></span><span class="theme-icon theme-icon-auto"><?php echo ag_icon('monitor'); ?></span>
        </button>
        <button class="icon-btn mobile-only js-only" id="nav-toggle" type="button" aria-label="展开导航" aria-controls="site-nav" aria-expanded="false"><?php echo ag_icon('menu'); ?></button>
      </div>
    </div>
  </header>
  <dialog class="search-dialog glass-shell" id="search-dialog" aria-labelledby="search-title">
    <div class="dialog-heading"><div><span class="eyebrow">Find a thought</span><h2 id="search-title">找一点灵感。</h2></div><button class="icon-btn" id="search-close" type="button" aria-label="关闭搜索"><?php echo ag_icon('close'); ?></button></div>
    <form class="search-form" method="get" action="<?php $options->siteUrl(); ?>"><label class="sr-only" for="search-input">搜索关键词</label><div class="search-field"><?php echo ag_icon('search'); ?><input id="search-input" name="s" type="search" placeholder="搜索文章、想法、关键词…" required></div><button class="btn btn-primary" type="submit">搜索 <?php echo ag_icon('arrow-right'); ?></button></form>
    <p class="dialog-hint">按 <kbd>Esc</kbd> 关闭 <span>⌘ / Ctrl + K 随时搜索</span></p>
  </dialog>
  <noscript><form class="noscript-search" method="get" action="<?php $options->siteUrl(); ?>"><label>搜索文章 <input name="s" type="search" required></label><button type="submit">搜索</button></form></noscript>
  <div class="site-frame" id="main-content" tabindex="-1">
