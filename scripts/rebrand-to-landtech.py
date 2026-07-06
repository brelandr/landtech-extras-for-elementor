#!/usr/bin/env python3
"""One-off rebrand helper: run once from repo root via `python3 scripts/rebrand-to-landtech.py`."""
from __future__ import annotations

import os
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]

MAINT_COMMENT = "// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license."

TEXT_EXTENSIONS = {
    ".php",
    ".js",
    ".css",
    ".scss",
    ".json",
    ".txt",
    ".md",
    ".xml",
    ".pot",
    ".html",
}


def skip_dir(name: str) -> bool:
    if name.startswith(".zip-pack"):
        return True
    if name in {".git", "node_modules", "vendor"}:
        return True
    return False


def should_skip_path(rel: Path) -> bool:
    for part in rel.parts:
        if part.startswith(".zip-pack") or part in {"node_modules", "vendor", ".git"}:
            return True
    return False


def apply_string_replacements(content: str) -> str:
    rules = [
        ("ElementorExtras_Landtech_License_Client_SDK", "LandTech_Extras_License_Client_SDK"),
        ("ElementorExtras_License_Cron", "LandTech_Extras_License_Cron"),
        ("ElementorExtras_License_Debug", "LandTech_Extras_License_Debug"),
        ("ElementorExtrasPlugin", "LandTechExtrasPlugin"),
        ("ElementorExtrasUtils", "LandTechExtrasUtils"),
        ("ElementorExtrasOffcanvas", "LandTechExtrasOffcanvas"),
        ("\\ElementorExtras\\", "\\LandTechExtras\\"),
        ("namespace ElementorExtras;", "namespace LandTechExtras;"),
        ("use ElementorExtras\\", "use LandTechExtras\\"),
        ("ELEMENTOR_EXTRAS_", "LANDTECH_EXTRAS_"),
        ("elementor_extras_", "landtech_extras_"),
        ("elementor-extras", "landtech-extras"),
    ]
    out = content
    for old, new in rules:
        out = out.replace(old, new)

    def sub_ee_under(m: re.Match) -> str:
        return "ltxe_" + m.group(1)

    out = re.sub(r"\bee_([a-zA-Z0-9_]+)", sub_ee_under, out)

    for old, new in (
        ("Extras for Elementor", "LandTech Extras for Elementor"),
        ("Elementor Extras", "LandTech Extras"),
    ):
        out = out.replace(old, new)

    # Remaining namespaced identifiers in docblocks, etc.
    out = re.sub(r"\bElementorExtras\b", "LandTechExtras", out)

    return out


def ensure_php_fork_line(content: str) -> str:
    if "Land Tech Web Designs (2026)" in content:
        return content
    if not content.startswith("<?php"):
        return content
    # Insert after <?php opening tag (preserve short open tag spacing).
    if content.startswith("<?php\n"):
        body = content[6:]
        return "<?php\n" + MAINT_COMMENT + "\n" + body
    if content.startswith("<?php\r\n"):
        body = content[7:]
        return "<?php\r\n" + MAINT_COMMENT + "\r\n" + body
    # <?php followed by space or same line — rare for this codebase.
    m = re.match(r"^<\?php\s*", content)
    if m:
        rest = content[m.end() :]
        return "<?php\n" + MAINT_COMMENT + "\n" + rest
    return content


def process_file(path: Path, rel: Path) -> bool:
    if should_skip_path(rel):
        return False

    name = path.name
    suf = path.suffix.lower()

    interesting = suf in TEXT_EXTENSIONS or name in (
        ".cursorrules",
        "create-plugin-zip.sh",
        "phpcs.xml.dist",
        "composer.json",
    )
    interesting = interesting or suf == ".min.js"

    if not interesting:
        return False

    try:
        raw = path.read_bytes()
    except OSError:
        return False

    if b"\0" in raw[:8192]:
        return False

    try:
        text = raw.decode("utf-8")
    except UnicodeDecodeError:
        text = raw.decode("utf-8", errors="replace")

    new_text = apply_string_replacements(text)

    if suf == ".php" or path.name.endswith(".php"):
        new_text = ensure_php_fork_line(new_text)

    if new_text != text:
        path.write_text(new_text, encoding="utf-8")
        return True
    return False


def main() -> None:
    n = 0
    for dirpath, dirnames, filenames in os.walk(ROOT):
        dirnames[:] = sorted(d for d in dirnames if not skip_dir(d))
        dp = Path(dirpath)
        try:
            rel_dir = dp.relative_to(ROOT)
        except ValueError:
            continue
        if should_skip_path(rel_dir):
            continue

        for fname in filenames:
            p = dp / fname
            rel = rel_dir / fname
            if process_file(p, rel):
                n += 1
    print("modified_files", n)


if __name__ == "__main__":
    main()
