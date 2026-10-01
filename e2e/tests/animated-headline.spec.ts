import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';

const css = readFileSync(join(process.cwd(), '../assets/css/animated-headline.css'), 'utf8');
const js = readFileSync(join(process.cwd(), '../assets/js/animated-headline.js'), 'utf8');
const php = readFileSync(join(process.cwd(), '../modules/animated-headline/widgets/animated-headline.php'), 'utf8');

function headline(effect: string, hold = 400): string {
	return `<h2 class="ltxe-ah ltxe-ah--${effect}" data-ltxe-ah='{"effect":"${effect}","words":["websites","apps","brands"],"speed":40,"hold":${hold},"cursor":true,"char":"|"}'>
		<span class="ltxe-ah__before">We build</span>
		<span class="ltxe-ah__word" data-ltxe-ah-word>websites</span>
		<span class="ltxe-ah__cursor">|</span>
		<span class="ltxe-ah__after">for you</span>
	</h2>`;
}

async function load(page: Page, html: string, reduced = false): Promise<void> {
	if (reduced) {
		await page.emulateMedia({ reducedMotion: 'reduce' });
	}
	await page.setContent(html);
	await page.addStyleTag({ content: css });
	await page.addScriptTag({ content: js });
}

test.describe('F7 Animated Headline', () => {
	test('static before and after text is visible', async ({ page }) => {
		await load(page, headline('word-cycle-fade'));
		await expect(page.locator('.ltxe-ah__before')).toHaveText('We build');
		await expect(page.locator('.ltxe-ah__after')).toHaveText('for you');
	});

	test('first animated word is visible on load', async ({ page }) => {
		await load(page, headline('word-cycle-fade'));
		await expect(page.locator('[data-ltxe-ah-word]')).toHaveText('websites');
	});

	test('word transitions to the second word after hold', async ({ page }) => {
		await load(page, headline('word-cycle-fade', 350));
		await expect(page.locator('[data-ltxe-ah-word]')).toHaveText('apps', { timeout: 2000 });
	});

	test('typewriter builds character by character', async ({ page }) => {
		await load(page, headline('typewriter', 800));
		await page.waitForTimeout(90);
		const text = await page.locator('[data-ltxe-ah-word]').textContent();
		expect(text === '' || (text && text.length < 'websites'.length)).toBeTruthy();
		await expect(page.locator('[data-ltxe-ah-word]')).toHaveText('websites', { timeout: 2000 });
	});

	test('prefers-reduced-motion shows all words at once', async ({ page }) => {
		await load(page, headline('typewriter'), true);
		await expect(page.locator('.ltxe-ah__all')).toHaveText('websites, apps, brands');
	});

	test('premium upsell notice exists in the editor control', async () => {
		expect(php).toContain('Unlock 15+ effects including Split Text, Gradient Reveal, and Scramble');
	});
});
