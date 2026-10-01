import { test, expect, type Page } from '@playwright/test';
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
