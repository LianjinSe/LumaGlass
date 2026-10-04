<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; $this->need('header.php'); ?>
<main class="not-found glass-shell"><span class="not-found-number" aria-hidden="true">404</span><span class="eyebrow">A small detour</span><h1>这一页，暂时走散了。</h1><p>也许故事换了个地址。回到首页，继续发现值得记录的事。</p><div class="hero-actions"><a class="btn btn-primary" href="<?php $this->options->siteUrl(); ?>">返回首页 <?php echo ag_icon('arrow-right'); ?></a><button class="btn btn-secondary js-only" type="button" data-open-search><?php echo ag_icon('search'); ?> 搜索文章</button></div></main>
<?php $this->need('footer.php'); ?>
