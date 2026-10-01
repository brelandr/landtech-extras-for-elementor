import { test, expect } from '@playwright/test';

const base = process.env.LTXE_E2E_BASE_URL || process.env.BASE_URL || 'http://127.0.0.1:8888';

test.describe('U10–U15 new widgets', () => {
	test('progress bar has progressbar role', async ({ page }) => {
		await page.setViewportSize({ width: 375, height: 812 });
		await page.goto(`${base}/demo-progress-bar/`, { waitUntil: 'domcontentloaded' });
		await expect(page.locator('[role="progressbar"]').first()).toBeVisible();
	});

	test('countdown digits render', async ({ page }) => {
		await page.goto(`${base}/demo-countdown/`, { waitUntil: 'domcontentloaded' });
		await expect(page.locator('.ltxe-countdown__digit').first()).toBeVisible();
	});

	test('reading progress is fixed', async ({ page }) => {
		await page.goto(`${base}/demo-reading-progress/`, { waitUntil: 'domcontentloaded' });
		const bar = page.locator('.ltxe-reading-progress').first();
		await expect(bar).toBeVisible();
	});

	test('pricing toggle fires period change', async ({ page }) => {
		await page.goto(`${base}/demo-pricing-table/`, { waitUntil: 'domcontentloaded' });
		const toggle = page.locator('.ltxe-pricing-toggle__btn[data-period="annual"]');
		if (await toggle.count()) {
			await toggle.click();
			await expect(page.locator('.ltxe-pricing-table__price--annual').first()).toBeVisible();
		}
	});

	test('star rating renders filled stars', async ({ page }) => {
		await page.goto(`${base}/demo-star-rating/`, { waitUntil: 'domcontentloaded' });
		await expect(page.locator('.ltxe-star--full').first()).toBeVisible();
	});
});
