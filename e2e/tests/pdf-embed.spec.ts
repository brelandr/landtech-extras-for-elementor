import { test, expect, type Page } from '@playwright/test';
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
