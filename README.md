# 澄光 · LumaGlass

一个为阅读和个人记录设计的 Typecho 1.3 主题。基于 Sandro 的 [TypechoGlass](https://github.com/Sandro-Z/TypechoGlass) 分叉，保留 PHP 模板、主题设置、文章字段和插件接口，重新设计页面结构、材质、排版与交互。

## 设计

- 雾蓝与浅紫的静态背景，独立的深色材质，支持自定义背景图片。
- 悬浮导航、个人空间卡片、阅读面板采用不同的模糊强度与透明度；正文优先保证对比度。
- 系统字体、适合中文阅读的行距、克制的高光边缘和阴影。
- 首页文章列表、文章目录、关于页、完整时间归档、友链、分类、标签、搜索、404。
- 搜索面板支持键盘聚焦、Esc 关闭和 ⌘/Ctrl+K，颜色模式支持浅色、深色与跟随系统。
- 移动导航、即时按下反馈、可中断的搜索过渡；尊重减少动画、减少透明度和增强对比度偏好。
- 无 npm 构建、无额外运行时依赖。天气使用 Open-Meteo，并缓存 30 分钟；请求失败时保留已有缓存。

## 安装与迁移

1. 将整个 `LumaGlass` 文件夹放到 Typecho 的 `usr/themes/`。
2. 在后台「控制台 → 外观」启用 **LumaGlass**。
3. 打开主题设置，检查首页文案、Logo、社交链接、背景、备案信息和功能开关，然后保存。

也可以从 fork 的主题分支直接安装：

```bash
git clone https://github.com/LianjinSe/LumaGlass.git LumaGlass
```

将克隆得到的 `LumaGlass` 文件夹放入 `usr/themes/`。fork 仅保留默认分支 [`lumaglass`](https://github.com/LianjinSe/LumaGlass/tree/lumaglass)，克隆后即可使用 LumaGlass 主题。

首次使用时，没有单独设置的项目会读取 `theme:TypechoGlass` 中的原有设置，不修改该设置记录。保存后使用 LumaGlass 自己的主题配置；可分别维护两套主题。文章自定义字段保持 `cover`、`subtitle`、`featured`、`disableToc`、`linksJson`，无需迁移内容。

ICP备案号、公安备案号、公安备案查询链接和图标均默认为空，由站点管理员在后台「控制台 → 外观 → 主题设置」填写。备案号留空时隐藏对应信息；查询链接留空时仅显示文字；图标留空时不显示图标。ICP备案查询链接默认使用通用的官方查询首页，也可修改或清空。

首页附注、侧栏附注和页脚文案也由主题设置填写，默认留空并隐藏。已有设置继续从站点数据库读取，主题源码不保存本站的备案号或专属查询地址。

归档页面选择「时间归档页」，友链页面选择「友情链接页」。导航自动读取独立页面，并对额外导航中的归档、友链地址去重。

## 插件与友链

保留 Typecho 的 `header()`、`footer()`、正文和评论钩子，可继续使用 EnhancedMarkdown、TinyMCE8、AdminBeautify、FourSeasons 等插件。文章公式按内容加载 MathJax；可由 EnhancedMarkdown 处理的纯 Markdown 公式交给插件，避免重复加载两套渲染器。

友链页保留 Links Plus 的正常输出。如果插件留下未展开的 `[LinksPlus/]`，主题仅从已有 `links` 表读取 `state=1` 的公开友链，用自己的卡片展示。未启用或待审核的数据不会显示。主题不修改插件的许可、配置或审核状态。

也可在页面的 `linksJson` 字段填入：

```json
[
  {
    "name": "Typecho",
    "url": "https://typecho.org/",
    "description": "记录生活，分享想法。",
    "image": ""
  }
]
```

插件申请表的可用性仍由插件决定；若申请表短代码未展开，主题不会伪造申请功能，访客可使用已配置的邮件链接联系博主。

## 文件

| 位置 | 职责 |
| --- | --- |
| `header.php` / `footer.php` | 导航、搜索、资源、备案与插件钩子 |
| `index.php` / `sidebar.php` | 首页、个人空间与侧栏 |
| `post.php` / `page.php` / `archive.php` | 文章、页面与列表 |
| `page-archive.php` / `page-links.php` | 时间归档与友链 |
| `functions.php` | 设置、继承、文章卡片及公共逻辑 |
| `assets/css/tokens.css` | 颜色、材质、阴影 |
| `assets/css/base.css` | 布局、响应式、无障碍偏好 |
| `assets/css/components.css` | 控件与搜索面板 |
| `assets/css/markdown.css` / `pages.css` | 正文与独立页面 |
| `assets/js/theme.js` / `main.js` / `toc.js` | 颜色模式、交互、天气与目录 |

资源 URL 附带文件修改时间，修改后无需前端编译。

## 验证

开发时使用独立数据库副本和仅监听回环地址的 PHP 预览服务验证页面，未切换正式博客主题。验证覆盖真实文章、独立页面、搜索空结果、归档、友链状态、RSS、桌面与手机布局、搜索焦点和关闭、颜色模式、禁用 JavaScript、减少动画、图片失败及长文本。

安装到 Typecho 后，可运行站点设置回归检查；测试仅修改当前 PHP 进程中的选项，不写入站点数据库：

```bash
php tests/site-settings.php /path/to/typecho
```

检查包括备案字段的空默认值、留空隐藏、已填写链接和图标、文本转义，以及首页、侧栏和页脚附注的配置与显示。

## 来源与许可

- 上游：Sandro-Z/TypechoGlass。
- 分叉基线：`01d99a0e47f199f95273d44585adb5860b9b9ddf`。
- 新主题版本：1.0.0。
- 沿用上游 GPL-3.0 许可，完整许可保存在 `LICENSE`。
- 此目录保留上游 Git 历史，开发分支为 `lumaglass`。`upstream` 指向原主题仓库，`origin` 指向 [LianjinSe/LumaGlass](https://github.com/LianjinSe/LumaGlass) fork。
