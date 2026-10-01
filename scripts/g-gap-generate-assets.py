#!/usr/bin/env python3
"""Generate G2–G10 SVGs, fixtures, Playwright specs, and demo templates."""
import json
from pathlib import Path

FREE = Path("/Users/randy/wordpress-plugins/WordpressDev/Extras-For-Elementor/landtech-extras-for-elementor")
PREM = Path("/Users/randy/wordpress-plugins/WordpressDev/Extras-For-Elementor/LandTech-Extras-For-Elementor-Premium")

PATHS = {
    "wave-1": "M0,64 C150,120 350,0 600,64 C850,128 1050,16 1200,64 L1200,120 L0,120 Z",
    "wave-2": "M0,80 C200,20 400,20 600,80 C800,140 1000,140 1200,80 L1200,120 L0,120 Z",
    "wave-3": "M0,40 C300,100 500,0 800,50 C1000,80 1100,20 1200,40 L1200,120 L0,120 Z",
    "slant-1": "M0,80 L1200,0 L1200,120 L0,120 Z",
    "slant-2": "M0,0 L1200,80 L1200,120 L0,120 Z",
    "triangle-1": "M0,120 L600,0 L1200,120 Z",
    "curve-1": "M0,120 Q600,0 1200,120 Z",
    "curve-2": "M0,0 Q600,120 1200,0 L1200,120 L0,120 Z",
    "zigzag-1": "M0,80 L150,20 L300,80 L450,20 L600,80 L750,20 L900,80 L1050,20 L1200,80 L1200,120 L0,120 Z",
    "arrow-1": "M0,80 L560,80 L600,20 L640,80 L1200,80 L1200,120 L0,120 Z",
    "tilt-1": "M0,40 L1200,100 L1200,120 L0,120 Z",
    "clouds-1": "M0,80 C80,80 80,40 160,40 C200,10 280,10 320,40 C400,20 480,40 520,70 C600,40 700,40 760,70 C840,30 960,40 1020,70 C1100,50 1160,70 1200,80 L1200,120 L0,120 Z",
    "fan-1": "M0,120 L0,80 C200,80 200,20 400,20 C600,20 600,80 800,80 C1000,80 1000,20 1200,20 L1200,120 Z",
    "drops-1": "M0,90 Q150,20 300,90 T600,90 T900,90 T1200,90 L1200,120 L0,120 Z",
    "mountains-1": "M0,120 L0,90 L200,20 L400,90 L600,10 L800,90 L1000,40 L1200,90 L1200,120 Z",
    "book-1": "M0,40 Q300,100 600,40 Q900,0 1200,40 L1200,120 L0,120 Z",
    "split-1": "M0,120 L0,60 L600,10 L1200,60 L1200,120 Z",
    "waves-opacity-1": "M0,70 C200,10 400,130 600,70 C800,10 1000,130 1200,70 L1200,120 L0,120 Z",
    "pyramids-1": "M0,120 L200,40 L400,120 L600,20 L800,120 L1000,50 L1200,120 Z",
    "curve-asym-1": "M0,90 C200,10 800,110 1200,30 L1200,120 L0,120 Z",
}

shapes_dir = FREE / "modules/shape-dividers/assets/shapes"
shapes_dir.mkdir(parents=True, exist_ok=True)
for slug, d in PATHS.items():
    svg = f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path fill="#0b1020" d="{d}"/></svg>\n'
    (shapes_dir / f"{slug}.svg").write_text(svg)
(shapes_dir / "README.txt").write_text(
    "LandTech Extras shape divider SVGs (CC0 / original).\nUsed by the Shape Dividers extension. 20 paths, viewBox 0 0 1200 120.\n"
)

fixtures = {
    "comparison-table.html": """<!DOCTYPE html>
<html lang="en"><body>
<div class="ltxe-cmp" data-ltxe-cmp="1" data-mobile="tabs">
  <div class="ltxe-cmp__tabs" hidden></div>
  <div class="ltxe-cmp__scroll">
    <table class="ltxe-cmp__table">
      <thead><tr><th></th><th class="ltxe-cmp__col" data-col="0"><span class="ltxe-cmp__title">Starter</span></th><th class="ltxe-cmp__col ltxe-cmp__col--featured" data-col="1"><span class="ltxe-cmp__title">Pro</span></th></tr></thead>
      <tbody><tr><th class="ltxe-cmp__row-label">Support</th><td data-col="0"><span class="ltxe-cmp__check">✓</span></td><td data-col="1"><span class="ltxe-cmp__check">✓</span></td></tr></tbody>
    </table>
  </div>
</div>
</body></html>
""",
    "interactive-card.html": """<!DOCTYPE html>
<html lang="en"><body>
<div class="ltxe-icard ltxe-icard--tilt" data-ltxe-tilt="1" data-ltxe-icard='{"max":12}'>
  <h3 class="ltxe-icard__title">Tilt card</h3>
  <p class="ltxe-icard__desc">Hover me</p>
  <a class="ltxe-icard__btn" href="#go">Learn more</a>
</div>
</body></html>
""",
    "image-scroller.html": """<!DOCTYPE html>
<html lang="en"><body>
<div class="ltxe-iscroll" data-scroll-trigger="hover" data-ltxe-iscroll='{"trigger":"hover"}'>
  <img class="ltxe-iscroll__img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==" alt="" />
</div>
</body></html>
""",
    "shape-dividers.html": """<!DOCTYPE html>
<html lang="en"><body>
<section class="ltxe-has-shape-divider" data-ltxe-sd='{"bottom":{"slug":"wave-1","svg":"<svg viewBox=\\"0 0 1200 120\\"><path fill=\\"#0b1020\\" d=\\"M0,64 L1200,64 L1200,120 L0,120 Z\\"></path></svg>","h":80,"w":100,"z":1}}'>
  <p>Section with divider</p>
</section>
</body></html>
""",
    "pdf-embed.html": """<!DOCTYPE html>
<html lang="en"><body>
<div class="ltxe-pdf" data-ltxe-pdf='{"url":"https://example.com/doc.pdf","page":1}'>
  <div class="ltxe-pdf__toolbar">
    <button type="button" class="ltxe-pdf__btn" data-pdf-act="prev">Previous</button>
    <a class="ltxe-pdf__btn" download href="https://example.com/doc.pdf">Download</a>
  </div>
  <iframe class="ltxe-pdf__frame" src="https://example.com/doc.pdf#page=1" title="PDF document"></iframe>
</div>
</body></html>
""",
    "world-clock.html": """<!DOCTYPE html>
<html lang="en"><body>
<div class="ltxe-wclock ltxe-wclock--grid" data-ltxe-wclock='{"mode":"both","hour12":false}'>
  <div class="ltxe-wclock__item" data-tz="America/New_York" data-seconds="1" data-date="1">
    <p class="ltxe-wclock__city">New York</p>
    <div class="ltxe-wclock__analog"><span class="ltxe-wclock__hand ltxe-wclock__hand--h"></span><span class="ltxe-wclock__hand ltxe-wclock__hand--m"></span><span class="ltxe-wclock__hand ltxe-wclock__hand--s"></span></div>
    <p class="ltxe-wclock__digital">--:--</p>
    <p class="ltxe-wclock__date"></p>
  </div>
  <div class="ltxe-wclock__item" data-tz="Europe/London" data-seconds="1" data-date="1">
    <p class="ltxe-wclock__city">London</p>
    <p class="ltxe-wclock__digital">--:--</p>
    <p class="ltxe-wclock__date"></p>
  </div>
  <div class="ltxe-wclock__item" data-tz="Asia/Dubai" data-seconds="1" data-date="1"><p class="ltxe-wclock__city">Dubai</p><p class="ltxe-wclock__digital">--:--</p></div>
  <div class="ltxe-wclock__item" data-tz="Asia/Tokyo" data-seconds="1" data-date="1"><p class="ltxe-wclock__city">Tokyo</p><p class="ltxe-wclock__digital">--:--</p></div>
</div>
</body></html>
""",
    "tags-cloud-sphere.html": """<!DOCTYPE html>
<html lang="en"><body>
<div class="ltxe-sphere" data-ltxe-sphere='{"radius":140,"auto":true,"pauseHover":true}'>
  <a class="ltxe-sphere__tag" href="https://example.com/js">JavaScript</a>
  <a class="ltxe-sphere__tag" href="https://example.com/wp">WordPress</a>
  <a class="ltxe-sphere__tag" href="https://example.com/el">Elementor</a>
</div>
</body></html>
""",
    "newsletter-signup.html": """<!DOCTYPE html>
<html lang="en"><body>
<form class="ltxe-news ltxe-news--inline" data-ltxe-news='{"rest":"https://ltxe.test/wp-json/landtech-extras/v1/newsletter/subscribe","nonce":"abc","audience":"list1","success":"Thanks","error":"Nope"}'>
  <label class="ltxe-news__field"><input type="text" name="first_name" placeholder="First name" /></label>
  <label class="ltxe-news__field"><input type="email" name="email" required placeholder="Email address" /></label>
  <label class="ltxe-news__gdpr"><input type="checkbox" name="gdpr_consent" value="1" /> I agree</label>
  <button type="submit" class="ltxe-news__btn">Subscribe</button>
  <p class="ltxe-news__msg" role="status" hidden></p>
</form>
</body></html>
""",
}

fix_dir = FREE / "tests/fixtures"
for name, html in fixtures.items():
    (fix_dir / name).write_text(html)

specs = {
    "comparison-table.spec.ts": """import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';
const fixtureHtml = readFileSync(join(process.cwd(), '../tests/fixtures/comparison-table.html'), 'utf8');
const js = readFileSync(join(process.cwd(), '../modules/comparison-table/assets/js/ltxe-comparison-table.js'), 'utf8');
const css = readFileSync(join(process.cwd(), '../modules/comparison-table/assets/css/ltxe-comparison-table.css'), 'utf8');
async function load(page: Page) {
  await page.setViewportSize({ width: 1280, height: 800 });
  await page.setContent(fixtureHtml);
  await page.addStyleTag({ content: css });
  await page.addScriptTag({ content: js });
}
test.describe('G3 Comparison Table', () => {
  test('renders featured column and check cells', async ({ page }) => {
    await load(page);
    await expect(page.locator('.ltxe-cmp__col--featured')).toHaveCount(1);
    await expect(page.locator('.ltxe-cmp__check')).toHaveCount(2);
  });
});
""",
    "interactive-card.spec.ts": """import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';
const fixtureHtml = readFileSync(join(process.cwd(), '../tests/fixtures/interactive-card.html'), 'utf8');
const js = readFileSync(join(process.cwd(), '../modules/interactive-cards/assets/js/ltxe-interactive-card.js'), 'utf8');
const css = readFileSync(join(process.cwd(), '../modules/interactive-cards/assets/css/ltxe-interactive-card.css'), 'utf8');
async function load(page: Page) {
  await page.setViewportSize({ width: 1280, height: 800 });
  await page.setContent(fixtureHtml);
  await page.addStyleTag({ content: css });
  await page.addScriptTag({ content: js });
}
test.describe('G4 Interactive Card', () => {
  test('renders tilt card and 44px button', async ({ page }) => {
    await load(page);
    await expect(page.locator('[data-ltxe-tilt]')).toBeVisible();
    const box = await page.locator('.ltxe-icard__btn').boundingBox();
    expect(box!.height).toBeGreaterThanOrEqual(44);
  });
});
""",
    "image-scroller.spec.ts": """import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';
const fixtureHtml = readFileSync(join(process.cwd(), '../tests/fixtures/image-scroller.html'), 'utf8');
const js = readFileSync(join(process.cwd(), '../modules/image-scroller/assets/js/ltxe-image-scroller.js'), 'utf8');
const css = readFileSync(join(process.cwd(), '../modules/image-scroller/assets/css/ltxe-image-scroller.css'), 'utf8');
async function load(page: Page) {
  await page.setViewportSize({ width: 1280, height: 800 });
  await page.setContent(fixtureHtml);
  await page.addStyleTag({ content: css });
  await page.addScriptTag({ content: js });
}
test.describe('G5 Image Scroller', () => {
  test('renders hover trigger image', async ({ page }) => {
    await load(page);
    await expect(page.locator('.ltxe-iscroll')).toHaveAttribute('data-scroll-trigger', 'hover');
    await expect(page.locator('.ltxe-iscroll__img')).toBeVisible();
  });
});
""",
    "shape-dividers.spec.ts": """import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';
const fixtureHtml = readFileSync(join(process.cwd(), '../tests/fixtures/shape-dividers.html'), 'utf8');
const js = readFileSync(join(process.cwd(), '../modules/shape-dividers/assets/js/ltxe-shape-dividers-editor.js'), 'utf8');
const css = readFileSync(join(process.cwd(), '../modules/shape-dividers/assets/css/ltxe-shape-dividers.css'), 'utf8');
async function load(page: Page) {
  await page.setViewportSize({ width: 1280, height: 800 });
  await page.setContent(fixtureHtml);
  await page.addStyleTag({ content: css });
  await page.addScriptTag({ content: js });
}
test.describe('G6 Shape Dividers', () => {
  test('injects SVG divider from data attribute', async ({ page }) => {
    await load(page);
    await expect(page.locator('.ltxe-shape-divider[data-ltxe-shape="wave-1"]')).toHaveCount(1);
    await expect(page.locator('.ltxe-shape-divider svg')).toHaveCount(1);
  });
});
""",
    "pdf-embed.spec.ts": """import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';
const fixtureHtml = readFileSync(join(process.cwd(), '../tests/fixtures/pdf-embed.html'), 'utf8');
const js = readFileSync(join(process.cwd(), '../modules/pdf-embed/assets/js/ltxe-pdf-embed.js'), 'utf8');
const css = readFileSync(join(process.cwd(), '../modules/pdf-embed/assets/css/ltxe-pdf-embed.css'), 'utf8');
async function load(page: Page) {
  await page.setViewportSize({ width: 1280, height: 800 });
  await page.setContent(fixtureHtml);
  await page.addStyleTag({ content: css });
  await page.addScriptTag({ content: js });
}
test.describe('G7 PDF Embed', () => {
  test('iframe and download button exist', async ({ page }) => {
    await load(page);
    await expect(page.locator('.ltxe-pdf__frame')).toHaveCount(1);
    await expect(page.locator('a[download]')).toHaveAttribute('href', 'https://example.com/doc.pdf');
  });
});
""",
    "world-clock.spec.ts": """import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';
const fixtureHtml = readFileSync(join(process.cwd(), '../tests/fixtures/world-clock.html'), 'utf8');
const js = readFileSync(join(process.cwd(), '../modules/world-clock/assets/js/ltxe-world-clock.js'), 'utf8');
const css = readFileSync(join(process.cwd(), '../modules/world-clock/assets/css/ltxe-world-clock.css'), 'utf8');
async function load(page: Page) {
  await page.setViewportSize({ width: 1280, height: 800 });
  await page.setContent(fixtureHtml);
  await page.addStyleTag({ content: css });
  await page.addScriptTag({ content: js });
  await page.waitForFunction(() => {
    const el = document.querySelector('.ltxe-wclock__digital');
    return el && el.textContent && el.textContent !== '--:--';
  });
}
test.describe('G8 World Clock', () => {
  test('renders four clocks and analog hands', async ({ page }) => {
    await load(page);
    await expect(page.locator('.ltxe-wclock__item')).toHaveCount(4);
    await expect(page.locator('.ltxe-wclock__hand--h')).toHaveCount(1);
    await expect(page.locator('.ltxe-wclock__digital').first()).not.toHaveText('--:--');
  });
});
""",
    "tags-cloud-sphere.spec.ts": """import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';
const fixtureHtml = readFileSync(join(process.cwd(), '../tests/fixtures/tags-cloud-sphere.html'), 'utf8');
const js = readFileSync(join(process.cwd(), '../modules/tags-cloud-sphere/assets/js/ltxe-tags-cloud-sphere.js'), 'utf8');
const css = readFileSync(join(process.cwd(), '../modules/tags-cloud-sphere/assets/css/ltxe-tags-cloud-sphere.css'), 'utf8');
async function load(page: Page) {
  await page.setViewportSize({ width: 1280, height: 800 });
  await page.setContent(fixtureHtml);
  await page.addStyleTag({ content: css });
  await page.addScriptTag({ content: js });
}
test.describe('G9 Tags Cloud Sphere', () => {
  test('renders tag links', async ({ page }) => {
    await load(page);
    await expect(page.locator('.ltxe-sphere__tag')).toHaveCount(3);
    await expect(page.locator('.ltxe-sphere__tag').first()).toHaveAttribute('href', 'https://example.com/js');
  });
});
""",
    "newsletter-signup.spec.ts": """import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';
const fixtureHtml = readFileSync(join(process.cwd(), '../tests/fixtures/newsletter-signup.html'), 'utf8');
const js = readFileSync(join(process.cwd(), '../modules/newsletter-signup/assets/js/ltxe-newsletter.js'), 'utf8');
const css = readFileSync(join(process.cwd(), '../modules/newsletter-signup/assets/css/ltxe-newsletter.css'), 'utf8');
async function load(page: Page) {
  await page.route('**/landtech-extras/v1/newsletter/subscribe**', async (route) => {
    await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({ subscribed: true }) });
  });
  await page.setViewportSize({ width: 1280, height: 800 });
  await page.setContent(fixtureHtml);
  await page.addStyleTag({ content: css });
  await page.addScriptTag({ content: js });
}
test.describe('G10 Newsletter Signup', () => {
  test('form submits to REST and shows success', async ({ page }) => {
    await load(page);
    await page.fill('input[name="email"]', 'visitor@example.com');
    await page.check('input[name="gdpr_consent"]');
    await page.click('.ltxe-news__btn');
    await expect(page.locator('.ltxe-news__msg')).toHaveClass(/is-ok/);
    const html = await page.content();
    expect(html.includes('api-key')).toBeFalsy();
    expect(html.toLowerCase().includes('mailchimp')).toBeFalsy();
  });
});
""",
}

spec_dir = FREE / "e2e/tests"
for name, body in specs.items():
    (spec_dir / name).write_text(body)

demos = [
    ("recipe", "Recipe", "ltxe-recipe", "Print-ready recipes with JSON-LD schema.", "Add ingredients and steps.", "Enable print and schema.", "Style the layout."),
    ("comparison-table", "Comparison Table", "ltxe-comparison-table", "Feature grids with check, cross, and text cells.", "Add columns and rows.", "Mark a featured plan.", "Pick a mobile layout."),
    ("interactive-card", "Interactive Card", "ltxe-interactive-card", "Tilt, flip, and hover cards.", "Choose an effect.", "Add title and link.", "Tune tilt intensity."),
    ("image-scroller", "Image Scroller", "ltxe-image-scroller", "Reveal tall images on hover or scroll.", "Upload a tall image.", "Pick hover or scroll.", "Set direction."),
    ("shape-dividers", "Shape Dividers", "html", "Twenty SVG waves, slants, and zigzags on sections.", "Open the section Layout tab.", "Pick a top or bottom shape.", "Set color and height."),
    ("pdf-embed", "PDF Embed", "ltxe-pdf-embed", "Inline PDFs with toolbar controls.", "Upload a PDF.", "Choose native viewer.", "Toggle download."),
    ("world-clock", "World Clock", "ltxe-world-clock", "Live analog and digital clocks for any timezone.", "Add clock slots.", "Set IANA timezones.", "Choose analog, digital, or both."),
    ("tags-cloud-sphere", "Tags Cloud Sphere", "ltxe-tags-cloud-sphere", "A 3D rotating tag cloud.", "Choose a taxonomy.", "Set the radius.", "Enable auto-rotate."),
    ("newsletter-signup", "Newsletter Signup", "ltxe-newsletter-signup", "Mailchimp subscribe form with GDPR consent.", "Save the Mailchimp key in APIs.", "Set the audience ID.", "Choose fields and layout."),
]

tpl_dir = PREM / "e2e/scripts/demo-templates"
tpl_dir.mkdir(parents=True, exist_ok=True)

def demo_json(slug, title, widget, lead, s1, s2, s3):
    live_widget = {
        "id": slug[:6] + "w1",
        "elType": "widget",
        "widgetType": widget,
        "settings": {},
        "elements": [],
    }
    live_children = [live_widget]
    live_settings = {"css_classes": "ltxdh-section-live", "_element_id": "ltxdh-live"}
    if widget == "html":
        def shape_band(bid, bg, shape, color, label):
            return {
                "id": bid,
                "elType": "section",
                "isInner": True,
                "settings": {
                    "layout": "full_width",
                    "gap": "no",
                    "background_background": "classic",
                    "background_color": bg,
                    "padding": {"unit": "px", "top": "72", "right": "24", "bottom": "120", "left": "24", "isLinked": False},
                    "margin": {"unit": "px", "top": "0", "right": "0", "bottom": "0", "left": "0", "isLinked": True},
                    "ltxe_sd_bottom": shape,
                    "ltxe_sd_bottom_color": color,
                    "ltxe_sd_bottom_height": {"size": 90, "unit": "px"},
                    "ltxe_sd_bottom_width": {"size": 100, "unit": "%"},
                    "ltxe_sd_bottom_z": 2,
                },
                "elements": [{
                    "id": bid + "c",
                    "elType": "column",
                    "settings": {"_column_size": 100},
                    "elements": [{
                        "id": bid + "h",
                        "elType": "widget",
                        "widgetType": "html",
                        "settings": {"html": f'<p style="margin:0;color:#ffffff;text-align:center;font-size:1.35rem;font-weight:600;">{label}</p>'},
                        "elements": [],
                    }],
                }],
            }
        live_children = [
            shape_band("sdwave", "#1a1a2e", "wave-1", "#4c1d95", "Wave divider on this section"),
            shape_band("sdslant", "#4c1d95", "slant-1", "#0f172a", "Slant divider"),
            shape_band("sdzig", "#0f172a", "zigzag-1", "#e94560", "Zigzag divider"),
            shape_band("sdmount", "#e94560", "mountains-1", "#ffffff", "Mountains divider"),
        ]
        live_settings = {
            "layout": "full_width",
            "gap": "no",
            "_element_id": "ltxdh-live",
            "padding": {"unit": "px", "top": "0", "right": "0", "bottom": "0", "left": "0", "isLinked": True},
        }
    if widget == "ltxe-recipe":
        live_widget["settings"] = {"name": "Classic Chocolate Chip Cookies", "prep_time": "15", "cook_time": "12", "enable_print": "yes"}
    if widget == "ltxe-world-clock":
        live_widget["settings"] = {"mode": "both", "layout": "grid"}
    if widget == "ltxe-newsletter-signup":
        live_widget["settings"] = {"layout": "inline", "show_first": "yes", "show_gdpr": "yes", "audience_id": "demo"}
    if widget == "ltxe-pdf-embed":
        live_widget["settings"] = {"pdf_url": "https://mozilla.github.io/pdf.js/web/compressed.tracemonkey-pldi-09.pdf", "show_download": "yes"}
    return {
        "title": f"Demo: {title}",
        "type": "page",
        "page_settings": {"hide_title": "yes", "template": "elementor_header_footer"},
        "content": [
            {
                "id": slug[:6] + "hero",
                "elType": "section",
                "settings": {"css_classes": "ltxdh-section-hero"},
                "elements": [{
                    "id": slug[:6] + "hcol",
                    "elType": "column",
                    "settings": {"_column_size": 100},
                    "elements": [{
                        "id": slug[:6] + "hhtm",
                        "elType": "widget",
                        "widgetType": "html",
                        "settings": {
                            "html": f'<div class="ltxdh-hero"><span class="ltxdh-badge ltxdh-badge--free">Free</span><h1 id="ltxdh-top">{title}</h1><p class="ltxdh-hero__lead">{lead}</p><div class="ltxdh-hero__actions"><a class="ltxdh-btn ltxdh-btn--primary" href="https://extrasforelementor.com/demos/">See all live demos</a><a class="ltxdh-btn ltxdh-btn--glass" href="https://wordpress.org/plugins/landtech-extras-for-elementor/">Download free</a></div><nav class="ltxdh-hero__nav" aria-label="On this page"><a href="#ltxdh-overview">Overview</a><a href="#ltxdh-live">Live demo</a><a href="#ltxdh-howto">How to implement</a></nav></div>'
                        },
                        "elements": [],
                    }],
                }],
            },
            {
                "id": slug[:6] + "over",
                "elType": "section",
                "settings": {},
                "elements": [{
                    "id": slug[:6] + "ocol",
                    "elType": "column",
                    "settings": {"_column_size": 100},
                    "elements": [{
                        "id": slug[:6] + "ohtm",
                        "elType": "widget",
                        "widgetType": "html",
                        "settings": {"html": f'<div id="ltxdh-overview" class="ltxdh-section ltxdh-section--overview"><h2>What it does</h2><div class="ltxdh-prose"><p>{lead}</p></div></div>'},
                        "elements": [],
                    }],
                }],
            },
            {
                "id": slug[:6] + "live",
                "elType": "section",
                "settings": live_settings,
                "elements": [{
                    "id": slug[:6] + "lcol",
                    "elType": "column",
                    "settings": {"_column_size": 100},
                    "elements": live_children,
                }],
            },
            {
                "id": slug[:6] + "how",
                "elType": "section",
                "settings": {},
                "elements": [{
                    "id": slug[:6] + "hcol2",
                    "elType": "column",
                    "settings": {"_column_size": 100},
                    "elements": [{
                        "id": slug[:6] + "hhtm2",
                        "elType": "widget",
                        "widgetType": "html",
                        "settings": {"html": f'<div id="ltxdh-howto" class="ltxdh-section ltxdh-section--howto"><h2>How to implement</h2><ol class="ltxdh-steps"><li>{s1}</li><li>{s2}</li><li>{s3}</li></ol></div>'},
                        "elements": [],
                    }],
                }],
            },
        ],
    }

for row in demos:
    (tpl_dir / f"{row[0]}.json").write_text(json.dumps(demo_json(*row), indent=2))

print("generated", len(PATHS), "svgs", len(fixtures), "fixtures", len(specs), "specs", len(demos), "demos")
