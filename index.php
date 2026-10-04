<?php
/**
 * 澄光 · LumaGlass。为想法留一点光，基于 TypechoGlass 的独立主题。
 *
 * @package LumaGlass
 * @author LianjinSe / TypechoGlass by Sandro
 * @version 1.0.0
 * @link https://github.com/LianjinSe/LumaGlass/tree/lumaglass
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
$options = \Helper::options();
$stats = lg_site_stats();
$weatherLocation = trim((string) ag_option('weatherLocation', 'Shanghai'));
?>
<section class="hero-home" aria-labelledby="hero-title">
  <div class="hero-copy">
    <span class="eyebrow"><span class="eyebrow-line" aria-hidden="true"></span><?php echo htmlspecialchars(ag_option('heroEyebrow', 'A little space for thoughts')); ?></span>
    <h1 id="hero-title"><?php echo htmlspecialchars(ag_option('heroTitle', $options->title)); ?><span class="title-period" aria-hidden="true"></span></h1>
    <p class="hero-subtitle"><?php echo htmlspecialchars(ag_option('heroSubtitle', $options->description)); ?></p>
    <div class="hero-actions"><a class="btn btn-primary" href="#post-stream">开始阅读 <?php echo ag_icon('arrow-right'); ?></a><a class="btn btn-secondary" href="<?php $options->feedUrl(); ?>"><?php echo ag_icon('rss'); ?> 订阅 RSS</a></div>
    <?php $heroFootnote = trim((string) ag_option('heroFootnote', '')); if ($heroFootnote !== ''): ?><div class="hero-footnote"><span class="small-dot" aria-hidden="true"></span><?php echo htmlspecialchars($heroFootnote); ?></div><?php endif; ?>
  </div>
  <aside class="profile-card glass-shell" aria-label="个人空间">
    <div class="profile-topline"><span class="eyebrow">Personal space</span><span class="profile-orbit" aria-hidden="true"><?php echo ag_icon('sun'); ?></span></div>
    <div class="profile-identity"><?php lg_avatar('profile-avatar'); ?><div><strong><?php echo htmlspecialchars($options->title); ?></strong><p><?php echo htmlspecialchars(ag_option('brandTagline', $options->description)); ?></p></div></div>
    <div class="profile-status"><span class="status-dot" aria-hidden="true"></span><span><?php echo htmlspecialchars(ag_option('heroPanelOneLabel', '最近状态')); ?></span><strong><?php echo htmlspecialchars(ag_option('heroPanelOneValue', '保持好奇')); ?></strong></div>
    <div class="profile-stats"><div><strong><?php echo $stats['posts']; ?></strong><span>篇记录</span></div><div><strong><?php echo $stats['categories']; ?></strong><span>个分类</span></div><a href="<?php $options->feedUrl(); ?>"><span class="stat-icon"><?php echo ag_icon('rss'); ?></span><span>保持联络</span></a></div>
    <div class="hero-weather" id="hero-weather" data-location="<?php echo htmlspecialchars($weatherLocation); ?>" aria-live="polite">
      <div class="weather-left"><span class="weather-symbol" aria-hidden="true"><?php echo ag_icon('sun'); ?></span><div><strong class="weather-location" id="weather-location"><?php echo htmlspecialchars($weatherLocation ?: '未设置地点'); ?></strong><p id="weather-summary">正在获取天气…</p></div></div>
      <div class="weather-right"><strong><span id="weather-temperature">--</span><small>°</small></strong><span class="weather-pill" id="weather-pill">天气</span></div>
      <span class="sr-only" id="weather-meta">数据源：Open-Meteo</span>
    </div>
  </aside>
</section>
<section id="post-stream" class="content-grid" aria-labelledby="posts-heading">
  <main class="main-column glass-shell post-stream-panel">
    <div class="section-head"><div><span class="eyebrow">The journal</span><h2 id="posts-heading">最近的记录<span class="heading-period">.</span></h2></div><span class="section-count"><?php echo $stats['posts']; ?> 篇文章</span></div>
    <?php if (ag_option_bool('showCategoryStrip', true)): ?>
      <nav class="category-strip" aria-label="文章分类"><a class="chip is-active" href="<?php $options->siteUrl(); ?>" aria-current="page">全部</a><?php \Widget\Metas\Category\Rows::alloc()->to($categories); while ($categories->next()): ?><a class="chip" href="<?php $categories->permalink(); ?>"><?php $categories->name(); ?><span><?php echo (int) $categories->count; ?></span></a><?php endwhile; ?></nav>
    <?php endif; ?>
    <?php if ($this->have()): ?><div class="post-grid"><?php while ($this->next()): ag_render_post_card($this); endwhile; ?></div><?php ag_render_pagination($this); ?><?php else: ?><div class="empty-state"><span class="empty-icon"><?php echo ag_icon('book'); ?></span><h3>故事，即将开始。</h3><p>下一段想法，会在这里与你见面。</p></div><?php endif; ?>
  </main>
  <?php $this->need('sidebar.php'); ?>
</section>
<?php $this->need('footer.php'); ?>
