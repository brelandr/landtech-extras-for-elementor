import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';

const css = readFileSync(join(process.cwd(), '../assets/css/logo-carousel.css'), 'utf8');
const js = readFileSync(join(process.cwd(), '../assets/js/logo-carousel.js'), 'utf8');

const html = `<div class="ltxe-logo-carousel ltxe-logo-carousel--marquee ltxe-logo-carousel--gray ltxe-logo-carousel--medium">
	<div class="ltxe-logo-carousel__track">
		<div class="ltxe-logo-carousel__set">
			<a class="ltxe-logo-carousel__link" href="#logo-target"><span class="ltxe-logo-carousel__item"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='40'%3E%3Crect width='120' height='40' fill='%231a1a2e'/%3E%3C/svg%3E" alt="Acme" /></span></a>
			<span class="ltxe-logo-carousel__item"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='40'%3E%3Crect width='120' height='40' fill='%23e94560'/%3E%3C/svg%3E" alt="North" /></span>
			<span class="ltxe-logo-carousel__item"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='40'%3E%3Crect width='120' height='40' fill='%234b5563'/%3E%3C/svg%3E" alt="West" /></span>
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

test.describe('F3 Logo Carousel', () => {
	test('all logo images are visible', async ({ page }) => {
		await load(page);
		const imgs = page.locator('.ltxe-logo-carousel img');
		await expect(imgs).toHaveCount(3);
		await expect(imgs.nth(0)).toBeVisible();
		await expect(imgs.nth(1)).toBeVisible();
		await expect(imgs.nth(2)).toBeVisible();
	});

	test('grayscale filter is applied', async ({ page }) => {
		await load(page);
		const filter = await page.locator('.ltxe-logo-carousel img').first().evaluate((el) => getComputedStyle(el).filter);
		expect(filter).toMatch(/grayscale/i);
	});

	test('animation is paused when prefers-reduced-motion is active', async ({ page }) => {
		await load(page, true);
		const anim = await page.locator('.ltxe-logo-carousel__track').evaluate((el) => getComputedStyle(el).animationName);
		expect(anim === 'none' || anim === '').toBeTruthy();
	});

	test('clicking a linked logo navigates to the URL', async ({ page }) => {
		await load(page, true);
		const link = page.locator('.ltxe-logo-carousel__link');
		await expect(link).toHaveAttribute('href', '#logo-target');
		await link.click();
		await expect(page).toHaveURL(/#logo-target/);
	});
});
