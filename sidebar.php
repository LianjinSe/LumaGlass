<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; $options = \Helper::options(); ?>
<aside class="side-column" aria-label="更多内容">
  <section class="glass-card sidebar-card about-card"><span class="sidebar-symbol" aria-hidden="true"><?php echo ag_icon('book'); ?></span><span class="eyebrow">Behind the words</span><h2>关于这里。</h2><p><?php echo htmlspecialchars(ag_option('sidebarIntro', $options->description)); ?></p><div class="sidebar-social"><?php ag_render_social_links(); ?></div></section>
  <section class="glass-card sidebar-card archive-card"><div class="section-head compact"><h2>时光切片</h2><span><?php echo ag_icon('arrow-right'); ?></span></div><ul class="sidebar-list"><?php \Widget\Contents\Post\Date::alloc('type=month&format=Y 年 m 月')->parse('<li><a href="{permalink}"><span>{date}</span><span aria-hidden="true">↗</span></a></li>'); ?></ul></section>
  <?php if (!$this->is('index')): ?><section class="glass-card sidebar-card"><div class="section-head compact"><h2>最近文章</h2></div><ul class="sidebar-list"><?php \Widget\Contents\Post\Recent::alloc('pageSize=5')->to($recent); while ($recent->next()): ?><li><a href="<?php $recent->permalink(); ?>"><?php $recent->title(); ?></a></li><?php endwhile; ?></ul></section><?php endif; ?>
  <p class="sidebar-note">Written with curiosity.<br>Collected with care.</p>
</aside>
