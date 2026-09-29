import { test, expect } from '@playwright/test';

const base = process.env.LTXE_E2E_BASE_URL || 'http://localhost:8888';

test.describe('U19–U20 Icon Box and CTA', () => {
	test('icon box renders title and mark', async ({ page }) => {
		await page.setViewportSize({ width: 375, height: 812 });
		await page.goto(`${base}/demo-icon-box/`, { waitUntil: 'domcontentloaded' });
		await expect(page.locator('.ltxe-icon-box').first()).toBeVisible();
		await expect(page.locator('.ltxe-icon-box__title').first()).toBeVisible();
	});

	test('cta renders heading and buttons', async ({ page }) => {
		await page.goto(`${base}/demo-cta-block/`, { waitUntil: 'domcontentloaded' });
		await expect(page.locator('.ltxe-cta').first()).toBeVisible();
		await expect(page.locator('.ltxe-cta__heading').first()).toBeVisible();
		await expect(page.locator('.ltxe-cta__btn').first()).toBeVisible();
	});
});
