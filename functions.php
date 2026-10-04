<?php

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

function themeConfig($form)
{
    $elements = [
        text('heroEyebrow', 'A little space for thoughts', '首页眉标题', '显示在首页 Hero 小字上方。'),
        text('heroTitle', '记录，慢慢发光。', '首页标题', '首页 Hero 主标题。'),
        textarea('heroSubtitle', '把生活的片刻、学习的发现，以及脑海里的零碎，留在这里。', '首页副标题', '首页 Hero 副标题。'),
        textarea('heroFootnote', '', '首页附注文案', '显示在首页阅读按钮下方，留空隐藏。'),
        text('heroPanelOneLabel', '最近状态', '首页信息卡 1 标题', '首页 Hero 右侧第一张卡片标题。'),
        text('heroPanelOneValue', '保持好奇', '首页信息卡 1 内容', '首页 Hero 右侧第一张卡片内容。'),
        text('weatherLocation', 'Shanghai', '天气地点', '首页天气卡使用的地点，建议填写城市英文名，如 Shanghai、Beijing、Tokyo。'),
        radio('colorMode', ['auto' => '跟随系统', 'light' => '浅色', 'dark' => '深色'], 'auto', '默认颜色模式', '仍可在前台手动切换。'),
        text('favicon', '', '浏览器图标 URL', '用于浏览器标签页和收藏夹图标，支持 png、ico、svg。'),
        text('logoLight', '', '浅色 Logo URL', '可留空，留空则使用文字标识。'),
        text('logoDark', '', '深色 Logo URL', '可留空。'),
        text('brandInitial', 'A', '文字 Logo 首字母', '当未设置 Logo 图片时显示。'),
        text('brandTagline', '', '站点副标', '导航区域的小字。留空则使用站点描述。'),
        text('accentLight', '#0071e3', '浅色强调色', '建议使用纯色。'),
        text('accentDark', '#2997ff', '深色强调色', '建议使用纯色。'),
        text('backgroundLight', '', '浅色背景图 URL', '可留空，留空则使用内置渐变背景。'),
        text('backgroundDark', '', '深色背景图 URL', '可留空。'),
        text('heroRadius', '32', '大圆角数值', '例如 32。'),
        text('homePageSize', '10', '首页每页文章数', '用于首页文章分页。'),
        text('archivePageSize', '10', '归档/搜索每页文章数', '用于普通列表页。'),
        text('categoryPageSize', '12', '分类页每页文章数', '用于分类分页。'),
        textarea('socialLinks', "[\n  {\"name\":\"GitHub\",\"icon\":\"github\",\"url\":\"https://github.com/yourname\"},\n  {\"name\":\"X\",\"icon\":\"x\",\"url\":\"https://x.com/yourname\"},\n  {\"name\":\"RSS\",\"icon\":\"rss\",\"url\":\"/feed/\"}\n]", '社交链接 JSON', '支持自定义名称、图标、地址。图标可用：github、x、telegram、mail、rss、bilibili、youtube、link。'),
        textarea('extraNavLinks', "[\n  {\"name\":\"归档\",\"url\":\"special:archive\"},\n  {\"name\":\"友链\",\"url\":\"special:friends\"}\n]", '额外导航 JSON', '除独立页面外追加的导航。字段：name、url、newtab。支持 special:archive / special:friends 自动匹配对应页面。'),
        textarea('friendsIntro', '把喜欢的站点收集起来，让有趣的人彼此相遇。', '友链页说明', '用于友情链接页顶部说明。'),
        textarea('sidebarIntro', '', '侧栏简介', '留空则使用站点描述。'),
        textarea('sidebarNote', '', '侧栏附注文案', '显示在侧栏底部，支持换行，留空隐藏。'),
        textarea('footerText', '', '页脚文案', '显示在页脚，留空隐藏。'),
        text('beian', '', 'ICP备案号', '填写本站的 ICP 备案号，留空隐藏。'),
        text('beianUrl', 'https://beian.miit.gov.cn/', 'ICP备案查询链接', '支持 HTTP/HTTPS；清空后仅显示备案号文字。'),
        text('publicSecurityNumber', '', '公安备案号', '填写本站的公安备案号，留空隐藏。'),
        text('publicSecurityUrl', '', '公安备案查询链接', '填写本站备案查询页面的 HTTP/HTTPS 地址；留空后仅显示备案号文字。'),
        text('publicSecurityIcon', '', '公安备案图标 URL', '可填写图片 URL 或本站图片路径，留空不显示图标。'),
        radio('glassStrength', ['clear' => '通透', 'frosted' => '磨砂'], 'clear', '玻璃材质', '磨砂模式适合细节较多的背景。'),
        checkbox('featureSwitch', [
            'showReadingProgress' => '显示阅读进度条',
            'showToc' => '显示文章目录',
            'showCategoryStrip' => '首页显示分类条',
            'allowPageComments' => '独立页面显示评论',
        ], ['showReadingProgress', 'showToc', 'showCategoryStrip'], '功能开关', '按需启用。'),
        textarea('customCss', '', '自定义 CSS', '直接输出到 head。'),
        textarea('customJs', '', '自定义 JS', '直接输出到 footer。'),
    ];

    foreach ($elements as $element) {
        $form->addInput($element);
    }
}

function themeFields($layout)
{
    $fields = [
        text_field('cover', '', '封面图 URL', '文章/页面封面图。'),
        text_field('subtitle', '', '副标题', '显示在文章或页面头图区域。'),
        radio_field('featured', ['0' => '否', '1' => '是'], '0', '是否推荐', '用于文章列表角标。'),
        radio_field('disableToc', ['0' => '否', '1' => '是'], '0', '关闭目录', '只对当前文章有效。'),
        textarea_field('linksJson', "[\n  {\"name\":\"Typecho\",\"url\":\"https://typecho.org\",\"description\":\"Typecho 官方站点\",\"image\":\"\"}\n]", '友链 JSON', '仅友情链接页模板使用。'),
    ];

    foreach ($fields as $field) {
        $layout->addItem($field);
    }
}

function themeInit($archive)
{
    if ($archive->is('index')) {
        $archive->parameter->pageSize = max(1, (int) ag_option('homePageSize', '10'));
        return;
    }

    if ($archive->is('category')) {
        $archive->parameter->pageSize = max(1, (int) ag_option('categoryPageSize', '12'));
        return;
    }

    if ($archive->is('search') || $archive->is('tag') || $archive->is('archive')) {
        $archive->parameter->pageSize = max(1, (int) ag_option('archivePageSize', '10'));
    }
}

function text($name, $value, $label, $description = '')
{
    $el = new \Typecho\Widget\Helper\Form\Element\Text($name, null, lg_setting_default($name, $value), _t($label), _t($description));
    return $el;
}

function textarea($name, $value, $label, $description = '')
{
    return new \Typecho\Widget\Helper\Form\Element\Textarea($name, null, lg_setting_default($name, $value), _t($label), _t($description));
}

function radio($name, $options, $value, $label, $description = '')
{
    return new \Typecho\Widget\Helper\Form\Element\Radio($name, $options, lg_setting_default($name, $value), _t($label), _t($description));
}

function checkbox($name, $options, $value, $label, $description = '')
{
    return (new \Typecho\Widget\Helper\Form\Element\Checkbox($name, $options, lg_setting_default($name, $value), _t($label), _t($description)))->multiMode();
}

function text_field($name, $value, $label, $description = '')
{
    return new \Typecho\Widget\Helper\Form\Element\Text($name, null, lg_setting_default($name, $value), _t($label), _t($description));
}

function textarea_field($name, $value, $label, $description = '')
{
    return new \Typecho\Widget\Helper\Form\Element\Textarea($name, null, lg_setting_default($name, $value), _t($label), _t($description));
}

function radio_field($name, $options, $value, $label, $description = '')
{
    return new \Typecho\Widget\Helper\Form\Element\Radio($name, $options, lg_setting_default($name, $value), _t($label), _t($description));
}

function lg_legacy_settings()
{
    static $settings = null;
    if ($settings === null) {
        $raw = \Helper::options()->{'theme:TypechoGlass'};
        $decoded = is_string($raw) ? json_decode($raw, true) : null;
        $settings = is_array($decoded) ? $decoded : [];
    }
    return $settings;
}

function lg_setting_default($name, $default)
{
    $settings = lg_legacy_settings();
    return array_key_exists($name, $settings) && $settings[$name] !== null ? $settings[$name] : $default;
}

function ag_option($name, $default = '')
{
    $options = \Helper::options();
    if (isset($options->$name)) {
        return $options->$name === null ? $default : $options->$name;
    }
    return lg_setting_default($name, $default);
}

function ag_option_bool($name, $default = false)
{
    $switches = ag_option('featureSwitch', null);
    return $switches === null ? $default : in_array($name, (array) $switches, true);
}

function ag_asset($path)
{
    $options = \Helper::options();
    $rootUrl = rtrim((string) $options->rootUrl, '/');
    $themeDir = trim(defined('__TYPECHO_THEME_DIR__') ? __TYPECHO_THEME_DIR__ : '/usr/themes', '/');
    $theme = trim((string) $options->theme, '/');
    $relativePath = ltrim($path, '/');
    $assetUrl = $rootUrl . '/' . $themeDir . '/' . $theme . '/' . $relativePath;
    $localPath = rtrim(__TYPECHO_ROOT_DIR__, '/\\') . DIRECTORY_SEPARATOR
        . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $themeDir) . DIRECTORY_SEPARATOR
        . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $theme) . DIRECTORY_SEPARATOR
        . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);

    if (is_file($localPath)) {
        $separator = strpos($assetUrl, '?') === false ? '?' : '&';
        $assetUrl .= $separator . 'v=' . (string) filemtime($localPath);
    }

    return $assetUrl;
}

function ag_archive_has_math($archive)
{
    if (!is_object($archive) || !method_exists($archive, 'is')) {
        return false;
    }

    if (!$archive->is('post') && !$archive->is('page')) {
        return false;
    }

    foreach (ag_get_archive_math_sources($archive) as $source) {
        if (preg_match('/\$\$(?:.|[\r\n])*?\$\$/u', $source)) {
            return true;
        }
        if (preg_match('/\\\\\[(?:.|[\r\n])*?\\\\\]/u', $source)) {
            return true;
        }
        if (preg_match('/\\\\\((?:.|[\r\n])*?\\\\\)/u', $source)) {
            return true;
        }
        if (preg_match('/\\\\begin\{([a-zA-Z*]+)\}(?:.|[\r\n])*?\\\\end\{\1\}/u', $source)) {
            return true;
        }
        if (preg_match('/(?<!\$)\$(?!\$)(?:\\\\.|[^$\\\\\r\n])+(?<!\\\\)\$(?!\$)/u', $source)) {
            return true;
        }
    }

    return false;
}

function ag_get_archive_math_sources($archive)
{
    $sources = [];

    foreach (['text', 'content'] as $field) {
        if (isset($archive->$field) && trim((string) $archive->$field) !== '') {
            $sources[] = (string) $archive->$field;
        }
    }

    if (isset($archive->fields->subtitle) && trim((string) $archive->fields->subtitle) !== '') {
        $sources[] = (string) $archive->fields->subtitle;
    }

    return $sources;
}

function ag_render_math_assets($archive)
{
    if (!ag_archive_has_math($archive)) return;
    $plugins = \Helper::options()->plugins;
    $enhancedMath = isset($plugins['activated']['EnhancedMarkdown'])
        && \Helper::options()->plugin('EnhancedMarkdown')->math === '1'
        && strpos((string) $archive->text, '<!--markdown-->') === 0
        && !preg_match('/<\w+[^>]*>/u', substr((string) $archive->text, 15));
    if ($enhancedMath || !ag_archive_has_math($archive)) {
        return;
    }

    echo <<<'HTML'
  <script>
    window.MathJax = {
      tex: {
        inlineMath: {'[+]': [['$', '$'], ['\\(', '\\)']]},
        displayMath: [['$$', '$$'], ['\\[', '\\]']],
        processEscapes: true,
        processEnvironments: true
      },
      options: {
        ignoreHtmlClass: 'mathjax-ignore',
        processHtmlClass: 'mathjax-process',
        skipHtmlTags: {'[+]': ['script', 'noscript', 'style', 'textarea', 'pre', 'code']}
      },
      startup: {
        pageReady: () => {
          return MathJax.startup.defaultPageReady().then(() => {
            window.dispatchEvent(new Event('ag:math-ready'));
          });
        }
      }
    };

    window.agTypesetMath = function (elements) {
      if (!window.MathJax || typeof window.MathJax.typesetPromise !== 'function') {
        return Promise.resolve();
      }

      if (!elements) {
        return window.MathJax.typesetPromise();
      }

      const nodes = Array.isArray(elements) ? elements : [elements];
      return window.MathJax.typesetPromise(nodes);
    };
  </script>
  <script defer id="MathJax-script" src="https://cdn.jsdelivr.net/npm/mathjax@4/tex-chtml.js"></script>
HTML;
}

function ag_parse_json($raw)
{
    if (!is_string($raw) || trim($raw) === '') {
        return [];
    }

    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function ag_get_social_links()
{
    return ag_parse_json((string) ag_option('socialLinks', '[]'));
}

function ag_lower_text($value)
{
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }

    if (function_exists('mb_strtolower')) {
        return mb_strtolower($value, 'UTF-8');
    }

    return strtolower($value);
}

function ag_normalize_url_key($url)
{
    $url = trim((string) $url);
    if ($url === '') {
        return '';
    }

    if (strpos($url, 'special:') === 0) {
        return ag_lower_text($url);
    }

    $parts = parse_url($url);
    if ($parts === false) {
        return ag_lower_text(rtrim($url, '/'));
    }

    $path = $parts['path'] ?? '/';
    if ($path === '') {
        $path = '/';
    }
    if ($path !== '/') {
        $path = rtrim($path, '/');
    }

    $query = isset($parts['query']) && $parts['query'] !== '' ? '?' . $parts['query'] : '';

    return ag_lower_text($path . $query);
}

function ag_get_published_pages()
{
    static $pages = null;

    if ($pages !== null) {
        return $pages;
    }

    $pages = [];

    if (!class_exists('\Widget\Contents\Page\Rows')) {
        return $pages;
    }

    try {
        \Widget\Contents\Page\Rows::alloc()->to($widget);

        while ($widget->next()) {
            $permalink = trim((string) $widget->permalink);
            if ($permalink === '') {
                continue;
            }

            $pages[] = [
                'title' => trim((string) $widget->title),
                'slug' => trim((string) $widget->slug),
                'permalink' => $permalink,
                'template' => trim((string) $widget->template),
            ];
        }
    } catch (\Throwable $e) {
        return $pages;
    }

    return $pages;
}

function ag_get_special_nav_rules()
{
    return [
        'archive' => [
            'templates' => ['page-archive.php'],
            'slugs' => ['archive', 'archives', 'timeline'],
            'titles' => ['归档', '时间归档', 'Archive', 'Archives', 'Timeline'],
            'aliases' => [
                'special:archive',
                '/archive',
                '/archive.html',
                '/archives',
                '/archives.html',
                '/timeline',
                '/timeline.html',
                'archive',
                'archive.html',
                'archives',
                'archives.html',
                'timeline',
                'timeline.html',
            ],
        ],
        'friends' => [
            'templates' => ['page-links.php'],
            'slugs' => ['friends', 'links', 'friend-links', 'friendlink', 'friendlinks'],
            'titles' => ['友链', '友情链接', 'Friends', 'Links'],
            'aliases' => [
                'special:friends',
                'special:links',
                '/friends',
                '/friends.html',
                '/links',
                '/links.html',
                '/friend-links',
                '/friend-links.html',
                'friends',
                'friends.html',
                'links',
                'links.html',
                'friend-links',
                'friend-links.html',
            ],
        ],
    ];
}

function ag_match_special_nav_key($url)
{
    $raw = trim((string) $url);
    if ($raw === '') {
        return '';
    }

    $rawKey = ag_lower_text($raw);
    $urlKey = ag_normalize_url_key($raw);

    foreach (ag_get_special_nav_rules() as $key => $rule) {
        foreach ($rule['aliases'] as $alias) {
            $alias = trim((string) $alias);
            if ($alias === '') {
                continue;
            }

            if (strpos($alias, 'special:') === 0) {
                if ($rawKey === ag_lower_text($alias)) {
                    return $key;
                }
                continue;
            }

            if ($urlKey !== '' && $urlKey === ag_normalize_url_key($alias)) {
                return $key;
            }
        }
    }

    return '';
}

function ag_find_special_page($key)
{
    $rules = ag_get_special_nav_rules();
    if (empty($rules[$key])) {
        return [];
    }

    $templateSet = array_map('ag_lower_text', $rules[$key]['templates']);
    $slugSet = array_map('ag_lower_text', $rules[$key]['slugs']);
    $titleSet = array_map('ag_lower_text', $rules[$key]['titles']);

    $bestPage = [];
    $bestScore = -1;

    foreach (ag_get_published_pages() as $page) {
        $score = 0;
        $template = ag_lower_text($page['template'] ?? '');
        $slug = ag_lower_text($page['slug'] ?? '');
        $title = ag_lower_text($page['title'] ?? '');

        if ($template !== '' && in_array($template, $templateSet, true)) {
            $score = 300;
        }

        if ($slug !== '' && in_array($slug, $slugSet, true)) {
            $score = max($score, 200);
        }

        if ($title !== '' && in_array($title, $titleSet, true)) {
            $score = max($score, 100);
        }

        if ($score > $bestScore && !empty($page['permalink'])) {
            $bestPage = $page;
            $bestScore = $score;
        }
    }

    return $bestPage;
}

function ag_get_special_page_permalink($key)
{
    $page = ag_find_special_page($key);
    return !empty($page['permalink']) ? $page['permalink'] : '';
}

function ag_resolve_nav_url($url)
{
    $url = trim((string) $url);
    if ($url === '') {
        return '';
    }

    $specialKey = ag_match_special_nav_key($url);
    if ($specialKey === '') {
        return $url;
    }

    return ag_get_special_page_permalink($specialKey);
}

function ag_get_nav_links()
{
    $items = ag_parse_json((string) ag_option('extraNavLinks', '[]'));
    $links = [];
    $seen = [];

    foreach ($items as $item) {
        $name = trim((string) ($item['name'] ?? ''));
        $url = ag_resolve_nav_url($item['url'] ?? '');

        if ($name === '' || $url === '') {
            continue;
        }

        $key = ag_normalize_url_key($url);
        if ($key !== '' && isset($seen[$key])) {
            continue;
        }

        if ($key !== '') {
            $seen[$key] = true;
        }

        $item['name'] = $name;
        $item['url'] = $url;
        $links[] = $item;
    }

    return $links;
}

function ag_is_current_nav_link($archive, $url)
{
    $target = ag_normalize_url_key($url);
    if ($target === '') {
        return false;
    }

    $candidates = [];

    if (is_object($archive) && isset($archive->permalink)) {
        $candidates[] = (string) $archive->permalink;
    }

    if (!empty($_SERVER['REQUEST_URI'])) {
        $candidates[] = (string) $_SERVER['REQUEST_URI'];
    }

    foreach ($candidates as $candidate) {
        if (ag_normalize_url_key($candidate) === $target) {
            return true;
        }
    }

    return false;
}

function ag_document_title($archive)
{
    $siteTitle = trim((string) \Helper::options()->title);
    if ($archive->is('index')) return $siteTitle;
    if ($archive->is('post') || $archive->is('page')) {
        $pageTitle = html_entity_decode((string) $archive->title, ENT_QUOTES, 'UTF-8');
    } elseif ($archive->is('404')) {
        $pageTitle = '页面未找到';
    } else {
        $pageTitle = method_exists($archive, 'getArchiveTitle') ? (string) $archive->getArchiveTitle() : '文章列表';
        if ($archive->is('search')) $pageTitle = '搜索：' . $pageTitle;
        elseif ($archive->is('category')) $pageTitle = '分类：' . $pageTitle;
        elseif ($archive->is('tag')) $pageTitle = '标签：' . $pageTitle;
    }
    return ag_join_title_parts([$pageTitle, $siteTitle]);
}

function ag_join_title_parts($parts, $separator = ' - ')
{
    $clean = [];

    foreach ((array) $parts as $part) {
        $part = trim((string) $part);
        if ($part === '' || in_array($part, $clean, true)) {
            continue;
        }
        $clean[] = $part;
    }

    return implode($separator, $clean);
}

function ag_meta_description($archive)
{
    if ($archive->is('post') || $archive->is('page')) {
        if (isset($archive->fields->subtitle) && trim((string) $archive->fields->subtitle) !== '') {
            return trim((string) $archive->fields->subtitle);
        }
        $description = ag_excerpt_plain($archive, 140);
        return $description !== '' ? $description : (string) \Helper::options()->description;
    }
    return \Helper::options()->description;
}

function ag_excerpt_plain($archive, $length = 140)
{
    $text = ag_strip_math_expressions((string) $archive->text);
    $text = preg_replace('/\[LinksPlus[^\]]*\]/i', '', $text);
    $text = trim(strip_tags($text));
    $text = preg_replace('/\s+/u', ' ', $text);
    if (!is_string($text)) {
        $text = '';
    }
    if (function_exists('mb_substr')) {
        return mb_substr($text, 0, $length, 'UTF-8');
    }
    return substr($text, 0, $length);
}

function ag_strip_math_expressions($text)
{
    $clean = preg_replace([
        '/\$\$(?:.|[\r\n])*?\$\$/u',
        '/\\\\\[(?:.|[\r\n])*?\\\\\]/u',
        '/\\\\\((?:.|[\r\n])*?\\\\\)/u',
        '/\\\\begin\{([a-zA-Z*]+)\}(?:.|[\r\n])*?\\\\end\{\1\}/u',
        '/(?<!\$)\$(?!\$)(?:\\\\.|[^$\\\\\r\n])+(?<!\\\\)\$(?!\$)/u',
    ], ' ', (string) $text);

    if (!is_string($clean)) {
        $clean = (string) $text;
    }

    return str_replace(['$$', '$', '\(', '\)', '\[', '\]'], ' ', $clean);
}

function ag_get_cover($archive)
{
    if (isset($archive->fields->cover) && trim((string) $archive->fields->cover) !== '') {
        return trim((string) $archive->fields->cover);
    }
    return '';
}

function ag_get_favicon_url()
{
    $favicon = trim((string) ag_option('favicon', ''));
    if ($favicon === '') {
        return '';
    }

    return ag_normalize_media_url($favicon);
}

function ag_get_card_cover($archive)
{
    $cover = ag_get_cover($archive);
    if ($cover !== '') {
        return $cover;
    }

    return ag_extract_first_image_url($archive);
}

function ag_extract_first_image_url($archive)
{
    $patterns = [
        '/<img[^>]+src=[\"\']([^\"\']+)[\"\']/iu',
        '/!\[[^\]]*\]\(\s*<?([^\s)>]+)(?:\s+[\"\'][^\"\']*[\"\'])?\s*>?\)/u',
    ];

    $sources = [];

    if (isset($archive->text) && trim((string) $archive->text) !== '') {
        $sources[] = (string) $archive->text;
    }

    if (isset($archive->content) && trim((string) $archive->content) !== '') {
        $sources[] = (string) $archive->content;
    }

    foreach ($sources as $raw) {
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $raw, $matches) && !empty($matches[1])) {
                return ag_normalize_media_url($matches[1]);
            }
        }
    }

    return '';
}

function ag_normalize_media_url($url)
{
    $url = trim(html_entity_decode((string) $url, ENT_QUOTES, 'UTF-8'));
    if ($url === '') {
        return '';
    }

    if (preg_match('/^(https?:)?\/\//i', $url) || strpos($url, 'data:') === 0) {
        return $url;
    }

    if ($url[0] === '/') {
        return $url;
    }

    $rootUrl = rtrim((string) \Helper::options()->rootUrl, '/');
    return $rootUrl . '/' . ltrim($url, '/');
}

function ag_estimated_reading_time($text)
{
    $clean = trim(strip_tags((string) $text));
    $length = function_exists('mb_strlen') ? mb_strlen($clean, 'UTF-8') : strlen($clean);
    return max(1, (int) ceil($length / 450));
}

function ag_current_archive_title($archive)
{
    ob_start();
    $archive->archiveTitle([
        'category' => _t('分类：%s'),
        'search' => _t('搜索：%s'),
        'tag' => _t('标签：%s'),
        'author' => _t('作者：%s')
    ], '', '');
    $title = trim(ob_get_clean());
    return $title !== '' ? $title : _t('文章列表');
}

function ag_current_archive_subtitle($archive)
{
    if ($archive->is('category')) {
        return _t('这里是当前分类下的全部文章，支持分类独立分页。');
    }
    if ($archive->is('search')) {
        return _t('以下是与你搜索关键词相关的内容。');
    }
    if ($archive->is('tag')) {
        return _t('按标签聚合的文章列表。');
    }
    return _t('按时间、标签、分类或搜索条件浏览内容。');
}

function ag_primary_category_name($archive)
{
    $categories = $archive->categories ?? [];
    if (!empty($categories)) {
        $first = $categories[0];
        if (is_array($first) && isset($first['name'])) {
            return $first['name'];
        }
        if (is_object($first) && isset($first->name)) {
            return $first->name;
        }
    }
    return _t('未分类');
}

function ag_render_post_card($archive)
{
    $cover = ag_get_card_cover($archive);
    $subtitle = isset($archive->fields->subtitle) ? trim((string) $archive->fields->subtitle) : '';
    ?>
    <article class="post-card">
      <a class="post-card-link" href="<?php $archive->permalink(); ?>">
        <div class="post-visual<?php if (!$cover): ?> no-image<?php endif; ?>">
          <?php if ($cover): ?>
            <img src="<?php echo htmlspecialchars($cover, ENT_QUOTES); ?>" alt="" loading="lazy" decoding="async">
          <?php else: ?>
            <span class="cover-glyph" aria-hidden="true"><?php echo ag_icon('book'); ?></span>
          <?php endif; ?>
          <?php if (isset($archive->fields->featured) && $archive->fields->featured == '1'): ?>
            <span class="featured-label">精选</span>
          <?php endif; ?>
        </div>
        <div class="post-card-body">
          <div class="meta-row"><span><?php echo htmlspecialchars(ag_primary_category_name($archive)); ?></span><span class="meta-dot" aria-hidden="true">·</span><time datetime="<?php $archive->date('c'); ?>"><?php $archive->date('Y.m.d'); ?></time></div>
          <h3><?php echo htmlspecialchars(html_entity_decode((string) $archive->title, ENT_QUOTES, 'UTF-8')); ?></h3>
          <p><?php echo htmlspecialchars($subtitle !== '' ? $subtitle : ag_excerpt_plain($archive, 90)); ?></p>
          <div class="post-card-foot"><span><?php echo ag_estimated_reading_time($archive->text); ?> 分钟阅读</span><span class="read-link">阅读全文 <?php echo ag_icon('arrow-right'); ?></span></div>
        </div>
      </a>
    </article>
    <?php
}

function ag_render_pagination($archive)
{
    echo '<nav class="pagination-wrap">';
    $archive->pageNav(_t('上一页'), _t('下一页'), 2, '…', ['wrapTag' => 'ul', 'itemTag' => 'li', 'textTag' => 'span', 'currentClass' => 'is-current']);
    echo '</nav>';
}

function ag_render_social_links()
{
    foreach (ag_get_social_links() as $item) {
        if (empty($item['url'])) {
            continue;
        }
        $name = $item['name'] ?? 'Link';
        $icon = $item['icon'] ?? 'link';
        echo '<a class="social-link" href="' . htmlspecialchars($item['url']) . '" target="_blank" rel="noopener noreferrer" aria-label="' . htmlspecialchars($name) . '">';
        echo '<span class="icon-wrap">' . ag_icon($icon) . '</span>';
        echo '<span>' . htmlspecialchars($name) . '</span>';
        echo '</a>';
    }
}

function ag_icon($name)
{
    $icons = [
        'search' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 4.5 4.5"/></svg>',
        'arrow-right' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12h16m-6-6 6 6-6 6"/></svg>',
        'close' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="m6 6 12 12M18 6 6 18"/></svg>',
        'monitor' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="13" rx="3"/><path d="M8 21h8m-4-4v4"/></svg>',
        'book' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><path d="M12 5c-3-2-6-2-9-1v15c3-1 6-1 9 1 3-2 6-2 9-1V4c-3-1-6-1-9 1Zm0 0v15"/></svg>',
        'theme' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 12.79A9 9 0 1 1 11.21 3c0 .34 0 .68.05 1.01A7 7 0 0 0 20 12c.34.05.68.05 1 .79Z"/></svg>',
        'sun' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2.5M12 19.5V22M4.93 4.93l1.77 1.77M17.3 17.3l1.77 1.77M2 12h2.5M19.5 12H22M4.93 19.07l1.77-1.77M17.3 6.7l1.77-1.77"/></svg>',
        'moon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3c0 .34 0 .68.05 1.01A7 7 0 0 0 20 12c.34.05.68.05 1 .79Z"/></svg>',
        'menu' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16M4 12h16M4 17h16"/></svg>',
        'arrow-up' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m6 15 6-6 6 6"/><path d="M12 9v10"/></svg>',
        'github' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 .5C5.65.5.5 5.67.5 12.05c0 5.1 3.3 9.42 7.88 10.95.58.1.79-.26.79-.57v-2.02c-3.2.7-3.88-1.38-3.88-1.38-.53-1.35-1.28-1.71-1.28-1.71-1.04-.72.08-.71.08-.71 1.15.08 1.76 1.19 1.76 1.19 1.02 1.76 2.67 1.25 3.32.96.1-.75.4-1.25.72-1.54-2.56-.3-5.25-1.29-5.25-5.73 0-1.27.46-2.31 1.2-3.12-.12-.3-.52-1.52.12-3.17 0 0 .98-.31 3.2 1.19a10.9 10.9 0 0 1 5.83 0c2.22-1.5 3.2-1.19 3.2-1.19.64 1.65.24 2.87.12 3.17.75.81 1.2 1.85 1.2 3.12 0 4.45-2.7 5.42-5.28 5.72.41.35.77 1.04.77 2.1v3.11c0 .31.21.68.8.57A11.56 11.56 0 0 0 23.5 12.05C23.5 5.67 18.35.5 12 .5Z"/></svg>',
        'x' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.9 2H22l-6.77 7.74L23.2 22h-6.25l-4.9-6.9L6.02 22H2.9l7.23-8.26L.8 2h6.4l4.43 6.25L18.9 2Zm-1.1 18h1.73L6.26 3.9H4.4L17.8 20Z"/></svg>',
        'telegram' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M9.78 18.65c-.35 0-.29-.13-.41-.47l-1.05-3.46 8.1-5.13c.38-.23.73-.1.45.16l-6.56 5.92-.24 3.58c.35 0 .5-.16.7-.35l1.7-1.65 3.54 2.61c.66.37 1.12.18 1.29-.62l2.33-10.98c.25-.98-.37-1.42-1-1.14L4.78 12.5c-.95.39-.94.92-.17 1.16l3.61 1.13 8.36-5.28c.4-.24.76-.1.46.17"/></svg>',
        'mail' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/></svg>',
        'rss' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 11a9 9 0 0 1 9 9"/><path d="M4 4a16 16 0 0 1 16 16"/><circle cx="5" cy="19" r="1.5" fill="currentColor" stroke="none"/></svg>',
        'bilibili' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 5 9 3m6 2 2-2"/><rect x="3" y="6" width="18" height="13" rx="3"/><path d="M9.5 11.5v2m5-2v2"/><path d="M8 16c1 .8 2.3 1.2 4 1.2s3-.4 4-1.2"/></svg>',
        'youtube' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M21.58 7.2a2.98 2.98 0 0 0-2.1-2.1C17.7 4.6 12 4.6 12 4.6s-5.7 0-7.48.5a2.98 2.98 0 0 0-2.1 2.1C1.9 9 1.9 12 1.9 12s0 3 .52 4.8a2.98 2.98 0 0 0 2.1 2.1c1.78.5 7.48.5 7.48.5s5.7 0 7.48-.5a2.98 2.98 0 0 0 2.1-2.1c.52-1.8.52-4.8.52-4.8s0-3-.52-4.8ZM10 15.5v-7l6 3.5-6 3.5Z"/></svg>',
        'link' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10 14 6 18a3 3 0 0 1-4-4l4-4a3 3 0 0 1 4 0"/><path d="M14 10 18 6a3 3 0 0 1 4 4l-4 4a3 3 0 0 1-4 0"/><path d="M8 12h8"/></svg>',
    ];

    return $icons[$name] ?? $icons['link'];
}


function ag_render_adjacent_post_nav($archive)
{
    ob_start();
    $hasOutput = false;
    ?>
    <section class="post-nav-grid">
      <div class="glass-card nav-card nav-prev">
        <?php
        if (is_object($archive) && method_exists($archive, 'thePrev')) {
            $archive->thePrev('%s', '<span class="muted">没有更早的文章</span>');
            $hasOutput = true;
        } elseif (function_exists('thePrev')) {
            thePrev('%s', '<span class="muted">没有更早的文章</span>');
            $hasOutput = true;
        } else {
            echo '<span class="muted">没有更早的文章</span>';
        }
        ?>
      </div>
      <div class="glass-card nav-card nav-next">
        <?php
        if (is_object($archive) && method_exists($archive, 'theNext')) {
            $archive->theNext('%s', '<span class="muted">没有更新的文章</span>');
            $hasOutput = true;
        } elseif (function_exists('theNext')) {
            theNext('%s', '<span class="muted">没有更新的文章</span>');
            $hasOutput = true;
        } else {
            echo '<span class="muted">没有更新的文章</span>';
        }
        ?>
      </div>
    </section>
    <?php
    echo ob_get_clean();
}

function ag_threaded_comments_script_safe()
{
    if (class_exists('\Typecho\Utils\Helper') && method_exists('\Typecho\Utils\Helper', 'threadedCommentsScript')) {
        \Typecho\Utils\Helper::threadedCommentsScript();
        return;
    }

    if (class_exists('\Helper') && method_exists('\Helper', 'threadedCommentsScript')) {
        \Helper::threadedCommentsScript();
        return;
    }

    if (function_exists('threadedCommentsScript')) {
        threadedCommentsScript();
    }
}

function ag_comment_item($comments, $options)
{
    ?>
    <li id="li-<?php $comments->theId(); ?>" class="comment-item">
      <article id="<?php $comments->theId(); ?>" class="comment-card">
        <div class="comment-avatar"><?php $comments->gravatar('48', '', '', _t('匿名')); ?></div>
        <div class="comment-body">
          <div class="comment-head">
            <strong><?php $comments->author(); ?></strong>
            <time><?php $comments->date($options->dateFormat); ?></time>
          </div>
          <div class="comment-content"><?php $comments->content(); ?></div>
          <div class="comment-actions"><?php $comments->reply(_t('回复')); ?></div>
        </div>
      </article>
      <?php if ($comments->children): ?>
        <div class="comment-children"><?php $comments->threadedComments($options); ?></div>
      <?php endif; ?>
    </li>
    <?php
}


/** Published-only statistics for the personal-space card. */
function lg_site_stats()
{
    static $stats = null;
    if ($stats === null) {
        $db = \Typecho\Db::get();
        $posts = $db->fetchRow($db->select(['COUNT(cid)' => 'total'])->from('table.contents')->where('type = ?', 'post')->where('status = ?', 'publish')->where('created <= ?', time()));
        $categories = $db->fetchRow($db->select(['COUNT(mid)' => 'total'])->from('table.metas')->where('type = ?', 'category')->where('count > ?', 0));
        $stats = ['posts' => (int) ($posts['total'] ?? 0), 'categories' => (int) ($categories['total'] ?? 0)];
    }
    return $stats;
}

/** Render only registration data supplied by this site's theme settings. */
function lg_render_registration()
{
    $items = [
        [
            'text' => trim((string) ag_option('beian', '')),
            'url' => trim((string) ag_option('beianUrl', 'https://beian.miit.gov.cn/')),
            'icon' => '',
        ],
        [
            'text' => trim((string) ag_option('publicSecurityNumber', '')),
            'url' => trim((string) ag_option('publicSecurityUrl', '')),
            'icon' => trim((string) ag_option('publicSecurityIcon', '')),
        ],
    ];
    $items = array_filter($items, static function ($item) { return $item['text'] !== ''; });
    if (!$items) return;

    echo '<div class="footer-registration">';
    foreach ($items as $item) {
        $hasLink = preg_match('~^https?://~i', $item['url']) === 1;
        echo $hasLink
            ? '<a class="security-link" href="' . htmlspecialchars($item['url'], ENT_QUOTES) . '" target="_blank" rel="noopener noreferrer">'
            : '<span class="security-link">';
        $icon = ag_normalize_media_url($item['icon']);
        if ($icon !== '' && preg_match('~^(https?://|/(?!/))~i', $icon)) {
            echo '<img src="' . htmlspecialchars($icon, ENT_QUOTES) . '" alt="" width="14" height="14" loading="lazy">';
        }
        echo htmlspecialchars($item['text'], ENT_QUOTES);
        echo $hasLink ? '</a>' : '</span>';
    }
    echo '</div>';
}

/** Theme-aware avatar shared by the header and profile. */
function lg_avatar($class = '')
{
    $light = trim((string) ag_option('logoLight', ''));
    $dark = trim((string) ag_option('logoDark', ''));
    echo '<span data-initial="' . htmlspecialchars(ag_option('brandInitial', 'L'), ENT_QUOTES) . '" class="avatar ' . htmlspecialchars($class, ENT_QUOTES) . ($dark !== '' ? ' has-dark-logo' : '') . '">';
    if ($light !== '') {
        echo '<img class="logo-light" src="' . htmlspecialchars($light, ENT_QUOTES) . '" alt="" decoding="async">';
    } else {
        echo '<span class="brand-mark">' . htmlspecialchars(ag_option('brandInitial', 'L')) . '</span>';
    }
    if ($dark !== '') echo '<img class="logo-dark" src="' . htmlspecialchars($dark, ENT_QUOTES) . '" alt="" decoding="async">';
    echo '</span>';
}


/** Read only approved, public friend data; never alter plugin state or licensing. */
function lg_public_friends()
{
    try {
        $db = \Typecho\Db::get();
        return $db->fetchAll($db->select('name', 'url', 'image', 'description')->from('table.links')->where('state = ?', 1)->order('order', \Typecho\Db::SORT_ASC));
    } catch (\Throwable $error) {
        return [];
    }
}

function lg_render_friend_cards($items)
{
    echo '<div class="friend-grid">';
    foreach ($items as $item) {
        $url = trim((string) ($item['url'] ?? ''));
        $name = trim((string) ($item['name'] ?? ''));
        if ($name === '' || !preg_match('~^https?://~i', $url)) continue;
        echo '<a class="friend-card" href="' . htmlspecialchars($url, ENT_QUOTES) . '" target="_blank" rel="noopener noreferrer"><div class="friend-avatar">';
        if (!empty($item['image']) && preg_match('~^(https?://|/)~i', $item['image'])) {
            echo '<img src="' . htmlspecialchars($item['image'], ENT_QUOTES) . '" alt="" loading="lazy" decoding="async">';
        } else {
            echo htmlspecialchars(mb_substr($name, 0, 1, 'UTF-8'));
        }
        echo '</div><div class="friend-meta"><strong>' . htmlspecialchars($name) . '</strong><p>' . htmlspecialchars($item['description'] ?? '') . '</p></div></a>';
    }
    echo '</div>';
}

/** Keep plugin output; replace only shortcodes the plugin leaves unresolved. */
function lg_friends_content($archive)
{
    ob_start();
    $archive->content();
    $html = ob_get_clean();
    $html = preg_replace('/<p>\s*(\[LinksPlus\s*\/\])\s*<\/p>/i', '$1', $html);
    $html = preg_replace('/<p>\s*\[LinksPlus\s+OnlyForm\s*\/\]\s*<\/p>/i', '', $html);
    $pattern = '/\[LinksPlus\s*\/\]/i';
    if (preg_match($pattern, $html)) {
        ob_start();
        $items = lg_public_friends();
        if ($items) lg_render_friend_cards($items);
        else echo '<div class="empty-state friends-empty"><span class="empty-icon">' . ag_icon('link') . '</span><h2>好的相遇，值得慢慢等待。</h2><p>这里收集有趣的人，以及他们认真记录的世界。</p></div>';
        $cards = ob_get_clean();
        $html = preg_replace_callback($pattern, static function () use ($cards) { return $cards; }, $html);
    }
    $html = preg_replace('/\[LinksPlus\s+OnlyForm\s*\/\]/i', '', $html);
    echo $html;
}
