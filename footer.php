<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; $options = \Helper::options(); ?>
  </div>
  <footer class="site-footer-wrap">
    <div class="site-footer">
      <div class="footer-main"><div><a class="footer-brand" href="<?php $options->siteUrl(); ?>"><?php echo htmlspecialchars($options->title); ?><span class="heading-period">.</span></a><p><?php echo htmlspecialchars(ag_option('footerText', '让每一个想法，都有停留的地方。')); ?></p></div><div class="footer-social"><?php ag_render_social_links(); ?></div></div>
      <div class="footer-meta">
        <span>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($options->title); ?></span>
        <div class="footer-registration">
          <?php if (trim((string) ag_option('beian', '')) !== ''): ?><a href="https://beian.miit.gov.cn/" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars(ag_option('beian')); ?></a><?php endif; ?>
          <?php if (trim((string) ag_option('publicSecurityNumber', '')) !== ''): ?><a class="security-link" href="<?php echo htmlspecialchars(ag_option('publicSecurityUrl', '')); ?>" target="_blank" rel="noopener noreferrer"><?php if (ag_option('publicSecurityIcon', '')): ?><img src="<?php echo htmlspecialchars(ag_option('publicSecurityIcon', '')); ?>" alt="" width="14" height="14" loading="lazy"><?php endif; ?><?php echo htmlspecialchars(ag_option('publicSecurityNumber', '')); ?></a><?php endif; ?>
        </div>
        <span>Powered by <a href="https://typecho.org/" target="_blank" rel="noopener noreferrer">Typecho</a> · <a href="https://github.com/LianjinSe/TypechoGlass/tree/lumaglass" target="_blank" rel="noopener noreferrer" title="LumaGlass，基于 Sandro 的 TypechoGlass">LumaGlass</a></span>
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
