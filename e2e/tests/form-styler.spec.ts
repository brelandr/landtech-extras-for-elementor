import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';

const fixturePath = join(process.cwd(), '../tests/fixtures/form-styler.html');
const cssPath = join(process.cwd(), '../assets/css/form-styler.css');

async function loadFormStyler(page: Page): Promise<void> {
	await page.setViewportSize({ width: 1280, height: 800 });
	let html = readFileSync(fixturePath, 'utf8');
	html = html.replace(/<link[\s\S]*?>/g, '').replace(/<script[\s\S]*?<\/script>/g, '');
	await page.setContent(html, { waitUntil: 'domcontentloaded' });
	await page.addStyleTag({ content: readFileSync(cssPath, 'utf8') });
	await page.addScriptTag({
		content: `
			document.querySelectorAll('.ltxe-form-styler__preview').forEach(function (form) {
				form.addEventListener('submit', function (e) {
					e.preventDefault();
					var empty = Array.prototype.some.call(form.querySelectorAll('[required]'), function (input) {
						return !String(input.value || '').trim();
					});
					var error = form.querySelector('.ltxe-form-styler__error');
					var success = form.querySelector('.ltxe-form-styler__success');
					if (error) { error.hidden = !empty; }
					if (success) { success.hidden = empty; }
				});
			});
		`,
	});
}

test.describe('F1 Form Styler', () => {
	test.beforeEach(async ({ page }) => {
		await loadFormStyler(page);
	});

	test('CF7 styler widget renders and the form is visible', async ({ page }) => {
		const form = page.locator('.ltxe-form-styler form, .ltxe-form-styler .wpcf7-form').first();
		await expect(form).toBeVisible();
	});

	test('submit with empty required fields shows error color', async ({ page }) => {
		const form = page.locator('.ltxe-form-styler form').first();
		await expect(form).toBeVisible();
		await form.locator('button[type="submit"], input[type="submit"]').first().click();
		const error = page.locator('.ltxe-form-styler__error, .wpcf7-not-valid-tip, .wpforms-error').first();
		await expect(error).toBeVisible();
		const color = await error.evaluate((el) => getComputedStyle(el).color);
		expect(color).toMatch(/rgb\(185,\s*28,\s*28\)|#b91c1c/i);
	});

	test('button background matches configured color', async ({ page }) => {
		const btn = page.locator('.ltxe-form-styler .ltxe-form-styler__submit, .ltxe-form-styler .wpcf7-submit, .ltxe-form-styler input[type="submit"]').first();
		await expect(btn).toBeVisible();
		const bg = await btn.evaluate((el) => getComputedStyle(el).backgroundColor);
		expect(bg).toMatch(/rgb\(233,\s*69,\s*96\)|#e94560/i);
	});

	test('Gravity Forms upsell is an editor notice with the premium sentence', async () => {
		const modulePhp = readFileSync(join(process.cwd(), '../modules/form-styler/module.php'), 'utf8');
		const editorJs = readFileSync(join(process.cwd(), '../assets/js/form-styler-gf-upsell.js'), 'utf8');
		expect(modulePhp).toContain("add_action( 'elementor/editor/after_enqueue_scripts'");
		expect(modulePhp).toContain("'ltxe_gf_upsell_dismissed'");
		expect(modulePhp).toContain('Using Gravity Forms? Style your forms with the Gravity Forms Styler');
		expect(modulePhp).toContain('https://extrasforelementor.com/#premium');
		expect(modulePhp).not.toContain('wp_enqueue_scripts');
		expect(editorJs).toContain('elementor-panel-category-landtech-extras');
		expect(editorJs).toContain('textContent');
	});

	test('shows notice when CF7 is not active', async ({ page }) => {
		const notice = page.locator('.ltxe-form-styler__notice');
		if (await notice.count()) {
			await expect(notice.first()).toContainText(/Contact Form 7 is not installed or active/i);
		} else {
			await expect(page.locator('.wpcf7, .wpcf7-form')).toBeVisible();
		}
	});
});
