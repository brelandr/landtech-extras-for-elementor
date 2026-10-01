import { test, expect } from '@playwright/test';

const base = process.env.LTXE_E2E_BASE_URL || process.env.BASE_URL || 'http://127.0.0.1:8888';

test.describe('U17 FAQ Accordion', () => {
	test('first item is open and schema is present', async ({ page }) => {
		await page.setViewportSize({ width: 375, height: 812 });
		await page.goto(`${base}/demo-faq/`, { waitUntil: 'domcontentloaded' });
		await expect(page.locator('.ltxe-faq__question').first()).toHaveAttribute('aria-expanded', 'true');
		const schema = page.locator('script[type="application/ld+json"]');
		await expect(schema.first()).toBeAttached();
	});
});
