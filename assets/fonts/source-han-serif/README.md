# 思源宋体界面字体

这些 WOFF2 分片基于 Adobe 的 **Source Han Serif CN VF 2.003**，仅在后台选择「界面字体 → 思源宋体」时加载。文章正文继续使用原有系统字体，代码继续使用等宽字体。

字体按 Unicode 范围拆分；常用界面字符和西文合并为 `common-*.woff2`，其他字符由浏览器按需请求对应分片。文件名带有内容哈希，更新字库后可避免旧字体缓存混用。字重保留原字体的 250–900 可变范围。资源均由博客本地提供，不依赖第三方字体 CDN。

字体来源：

- <https://github.com/adobe-fonts/source-han-serif>
- <https://raw.githubusercontent.com/adobe-fonts/source-han-serif/release/Variable/WOFF2/OTF/Subset/SourceHanSerifCN-VF.otf.woff2>

字体及其分片遵循独立的 **SIL Open Font License 1.1**，完整许可见 [OFL.txt](OFL.txt)。字体保留原始版权信息；修改后的字体主名称为 `LumaGlass Serif`，以遵守原字体的 Reserved Font Name 条款。

主题维护者可在安装 `fonttools` 和 `brotli` 后重新生成：

```bash
python tools/build-interface-font.py /path/to/SourceHanSerifCN-VF.otf.woff2 \
  --license /path/to/OFL.txt
```

这只是维护时使用的工具；已生成的主题资源可直接部署，无需在博客服务器安装这些 Python 包。
