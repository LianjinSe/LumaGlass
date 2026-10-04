<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; $this->need('header.php'); ?>
<header class="hero-archive"><span class="eyebrow">Explore the journal</span><h1><?php echo htmlspecialchars(ag_current_archive_title($this)); ?><span class="heading-period">.</span></h1><p class="hero-subtitle"><?php echo htmlspecialchars(ag_current_archive_subtitle($this)); ?></p></header>
<section class="content-grid"><main class="main-column glass-shell post-stream-panel">
  <?php if ($this->have()): ?><div class="post-grid"><?php while ($this->next()): ag_render_post_card($this); endwhile; ?></div><?php ag_render_pagination($this); ?><?php else: ?><div class="empty-state"><span class="empty-icon"><?php echo ag_icon('search'); ?></span><h2>还没有找到这段故事。</h2><p>换个关键词，或回到首页看看。</p><a class="btn btn-secondary" href="<?php $this->options->siteUrl(); ?>">浏览全部文章 <?php echo ag_icon('arrow-right'); ?></a></div><?php endif; ?>
</main><?php $this->need('sidebar.php'); ?></section>
<?php $this->need('footer.php'); ?>
