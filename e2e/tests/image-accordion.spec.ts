import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';

const css = readFileSync(join(process.cwd(), '../assets/css/image-accordion.css'), 'utf8');
const js = readFileSync(join(process.cwd(), '../assets/js/image-accordion.js'), 'utf8');

const html = `<div class="ltxe-ia ltxe-ia--click ltxe-ia--bottom-left" data-ltxe-ia='{"trigger":"click","open":0}'>
	<div class="ltxe-ia__panel is-open" tabindex="0" role="button" aria-expanded="true" aria-label="Architecture">
		<span class="ltxe-ia__overlay"></span>
		<div class="ltxe-ia__content">
			<h3 class="ltxe-ia__title">Architecture</h3>
			<p class="ltxe-ia__desc">Build spaces</p>
			<a class="ltxe-ia__btn" href="#arch">Learn More</a>
		</div>
	</div>
	<div class="ltxe-ia__panel" tabindex="0" role="button" aria-expanded="false" aria-label="Interior">
		<span class="ltxe-ia__overlay"></span>
		<div class="ltxe-ia__content">
			<h3 class="ltxe-ia__title">Interior</h3>
			<p class="ltxe-ia__desc">Design rooms</p>
			<a class="ltxe-ia__btn" href="#int">Learn More</a>
		</div>
	</div>
	<div class="ltxe-ia__panel" tabindex="0" role="button" aria-expanded="false" aria-label="Landscape">
		<span class="ltxe-ia__overlay"></span>
		<div class="ltxe-ia__content">
			<h3 class="ltxe-ia__title">Landscape</h3>
			<p class="ltxe-ia__desc">Shape land</p>
			<a class="ltxe-ia__btn" href="#land">Learn More</a>
		</div>
	</div>
</div>`;

async function load(page: Page, reduced = false): Promise<void> {
	if (reduced) {
		await page.emulateMedia({ reducedMotion: 'reduce' });
	}
	await page.setContent(html);
	await page.addStyleTag({ content: css });
	await page.addScriptTag({ content: js });
}

test.describe('F9 Image Accordion', () => {
	test('all panels render and default-open is expanded', async ({ page }) => {
		await load(page);
		await expect(page.locator('.ltxe-ia__panel')).toHaveCount(3);
		await expect(page.locator('.ltxe-ia__panel').first()).toHaveClass(/is-open/);
	});

	test('clicking a collapsed panel expands it and collapses others', async ({ page }) => {
		await load(page);
		await page.locator('.ltxe-ia__panel').nth(1).click();
		await expect(page.locator('.ltxe-ia__panel').nth(1)).toHaveClass(/is-open/);
		await expect(page.locator('.ltxe-ia__panel').first()).not.toHaveClass(/is-open/);
	});

	test('title and description are visible on the expanded panel', async ({ page }) => {
		await load(page);
		await expect(page.locator('.ltxe-ia__panel.is-open .ltxe-ia__title')).toHaveText('Architecture');
		await expect(page.locator('.ltxe-ia__panel.is-open .ltxe-ia__desc')).toHaveText('Build spaces');
	});

	test('button is visible only on the expanded panel', async ({ page }) => {
		await load(page);
		const openDisplay = await page.locator('.ltxe-ia__panel.is-open .ltxe-ia__btn').evaluate((el) => getComputedStyle(el).display);
		const closedDisplay = await page.locator('.ltxe-ia__panel').nth(1).locator('.ltxe-ia__btn').evaluate((el) => getComputedStyle(el).display);
		expect(openDisplay).not.toBe('none');
		expect(closedDisplay).toBe('none');
	});

	test('prefers-reduced-motion shows full content on every panel', async ({ page }) => {
		await load(page, true);
		await expect(page.locator('.ltxe-ia')).toHaveClass(/ltxe-ia--reduced/);
		await expect(page.locator('.ltxe-ia__panel.is-open')).toHaveCount(3);
	});

	test('keyboard Enter expands a focused panel', async ({ page }) => {
		await load(page);
		await page.locator('.ltxe-ia__panel').nth(2).focus();
		await page.keyboard.press('Enter');
		await expect(page.locator('.ltxe-ia__panel').nth(2)).toHaveClass(/is-open/);
	});
});
