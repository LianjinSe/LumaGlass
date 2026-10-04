<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$options = \Helper::options();
$footerText = trim((string) ag_option('footerText', ''));
?>
  </div>
  <footer class="site-footer-wrap">
    <div class="site-footer">
      <div class="footer-main"><div><a class="footer-brand" href="<?php $options->siteUrl(); ?>"><?php echo htmlspecialchars($options->title); ?><span class="heading-period">.</span></a><?php if ($footerText !== ''): ?><p><?php echo htmlspecialchars($footerText); ?></p><?php endif; ?></div><div class="footer-social"><?php ag_render_social_links(); ?></div></div>
      <div class="footer-meta">
        <span>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($options->title); ?></span>
        <?php lg_render_registration(); ?>
        <span>Powered by <a href="https://typecho.org/" target="_blank" rel="noopener noreferrer">Typecho</a> · <a href="https://github.com/LianjinSe/LumaGlass/tree/lumaglass" target="_blank" rel="noopener noreferrer" title="LumaGlass，基于 Sandro 的 TypechoGlass">LumaGlass</a></span>
      </div>
    </div>
  </footer>
  <button class="floating-backtop glass-chrome js-only" id="backtop" type="button" aria-label="返回顶部" tabindex="-1"><?php echo ag_icon('arrow-up'); ?></button>
  <script>window.AeroGlassConfig = <?php echo json_encode(['colorMode' => ag_option('colorMode', 'auto'), 'showReadingProgress' => ag_option_bool('showReadingProgress', true), 'showToc' => ag_option_bool('showToc', true)], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;</script>
  <script src="<?php echo ag_asset('assets/js/theme.js'); ?>"></script>
  <script src="<?php echo ag_asset('assets/js/main.js'); ?>"></script>
  <script src="<?php echo ag_asset('assets/js/toc.js'); ?>"></script>
  <script><?php echo ag_option('customJs', ''); ?></script>
  <?php if (($this->is('post') || $this->is('page')) && $this->allow('comment')) ag_threaded_comments_script_safe(); ?>
  <?php $this->footer(); ?>
</body>
</html>
