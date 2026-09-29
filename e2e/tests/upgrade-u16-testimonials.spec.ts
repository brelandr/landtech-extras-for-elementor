import { test, expect } from '@playwright/test';

const base = process.env.LTXE_E2E_BASE_URL || 'http://localhost:8888';

test.describe('U16 Testimonials', () => {
	test('carousel renders reviews and next control', async ({ page }) => {
		await page.setViewportSize({ width: 375, height: 812 });
		await page.goto(`${base}/demo-testimonials/`, { waitUntil: 'domcontentloaded' });
		const root = page.locator('.ltxe-testimonials').first();
		await expect(root).toBeVisible();
		await expect(page.locator('.ltxe-testimonial').first()).toBeVisible();
		const next = page.locator('.ltxe-testimonials__btn--next').first();
		if (await next.count()) {
			await next.click();
			await expect(page.locator('.ltxe-testimonials__status').first()).toBeVisible();
		}
	});
});
