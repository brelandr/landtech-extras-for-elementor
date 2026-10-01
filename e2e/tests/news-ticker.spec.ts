import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';

const css = readFileSync(join(process.cwd(), '../assets/css/news-ticker.css'), 'utf8');
const js = readFileSync(join(process.cwd(), '../assets/js/news-ticker.js'), 'utf8');

const html = `<div class="ltxe-nt ltxe-nt--medium ltxe-nt--left ltxe-nt--pause" data-ltxe-nt="1">
	<span class="ltxe-nt__label">Latest:</span>
	<div class="ltxe-nt__viewport">
		<div class="ltxe-nt__track">
			<span class="ltxe-nt__set">
				<span class="ltxe-nt__item">First update</span>
				<span class="ltxe-nt__sep">•</span>
				<span class="ltxe-nt__item">Second update</span>
				<span class="ltxe-nt__sep">•</span>
				<span class="ltxe-nt__item">Third update</span>
			</span>
			<span class="ltxe-nt__set">
				<span class="ltxe-nt__item">First update</span>
				<span class="ltxe-nt__sep">•</span>
				<span class="ltxe-nt__item">Second update</span>
				<span class="ltxe-nt__sep">•</span>
				<span class="ltxe-nt__item">Third update</span>
			</span>
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

test.describe('F10 News Ticker', () => {
	test('all items are visible in the ticker', async ({ page }) => {
		await load(page);
		await expect(page.locator('.ltxe-nt__item').first()).toHaveText('First update');
		await expect(page.locator('.ltxe-nt__item').nth(1)).toHaveText('Second update');
		await expect(page.locator('.ltxe-nt__item').nth(2)).toHaveText('Third update');
	});

	test('ticker is scrolling', async ({ page }) => {
		await load(page);
		const state = await page.locator('.ltxe-nt__track').evaluate((el) => getComputedStyle(el).animationPlayState);
		expect(state).toBe('running');
	});

	test('hover pauses the animation', async ({ page }) => {
		await load(page);
		await page.locator('.ltxe-nt').hover();
		const state = await page.locator('.ltxe-nt__track').evaluate((el) => getComputedStyle(el).animationPlayState);
		expect(state).toBe('paused');
	});

	test('prefers-reduced-motion lists items statically', async ({ page }) => {
		await load(page, true);
		await expect(page.locator('.ltxe-nt')).toHaveClass(/ltxe-nt--reduced/);
		const anim = await page.locator('.ltxe-nt__track').evaluate((el) => getComputedStyle(el).animationName);
		expect(anim === 'none' || anim === '').toBeTruthy();
	});

	test('label text is visible before the scrolling content', async ({ page }) => {
		await load(page);
		await expect(page.locator('.ltxe-nt__label')).toHaveText('Latest:');
	});
});
