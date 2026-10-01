import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';

const css = readFileSync(join(process.cwd(), '../assets/css/flip-box.css'), 'utf8');
const js = readFileSync(join(process.cwd(), '../assets/js/flip-box.js'), 'utf8');

function boxHtml(trigger: 'hover' | 'click'): string {
	return `<div class="ltxe-flip-box ltxe-flip-box--horizontal ltxe-flip-box--${trigger} ltxe-flip-box--3d" tabindex="${trigger === 'click' ? '0' : '-1'}" role="button" aria-label="Front / Back">
		<div class="ltxe-flip-box__inner">
			<div class="ltxe-flip-box__face ltxe-flip-box__front"><h3 class="ltxe-flip-box__front-title">Front</h3></div>
			<div class="ltxe-flip-box__face ltxe-flip-box__back"><h3 class="ltxe-flip-box__back-title">Back</h3></div>
		</div>
	</div>`;
}

async function load(page: Page, trigger: 'hover' | 'click', reduced = false): Promise<void> {
	if (reduced) {
		await page.emulateMedia({ reducedMotion: 'reduce' });
	}
	await page.setContent(boxHtml(trigger));
	await page.addStyleTag({ content: css });
	await page.addScriptTag({ content: js });
}

async function innerTransform(page: Page): Promise<string> {
	return page.locator('.ltxe-flip-box__inner').evaluate((el) => getComputedStyle(el).transform);
}

test.describe('F4 Flip Box', () => {
	test('back face is not visible before hover or click', async ({ page }) => {
		await load(page, 'hover');
		const transform = await innerTransform(page);
		expect(transform === 'none' || transform.includes('matrix(1, 0, 0, 1, 0, 0)')).toBeTruthy();
		await expect(page.locator('.ltxe-flip-box__back-title')).toHaveCount(1);
	});

	test('hover trigger shows the back face after mouseenter', async ({ page }) => {
		await load(page, 'hover');
		await page.locator('.ltxe-flip-box').hover();
		await expect(page.locator('.ltxe-flip-box')).toHaveClass(/is-flipped/);
	});

	test('click trigger toggles on and off', async ({ page }) => {
		await load(page, 'click');
		const box = page.locator('.ltxe-flip-box');
		await box.click();
		await expect(box).toHaveClass(/is-flipped/);
		await box.click();
		await expect(box).not.toHaveClass(/is-flipped/);
	});

	test('Enter and Space flip a focused click widget', async ({ page }) => {
		await load(page, 'click');
		const box = page.locator('.ltxe-flip-box');
		await box.focus();
		await page.keyboard.press('Enter');
		await expect(box).toHaveClass(/is-flipped/);
		await page.keyboard.press(' ');
		await expect(box).not.toHaveClass(/is-flipped/);
	});

	test('prefers-reduced-motion stacks both faces', async ({ page }) => {
		await load(page, 'hover', true);
		const frontPos = await page.locator('.ltxe-flip-box__front').evaluate((el) => getComputedStyle(el).position);
		const backPos = await page.locator('.ltxe-flip-box__back').evaluate((el) => getComputedStyle(el).position);
		expect(frontPos).toBe('relative');
		expect(backPos).toBe('relative');
		await expect(page.locator('.ltxe-flip-box__front-title')).toBeVisible();
		await expect(page.locator('.ltxe-flip-box__back-title')).toBeVisible();
	});
});
