import { test, expect, type Page } from '@playwright/test';
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
