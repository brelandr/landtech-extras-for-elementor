import { test, expect, type Page } from '@playwright/test';
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
