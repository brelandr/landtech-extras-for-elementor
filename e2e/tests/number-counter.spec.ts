import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';

const fixtureHtml = readFileSync(join(process.cwd(), '../tests/fixtures/number-counter.html'), 'utf8');
const js = readFileSync(join(process.cwd(), '../assets/js/number-counter.js'), 'utf8');
const css = readFileSync(join(process.cwd(), '../assets/css/number-counter.css'), 'utf8');

async function loadCounter(page: Page, reduced = false): Promise<void> {
	await page.setViewportSize({ width: 1280, height: 800 });
	if (reduced) {
		await page.emulateMedia({ reducedMotion: 'reduce' });
	}
	await page.setContent(fixtureHtml);
	await page.addStyleTag({ content: css });
	await page.addScriptTag({ content: js });
}

test.describe('F2 Number Counter', () => {
	test('starts at 0 and reaches the target within duration + 500ms', async ({ page }) => {
		await loadCounter(page);
		const digits = page.locator('.ltxe-number-counter__digits');
		await expect(digits).toHaveText('0');
		await expect(digits).toHaveText('500', { timeout: 900 });
	});

	test('prefix and suffix are visible', async ({ page }) => {
		await loadCounter(page);
		await expect(page.locator('.ltxe-number-counter__prefix')).toHaveText('$');
		await expect(page.locator('.ltxe-number-counter__suffix')).toHaveText('+');
	});

	test('prefers-reduced-motion jumps to the final value', async ({ page }) => {
		await loadCounter(page, true);
		await expect(page.locator('.ltxe-number-counter__digits')).toHaveText('500', { timeout: 200 });
	});

	test('does not replay when trigger once is on', async ({ page }) => {
		await loadCounter(page);
		const digits = page.locator('.ltxe-number-counter__digits');
		await expect(digits).toHaveText('500', { timeout: 900 });
		await page.locator('#bottom').scrollIntoViewIfNeeded();
		await page.waitForTimeout(200);
		await page.locator('.ltxe-number-counter').scrollIntoViewIfNeeded();
		await page.waitForTimeout(200);
		await expect(digits).toHaveText('500');
		const done = await page.locator('.ltxe-number-counter').getAttribute('data-ltxe-counter-done');
		expect(done).toBe('1');
	});
});
