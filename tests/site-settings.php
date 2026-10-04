<?php
/** Run from an installed theme: php tests/site-settings.php [typecho-root] */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
$typechoRoot = $argv[1] ?? dirname(__DIR__, 4);
if (!is_file($typechoRoot . '/config.inc.php')) {
    fwrite(STDERR, "Pass the path to an installed Typecho site.\n");
    exit(1);
}
$_SERVER['HTTP_HOST'] = 'example.test';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SERVER_PORT'] = '80';
define('__TYPECHO_ROOT_URL__', 'https://example.test');
define('__TYPECHO_SITE_URL__', 'https://example.test');
define('__TYPECHO_DEBUG__', true);
require $typechoRoot . '/config.inc.php';
\Widget\Init::alloc();
$options = \Helper::options();
// Change only this process's Options object; do not write the site's database.
$options->{'theme:TypechoGlass'} = '{}';
$options->rootUrl = 'https://example.test';
$options->siteUrl = 'https://example.test';
$options->title = '示例博客';
$options->socialLinks = '[]';
require_once dirname(__DIR__) . '/functions.php';

function check($condition, $message)
{
    if (!$condition) throw new \RuntimeException($message);
}

function markup($callback)
{
    ob_start();
    $callback();
    return ob_get_clean();
}

function xpathFor($html)
{
    $document = new \DOMDocument();
    $previous = libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
    return new \DOMXPath($document);
}

$form = new \Typecho\Widget\Helper\Form();
themeConfig($form);
foreach (['beian', 'publicSecurityNumber', 'publicSecurityUrl', 'publicSecurityIcon'] as $name) {
    check($form->getInput($name)->input->getAttribute('value') === '', $name . ' must have an empty default');
}
foreach (['beianUrl', 'footerText', 'heroFootnote', 'sidebarNote'] as $name) {
    check($form->getInput($name) !== null, $name . ' must be editable in the Typecho settings form');
}
echo "PASS: native Typecho settings fields and empty registration defaults\n";

foreach (['beian', 'beianUrl', 'publicSecurityNumber', 'publicSecurityUrl', 'publicSecurityIcon'] as $name) $options->$name = '';
check(markup('lg_render_registration') === '', 'Empty settings must render no registration block');

$options->beian = '示例 ICP 备案号';
$options->publicSecurityNumber = '示例公安备案号';
$plain = xpathFor(markup('lg_render_registration'));
check($plain->query('//a')->length === 0, 'Empty links must render plain text');
check($plain->query('//img')->length === 0, 'Empty icon must render no image');
check(strpos($plain->document->textContent, '示例 ICP 备案号') !== false, 'Configured ICP label must render');
check(strpos($plain->document->textContent, '示例公安备案号') !== false, 'Configured public-security label must render');

$options->beianUrl = 'https://example.test/icp';
$options->publicSecurityUrl = 'https://example.test/security?item=demo&source=blog';
$options->publicSecurityIcon = '/images/security.svg';
$linked = xpathFor(markup('lg_render_registration'));
check($linked->query('//a')->length === 2, 'Configured links must render');
check($linked->query('//img')->length === 1, 'Configured icon must render');
check($linked->query('//a')->item(1)->getAttribute('href') === $options->publicSecurityUrl, 'Query-string URL must be preserved');

$options->beian = '<strong>示例备案号</strong>';
$options->beianUrl = 'javascript:alert(1)';
$escaped = xpathFor(markup('lg_render_registration'));
check($escaped->query('//strong')->length === 0, 'Registration labels must be escaped');
check($escaped->query('//a')->length === 1, 'Non-HTTP links must not be clickable');
echo "PASS: registration visibility, configured links/icons, escaping and invalid links\n";

class SettingsPageFixture
{
    public function is($type, $slug = null) { return $type === 'index'; }
    public function have() { return false; }
    public function footer() {}
    public function need($file) { if ($file !== 'header.php') require dirname(__DIR__) . '/' . $file; }
    public function render() { return markup(function () { $this->need('index.php'); }); }
}

$options->heroFootnote = '';
$options->sidebarNote = '';
$options->footerText = '';
$emptyCopy = xpathFor((new SettingsPageFixture())->render());
check($emptyCopy->query('//*[contains(concat(" ",normalize-space(@class)," ")," hero-footnote ")]')->length === 0, 'Empty home footnote must be hidden');
check($emptyCopy->query('//*[contains(concat(" ",normalize-space(@class)," ")," sidebar-note ")]')->length === 0, 'Empty sidebar note must be hidden');
check($emptyCopy->query('//*[contains(concat(" ",normalize-space(@class)," ")," footer-main ")]//p')->length === 0, 'Empty footer copy must be hidden');

$options->heroFootnote = '首页附注 <em>示例</em>';
$options->sidebarNote = "侧栏第一行\n第二行";
$options->footerText = '页脚文案 <b>示例</b>';
$filledCopy = xpathFor((new SettingsPageFixture())->render());
check($filledCopy->query('//*[contains(concat(" ",normalize-space(@class)," ")," hero-footnote ")]')->length === 1, 'Configured home footnote must render');
check($filledCopy->query('//*[contains(concat(" ",normalize-space(@class)," ")," sidebar-note ")]//br')->length === 1, 'Sidebar note must preserve line breaks');
check($filledCopy->query('//em|//b')->length === 0, 'Site copy must be escaped');
echo "PASS: optional site copy is configurable, escaped and hidden when empty\n";
