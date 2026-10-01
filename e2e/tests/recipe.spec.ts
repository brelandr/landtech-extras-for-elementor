import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';

const fixtureHtml = readFileSync(join(process.cwd(), '../tests/fixtures/recipe.html'), 'utf8');
const js = readFileSync(join(process.cwd(), '../modules/recipe/assets/js/ltxe-recipe.js'), 'utf8');
const css = readFileSync(join(process.cwd(), '../modules/recipe/assets/css/ltxe-recipe.css'), 'utf8');

async function loadRecipe(page: Page): Promise<void> {
	await page.setViewportSize({ width: 1280, height: 800 });
	await page.setContent(fixtureHtml);
	await page.addStyleTag({ content: css });
	await page.addScriptTag({ content: js });
}

test.describe('G2 Recipe', () => {
	test('renders all sections', async ({ page }) => {
		await loadRecipe(page);
		await expect(page.locator('.ltxe-recipe__title')).toHaveText('Classic Chocolate Chip Cookies');
		await expect(page.locator('.ltxe-recipe__ingredients li')).toHaveCount(1);
		await expect(page.locator('.ltxe-recipe__steps li')).toHaveCount(1);
		await expect(page.locator('.ltxe-recipe__nutrition li')).toHaveCount(1);
	});

	test('JSON-LD Recipe schema is present with ISO durations', async ({ page }) => {
		await loadRecipe(page);
		const json = await page.locator('script[type="application/ld+json"]').innerText();
		const data = JSON.parse(json);
		expect(data['@type']).toBe('Recipe');
		expect(data.totalTime).toBe('PT30M');
		expect(data.prepTime).toBe('PT15M');
	});

	test('print button calls window.print', async ({ page }) => {
		await loadRecipe(page);
		await page.evaluate(() => {
			(window as unknown as { __ltxePrinted?: boolean }).__ltxePrinted = false;
			window.print = function () {
				(window as unknown as { __ltxePrinted?: boolean }).__ltxePrinted = true;
			};
		});
		await page.locator('.ltxe-recipe__print').click();
		await expect.poll(async () => page.evaluate(() => (window as unknown as { __ltxePrinted?: boolean }).__ltxePrinted)).toBe(true);
	});
});
