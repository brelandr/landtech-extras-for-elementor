import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';

const css = readFileSync(join(process.cwd(), '../assets/css/posts-carousel.css'), 'utf8');
const js = readFileSync(join(process.cwd(), '../assets/js/posts-carousel.js'), 'utf8');

const html = `<div class="ltxe-pc" data-ltxe-pc='{"slides":3,"tablet":2,"mobile":1,"delay":200,"loop":true,"autoplay":true}'>
	<div class="ltxe-pc__viewport">
		<div class="ltxe-pc__track">
			<article class="ltxe-pc__slide" data-title="One"><div class="ltxe-pc__card"><h3 class="ltxe-pc__title">One</h3></div></article>
			<article class="ltxe-pc__slide" data-title="Two"><div class="ltxe-pc__card"><h3 class="ltxe-pc__title">Two</h3></div></article>
			<article class="ltxe-pc__slide" data-title="Three"><div class="ltxe-pc__card"><h3 class="ltxe-pc__title">Three</h3></div></article>
			<article class="ltxe-pc__slide" data-title="Four"><div class="ltxe-pc__card"><h3 class="ltxe-pc__title">Four</h3></div></article>
			<article class="ltxe-pc__slide" data-title="Five"><div class="ltxe-pc__card"><h3 class="ltxe-pc__title">Five</h3></div></article>
			<article class="ltxe-pc__slide" data-title="Six"><div class="ltxe-pc__card"><h3 class="ltxe-pc__title">Six</h3></div></article>
		</div>
	</div>
	<div class="ltxe-pc__nav">
		<button type="button" class="ltxe-pc__prev">Prev</button>
		<button type="button" class="ltxe-pc__next">Next</button>
	</div>
</div>`;

async function load(page: Page): Promise<void> {
	await page.setContent(html);
	await page.addStyleTag({ content: css });
	await page.addScriptTag({ content: js });
}

test.describe('F6 Post Carousel', () => {
	test('multiple post cards are rendered', async ({ page }) => {
		await load(page);
		await expect(page.locator('.ltxe-pc__slide')).toHaveCount(6);
	});

	test('next arrow advances to the next slide', async ({ page }) => {
		await load(page);
		await expect(page.locator('.ltxe-pc')).toHaveAttribute('data-index', '0');
		await page.locator('.ltxe-pc__next').click();
		await expect(page.locator('.ltxe-pc')).toHaveAttribute('data-index', '1');
		await expect(page.locator('.ltxe-pc')).toHaveAttribute('data-active-title', 'Two');
	});

	test('autoplay advances after configured delay', async ({ page }) => {
		await load(page);
		await expect(page.locator('.ltxe-pc')).toHaveAttribute('data-index', '0');
		await page.waitForTimeout(280);
		const index = await page.locator('.ltxe-pc').getAttribute('data-index');
		expect(Number(index)).toBeGreaterThan(0);
	});

	test('loop wraps from last slide back to first', async ({ page }) => {
		await load(page);
		for (let i = 0; i < 6; i++) {
			await page.locator('.ltxe-pc__next').click();
		}
		await expect(page.locator('.ltxe-pc')).toHaveAttribute('data-index', '0');
		await expect(page.locator('.ltxe-pc')).toHaveAttribute('data-active-title', 'One');
	});

	test('slides-per-view changes at 768 and 1024', async ({ page }) => {
		await page.setViewportSize({ width: 1100, height: 800 });
		await load(page);
		const desktop = await page.locator('.ltxe-pc__slide').first().evaluate((el) => el.getBoundingClientRect().width);
		await page.setViewportSize({ width: 800, height: 800 });
		await page.waitForTimeout(50);
		const tablet = await page.locator('.ltxe-pc__slide').first().evaluate((el) => el.getBoundingClientRect().width);
		await page.setViewportSize({ width: 500, height: 800 });
		await page.waitForTimeout(50);
		const mobile = await page.locator('.ltxe-pc__slide').first().evaluate((el) => el.getBoundingClientRect().width);
		expect(mobile).toBeGreaterThan(tablet);
		expect(tablet).toBeGreaterThan(desktop);
	});
});
