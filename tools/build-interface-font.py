#!/usr/bin/env python3
"""Build local, unicode-ranged UI webfonts from Adobe's Source Han Serif CN VF.

Optional maintainer tooling: pip install fonttools brotli
The theme serves the generated assets directly and needs no font tooling at runtime.
"""

import argparse
import copy
import hashlib
import json
import multiprocessing
import re
from pathlib import Path

from fontTools import subset
from fontTools.ttLib import TTFont

FAMILY = "LumaGlass Serif"
FONT = None
OUTPUT = None


def ranges(codepoints):
    values = sorted(codepoints)
    result = []
    start = previous = values[0]
    for value in values[1:]:
        if value == previous + 1:
            previous = value
            continue
        result.append(f"U+{start:X}" if start == previous else f"U+{start:X}-{previous:X}")
        start = previous = value
    result.append(f"U+{start:X}" if start == previous else f"U+{start:X}-{previous:X}")
    return ",".join(result)


def build_chunk(item):
    filename, codepoints = item
    font = copy.deepcopy(FONT)
    options = subset.Options()
    options.layout_features = ["*"]
    options.name_IDs = ["*"]
    options.name_languages = ["*"]
    options.name_legacy = True
    sub = subset.Subsetter(options=options)
    sub.populate(unicodes=codepoints)
    sub.subset(font)
    font.flavor = "woff2"
    font.save(OUTPUT / filename)
    font.close()
    digest = hashlib.sha256((OUTPUT / filename).read_bytes()).hexdigest()[:12]
    versioned = f"{Path(filename).stem}-{digest}.woff2"
    (OUTPUT / filename).rename(OUTPUT / versioned)
    filename = versioned
    return {"file": filename, "characters": len(codepoints), "unicode_range": ranges(codepoints), "bytes": (OUTPUT / filename).stat().st_size}


def main():
    global FONT, OUTPUT
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("source", type=Path)
    parser.add_argument("--license", required=True, type=Path)
    parser.add_argument("--output", type=Path, default=Path(__file__).resolve().parents[1] / "assets/fonts/source-han-serif")
    parser.add_argument("--jobs", type=int, default=2)
    args = parser.parse_args()
    OUTPUT = args.output
    OUTPUT.mkdir(parents=True, exist_ok=True)
    FONT = TTFont(args.source, recalcTimestamp=False)
    FONT.ensureDecompiled()
    characters = set(FONT.getBestCmap())
    # Give derivative subsets a distinct primary name; preserve copyright/OFL records.
    for record in FONT["name"].names:
        if record.nameID in [0, 7, 8, 9, 10, 11, 12, 13, 14]:
            continue
        value = record.toUnicode()
        if record.nameID in [1, 4, 16, 21]:
            value = FAMILY
        else:
            value = re.sub(r"Source Han Serif(?: CN| SC)?(?: VF)?", FAMILY, value)
            value = re.sub(r"SourceHanSerif(?:CN|SC)?(?:VF)?", "LumaGlassSerif", value)
        record.string = value.encode(record.getEncoding())
    theme = Path(__file__).resolve().parents[1]
    ui = "".join(path.read_text() for path in sorted(theme.glob("*.php"))) + (theme / "README.md").read_text()
    common = set(ord(char) for char in re.findall(r"[\u3400-\u9fff]", ui))
    common.update(ord(char) for char in "中文文章博客开发手记记录生活学习想法字体显示页面标题分类标签导航按钮菜单阅读浅色深色天气日期时间")
    common.update(range(0x0000, 0x0400))
    common.update(range(0x2000, 0x2100))
    common.update(range(0x3000, 0x3040))
    common.update(range(0xFF00, 0xFFF0))
    common &= characters
    remaining = characters - common
    chunks = [(f"u{block:05x}.woff2", sorted(cp for cp in remaining if cp // 512 == block)) for block in sorted({cp // 512 for cp in remaining})]
    chunks.append(("common.woff2", sorted(common)))
    with multiprocessing.get_context("fork").Pool(max(1, min(args.jobs, 4))) as pool:
        built = pool.map(build_chunk, chunks)
    axes = [(axis.minValue, axis.maxValue) for axis in FONT["fvar"].axes if axis.axisTag == "wght"]
    low, high = axes[0]
    css = ["/* Source Han Serif-derived subsets; SIL OFL 1.1. See OFL.txt and README.md. */"]
    for entry in built:
        css.append(f'''@font-face {{
  font-family: "{FAMILY}";
  src: url("{entry['file']}") format("woff2");
  font-style: normal;
  font-weight: {low:g} {high:g};
  font-display: swap;
  unicode-range: {entry['unicode_range']};
}}''')
    css.append(f'''html[data-interface-font="source-han-serif"] {{
  --font-interface: "{FAMILY}", "Songti SC", "SimSun", serif;
}}''')
    (OUTPUT / "font.css").write_text("\n\n".join(css) + "\n")
    (OUTPUT / "OFL.txt").write_bytes(args.license.read_bytes())
    manifest = {"family": FAMILY, "source": "Adobe Source Han Serif CN VF, 2.003", "source_sha256": hashlib.sha256(args.source.read_bytes()).hexdigest(), "characters": len(characters), "weight_range": [low, high], "chunks": built}
    (OUTPUT / "manifest.json").write_text(json.dumps(manifest, indent=2) + "\n")
    print(f"Built {len(built)} font files; {len(characters)} characters; {sum(item['bytes'] for item in built):,} bytes total; common subset: {built[-1]['bytes']:,} bytes.")


if __name__ == "__main__":
    main()
