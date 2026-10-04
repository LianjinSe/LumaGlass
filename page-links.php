<?php
/**
 * 友情链接页
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
$linkItems = ag_parse_json(isset($this->fields->linksJson) ? (string) $this->fields->linksJson : '');
$contactUrl = '';
foreach (ag_get_social_links() as $social) {
    if (strpos((string) ($social['url'] ?? ''), 'mailto:') === 0) { $contactUrl = $social['url']; break; }
}
?>
<article class="single-article standalone-page links-page">
  <header class="single-header"><span class="eyebrow">People & connections</span><h1><?php $this->title(); ?><span class="heading-period">.</span></h1><?php if (trim((string) ag_option('friendsIntro', '')) !== ''): ?><p class="hero-subtitle"><?php echo htmlspecialchars(ag_option('friendsIntro')); ?></p><?php endif; ?></header>
  <main class="glass-shell article-body links-body">
    <div class="entry-content links-plugin-wrap mathjax-process"><?php lg_friends_content($this); ?></div>
    <?php if ($linkItems): ?><div class="section-head compact mtop-xl"><h2>一些值得遇见的朋友</h2></div><?php lg_render_friend_cards($linkItems); ?><?php endif; ?>
    <?php if ($contactUrl !== ''): ?><div class="friend-contact"><div><strong>让有趣的世界，彼此连接。</strong><p>想交换友链，或只是打个招呼？</p></div><a class="btn btn-secondary" href="<?php echo htmlspecialchars($contactUrl, ENT_QUOTES); ?>"><?php echo ag_icon('mail'); ?> 写一封信</a></div><?php endif; ?>
  </main>
</article>
<?php $this->need('footer.php'); ?>
