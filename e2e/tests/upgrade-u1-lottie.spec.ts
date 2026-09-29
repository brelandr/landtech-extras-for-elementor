import { test, expect } from '@playwright/test';

const base = process.env.LTXE_E2E_BASE_URL || 'http://localhost:8888';

test.describe('U1 Lottie', () => {
	test('demo page exposes lottie-player or advanced canvas', async ({ page }) => {
		await page.setViewportSize({ width: 375, height: 812 });
		await page.goto(`${base}/demo-lottie/`, { waitUntil: 'domcontentloaded' });
		const player = page.locator('lottie-player, .ee-lottie__canvas');
		await expect(player.first()).toBeVisible();
	});
});
