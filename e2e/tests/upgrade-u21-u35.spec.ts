import { test, expect } from '@playwright/test';

const base = process.env.LTXE_E2E_BASE_URL || process.env.BASE_URL || 'http://127.0.0.1:8888';

test.describe('U21–U25 share, top, consent, sticky, dark mode', () => {
	test('social share renders buttons', async ({ page }) => {
		await page.setViewportSize({ width: 375, height: 812 });
		await page.goto(`${base}/demo-social-share/`, { waitUntil: 'domcontentloaded' });
		await expect(page.locator('.ltxe-share').first()).toBeVisible();
		await expect(page.locator('.ltxe-share__btn').first()).toBeVisible();
	});

	test('back to top appears after scroll', async ({ page }) => {
		await page.goto(`${base}/demo-back-to-top/`, { waitUntil: 'domcontentloaded' });
		const btn = page.locator('.ltxe-btt').first();
		await expect(btn).toHaveCount(1);
		await page.evaluate(() => {
			const doc = document.documentElement;
			if ( doc.scrollHeight < window.innerHeight + 900 ) {
				const spacer = document.createElement( 'div' );
				spacer.style.minHeight = '1600px';
				spacer.setAttribute( 'aria-hidden', 'true' );
				document.body.appendChild( spacer );
			}
			window.scrollTo( 0, 800 );
			window.dispatchEvent( new Event( 'scroll' ) );
		});
		await expect(btn).toHaveClass(/is-visible/);
	});

	test('cookie consent banner can appear', async ({ page }) => {
		await page.goto(`${base}/demo-cookie-consent/`, { waitUntil: 'domcontentloaded' });
		await page.evaluate(() => {
			Object.keys(localStorage).forEach((k) => {
				if (k.indexOf('ltxe_consent_') === 0) {
					localStorage.removeItem(k);
				}
			});
		});
		await page.reload({ waitUntil: 'domcontentloaded' });
		await expect(page.locator('.ltxe-cookie-consent').first()).toBeVisible();
	});

	test('sticky wrapper and dark mode render', async ({ page }) => {
		await page.goto(`${base}/demo-sticky-wrapper/`, { waitUntil: 'domcontentloaded' });
		await expect(page.locator('.ltxe-sticky').first()).toBeVisible();
		await page.goto(`${base}/demo-dark-mode/`, { waitUntil: 'domcontentloaded' });
		await expect(page.locator('.ltxe-dark-toggle').first()).toBeVisible();
	});
});
