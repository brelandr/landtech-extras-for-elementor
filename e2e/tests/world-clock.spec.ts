import { test, expect, type Page } from '@playwright/test';
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
