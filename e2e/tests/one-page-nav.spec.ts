import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';

const css = readFileSync(join(process.cwd(), '../assets/css/one-page-nav.css'), 'utf8');
const js = readFileSync(join(process.cwd(), '../assets/js/one-page-nav.js'), 'utf8');

const html = `<style>body{margin:0}.ltxe-opn-demo-sec{height:900px;padding:40px}</style>
<section class="ltxe-opn-demo-sec" id="hero" data-ltxe-opn-title="Hero"><h2>Hero</h2></section>
<section class="ltxe-opn-demo-sec" id="about" data-ltxe-opn-title="About"><h2>About</h2></section>
<section class="ltxe-opn-demo-sec" id="services" data-ltxe-opn-title="Services"><h2>Services</h2></section>
<nav class="ltxe-opn ltxe-opn--right" data-ltxe-opn='{"selector":".ltxe-opn-demo-sec","tooltip":true,"smooth":false,"offset":0}' aria-label="On this page">
	<ul class="ltxe-opn__list"></ul>
</nav>`;

async function load(page: Page): Promise<void> {
	await page.setContent(html);
	await page.addStyleTag({ content: css });
	await page.addScriptTag({ content: js });
}

test.describe('F8 One Page Nav', () => {
	test('dots are visible and fixed to the configured side', async ({ page }) => {
		await load(page);
		await expect(page.locator('.ltxe-opn__btn')).toHaveCount(3);
		const pos = await page.locator('.ltxe-opn').evaluate((el) => getComputedStyle(el).right);
		expect(pos).not.toBe('auto');
	});

	test('clicking a dot scrolls to the corresponding section', async ({ page }) => {
		await load(page);
		await page.locator('.ltxe-opn__btn[data-target="about"]').click();
		await page.waitForTimeout(50);
		const top = await page.evaluate(() => window.scrollY);
		expect(top).toBeGreaterThan(400);
	});

	test('active dot changes as the user scrolls', async ({ page }) => {
		await load(page);
		await page.evaluate(() => window.scrollTo(0, 1000));
		await page.waitForTimeout(120);
		await expect(page.locator('.ltxe-opn__btn.is-active')).toHaveAttribute('data-target', /about|services|hero/);
	});

	test('tooltip shows section title on hover', async ({ page }) => {
		await load(page);
		const btn = page.locator('.ltxe-opn__btn[data-target="hero"]');
		await btn.dispatchEvent('mouseenter');
		await expect(btn).toHaveClass(/is-tip/);
		await expect(btn.locator('.ltxe-opn__tip')).toHaveText('Hero');
		await expect(btn.locator('.ltxe-opn__tip')).toBeVisible();
	});
});
