import { test, expect, type Page } from '@playwright/test';
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
