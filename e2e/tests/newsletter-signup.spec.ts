import { test, expect, type Page } from '@playwright/test';
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
