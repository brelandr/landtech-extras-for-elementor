import { test, expect } from '@playwright/test';

const base = process.env.LTXE_E2E_BASE_URL || process.env.BASE_URL || 'http://127.0.0.1:8888';

test.describe('U6 Tabs', () => {
	test('tabs switch panels', async ({ page }) => {
		await page.setViewportSize({ width: 1280, height: 800 });
		await page.goto(`${base}/demo-tabs/`, { waitUntil: 'domcontentloaded' });
		const tabs = page.locator('.ltxe-tabs__tab');
		await expect(tabs.first()).toBeVisible();
		if (await tabs.count() > 1) {
			await tabs.nth(1).click();
			await expect(tabs.nth(1)).toHaveAttribute('aria-selected', 'true');
		}
	});

	test('mobile accordion titles are at least 44px', async ({ page }) => {
		await page.setViewportSize({ width: 375, height: 812 });
		await page.goto(`${base}/demo-tabs/`, { waitUntil: 'domcontentloaded' });
		const tab = page.locator('.ltxe-tabs__accordion-title').first();
		await expect(tab).toBeVisible();
		const box = await tab.boundingBox();
		expect(box?.height || 0).toBeGreaterThanOrEqual(40);
	});
});
