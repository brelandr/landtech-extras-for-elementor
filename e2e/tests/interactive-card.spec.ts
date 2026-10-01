import { test, expect, type Page } from '@playwright/test';
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
