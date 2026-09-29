import { test, expect } from '@playwright/test';

const base = process.env.LTXE_E2E_BASE_URL || 'http://localhost:8888';

test.describe('U18 Team Members', () => {
	test('grid renders member cards', async ({ page }) => {
		await page.setViewportSize({ width: 375, height: 812 });
		await page.goto(`${base}/demo-team-members/`, { waitUntil: 'domcontentloaded' });
		await expect(page.locator('.ltxe-team').first()).toBeVisible();
		await expect(page.locator('.ltxe-team-card').first()).toBeVisible();
		await expect(page.locator('.ltxe-team-card__name').first()).toBeVisible();
	});
});
