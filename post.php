<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; $this->need('header.php'); ?>
<article class="single-article">
  <header class="single-header">
    <a class="back-link" href="<?php $this->options->siteUrl(); ?>"><?php echo ag_icon('arrow-right'); ?> 所有记录</a>
    <div class="meta-row"><span class="meta-pill"><?php echo htmlspecialchars(ag_primary_category_name($this)); ?></span><?php if (isset($this->fields->featured) && $this->fields->featured == '1'): ?><span class="meta-pill accent">精选</span><?php endif; ?></div>
    <h1><?php echo htmlspecialchars(html_entity_decode((string) $this->title, ENT_QUOTES, 'UTF-8')); ?></h1>
    <?php if (isset($this->fields->subtitle) && trim((string) $this->fields->subtitle) !== ''): ?><p class="hero-subtitle mathjax-process"><?php echo htmlspecialchars((string) $this->fields->subtitle); ?></p><?php endif; ?>
    <div class="entry-meta"><time datetime="<?php $this->date('c'); ?>"><?php $this->date('Y 年 m 月 d 日'); ?></time><span aria-hidden="true">·</span><span><?php echo ag_estimated_reading_time($this->text); ?> 分钟阅读</span><span aria-hidden="true">·</span><span><?php $this->commentsNum('0 条评论', '1 条评论', '%d 条评论'); ?></span></div>
  </header>
  <div class="article-layout<?php if (!ag_option_bool('showToc', true) || (isset($this->fields->disableToc) && $this->fields->disableToc == '1')): ?> without-toc<?php endif; ?>">
    <main class="article-main">
      <section class="article-body glass-shell"><div class="entry-content mathjax-process" id="entry-content"><?php $this->content(); ?></div><?php if ($this->tags): ?><footer class="entry-footer"><div class="tag-row"><?php $this->tags('', true, '<a class="tag-chip" href="{permalink}">#{name}</a>'); ?></div></footer><?php endif; ?></section>
      <?php ag_render_adjacent_post_nav($this); ?>
      <?php $this->need('comments.php'); ?>
    </main>
    <?php if (ag_option_bool('showToc', true) && !(isset($this->fields->disableToc) && $this->fields->disableToc == '1')): ?><aside class="article-aside"><nav class="glass-card toc-card" aria-label="文章目录"><span class="eyebrow">On this page</span><h2>阅读目录</h2><div id="toc-container" class="toc-container mathjax-process"></div></nav><p class="reading-aside-note">慢慢读，不必着急。</p></aside><?php endif; ?>
  </div>
</article>
<?php $this->need('footer.php'); ?>
