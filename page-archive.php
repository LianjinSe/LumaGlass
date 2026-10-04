<?php
/**
 * 时间归档页
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
$db = \Typecho\Db::get();
$posts = $db->fetchAll($db->select()->from('table.contents')->where('type = ?', 'post')->where('status = ?', 'publish')->where('created <= ?', time())->order('created', \Typecho\Db::SORT_DESC));
$currentYear = '';
$contents = \Widget\Contents\Post\Recent::alloc('pageSize=1');
?>
<article class="single-article standalone-page archive-page"><header class="single-header"><span class="eyebrow">Collected over time</span><h1><?php $this->title(); ?><span class="heading-period">.</span></h1><p class="hero-subtitle">每一篇记录，都是时间留下的一点光。共 <?php echo count($posts); ?> 篇。</p></header>
  <main class="glass-shell article-body timeline-body"><div class="timeline-list">
    <?php foreach ($posts as $post): $contents->push($contents->filter($post)); $year = $contents->date->year; ?>
      <?php if ($year !== $currentYear): $currentYear = $year; ?><h2 class="timeline-year"><?php echo $year; ?></h2><?php endif; ?>
      <a class="timeline-entry" href="<?php $contents->permalink(); ?>"><time datetime="<?php $contents->date('c'); ?>"><?php $contents->date('m.d'); ?></time><span><?php $contents->title(); ?></span><?php echo ag_icon('arrow-right'); ?></a>
    <?php endforeach; ?>
    <?php if (!$posts): ?><div class="empty-state"><h2>故事，即将开始。</h2><p>未来的记录会按时间收集在这里。</p></div><?php endif; ?>
  </div></main>
</article>
<?php $this->need('footer.php'); ?>
