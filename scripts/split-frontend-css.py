#!/usr/bin/env python3
"""Move single-widget rules out of the shared frontend stylesheets.

Shared rules (utilities, buttons, rules that name more than one widget) stay
in assets/css/frontend.css and assets/css/frontend-rtl.css.
"""

import os
import re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
CSS_DIR = os.path.join(ROOT, "assets", "css")
OUT_DIR = os.path.join(CSS_DIR, "widgets")

# Longest prefixes first. A selector is claimed by the first family it names.
FAMILIES = [
	("ee-circle-progress", "circle-progress"),
	("ee-image-comparison", "image-comparison"),
	("ee-scroll-indicator", "scroll-indicator"),
	("ee-slide-menu", "slide-menu"),
	("ee-age-gate", "age-gate"),
	("ee-offcanvas", "offcanvas"),
	("ee-breadcrumbs", "breadcrumbs"),
	("ee-hotspot", "hotspots"),
	("ee-heading", "heading"),
	("ee-device", "devices"),
	("ee-timeline", "timeline"),
	("ee-unfold", "unfold"),
	("ee-gallery", "gallery"),
	("ee-switcher", "switcher"),
	("ee-calendar", "calendar"),
	("ee-popup", "popup"),
	("ee-search", "search"),
	("ee-player", "media-player"),
	("ee-audio", "audio-player"),
	("ee-toggle", "toggle"),
	("ee-table", "table"),
	("ee-post", "posts"),
]


def family_of(selector):
	found = None
	for prefix, slug in FAMILIES:
		if re.search(r"(?<![a-zA-Z0-9_-])" + re.escape(prefix) + r"(?![a-zA-Z0-9])", selector):
			if found is None:
				found = slug
			elif found != slug:
				return None
	return found


def split_selectors(header):
	parts = []
	buf = []
	depth = 0
	for ch in header:
		if ch == "(":
			depth += 1
		elif ch == ")" and depth:
			depth -= 1
		if ch == "," and depth == 0:
			parts.append("".join(buf))
			buf = []
			continue
		buf.append(ch)
	parts.append("".join(buf))
	return [part.strip() for part in parts if part.strip()]


def exclusive_family(header):
	"""A rule moves only when every selector names the same widget."""
	selectors = split_selectors(header)
	if not selectors:
		return None
	owner = None
	for selector in selectors:
		found = family_of(selector)
		if found is None:
			return None
		if owner is None:
			owner = found
		elif owner != found:
			return None
	return owner


def scan_blocks(css):
	"""Yield (kind, header, body, full) for top-level constructs."""
	i = 0
	n = len(css)
	buf = []
	while i < n:
		if css[i] == "/" and i + 1 < n and css[i + 1] == "*":
			end = css.find("*/", i + 2)
			end = n if end < 0 else end + 2
			buf.append(css[i:end])
			i = end
			continue
		if css[i] == "{":
			header = "".join(buf)
			buf = []
			depth = 1
			j = i + 1
			while j < n and depth:
				if css[j] == "{":
					depth += 1
				elif css[j] == "}":
					depth -= 1
				j += 1
			body = css[i + 1 : j - 1]
			full = css[i:j]
			kind = "at" if header.lstrip().startswith("@") else "rule"
			yield kind, header, body, header + full
			i = j
			continue
		if css[i] == ";":
			buf.append(css[i])
			yield "stmt", "".join(buf), "", "".join(buf)
			buf = []
			i += 1
			continue
		buf.append(css[i])
		i += 1
	tail = "".join(buf)
	if tail.strip():
		yield "tail", tail, "", tail


def split_rule_list(body):
	"""Split a group body into child blocks, keeping comments with the next rule."""
	return list(scan_blocks(body))


def assign(kind, header, body):
	if kind == "rule":
		return exclusive_family(header)
	if kind == "at":
		name = header.lstrip()
		if name.startswith("@media") or name.startswith("@supports"):
			return "group"
		return None
	return None


def distribute(css):
	buckets = {slug: [] for _, slug in FAMILIES}
	shell = []

	def take(kind, header, body, raw):
		owner = assign(kind, header, body)
		if owner is None:
			shell.append(raw)
			return
		if owner != "group":
			buckets[owner].append(raw)
			return
		children = split_rule_list(body)
		grouped = {slug: [] for _, slug in FAMILIES}
		shared = []
		for ck, ch, cb, cr in children:
			if ck in ("stmt", "tail") or not cr.strip():
				shared.append(cr)
				continue
			child_owner = assign(ck, ch, cb)
			if child_owner and child_owner != "group":
				grouped[child_owner].append(cr)
			else:
				shared.append(cr)
		if any(grouped[s] for s in grouped) and not any(p.strip() for p in shared):
			for slug, parts in grouped.items():
				if parts:
					buckets[slug].append(header + "{" + "".join(parts) + "}\n")
		elif any(grouped[s] for s in grouped):
			for slug, parts in grouped.items():
				if parts:
					buckets[slug].append(header + "{" + "".join(parts) + "}\n")
			if any(p.strip() for p in shared):
				shell.append(header + "{" + "".join(shared) + "}\n")
		else:
			shell.append(raw)

	for kind, header, body, raw in scan_blocks(css):
		take(kind, header, body, raw)
	return shell, buckets


def write_pair(src_name, rtl):
	path = os.path.join(CSS_DIR, src_name)
	with open(path, "r", encoding="utf-8") as handle:
		css = handle.read()
	shell, buckets = distribute(css)
	shell_css = "".join(shell)
	if not shell_css.lstrip().startswith("@charset") and css.lstrip().startswith("@charset"):
		shell_css = '@charset "UTF-8";\n' + shell_css
	with open(path, "w", encoding="utf-8") as handle:
		handle.write(shell_css)
	os.makedirs(OUT_DIR, exist_ok=True)
	suffix = "-rtl" if rtl else ""
	written = 0
	for slug, parts in buckets.items():
		text = "".join(parts).strip()
		if not text:
			continue
		out = os.path.join(OUT_DIR, slug + suffix + ".css")
		banner = "/* LandTech " + slug + " */\n"
		with open(out, "w", encoding="utf-8") as handle:
			handle.write(banner + text + "\n")
		written += 1
		print(slug + suffix, len(text))
	print(src_name, "shell", len(shell_css), "files", written)


if __name__ == "__main__":
	write_pair("frontend.css", False)
	write_pair("frontend-rtl.css", True)
