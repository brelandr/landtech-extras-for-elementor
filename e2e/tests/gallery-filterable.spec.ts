import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';

const css = readFileSync(join(process.cwd(), '../assets/css/gallery-filterable.css'), 'utf8');
const js = readFileSync(join(process.cwd(), '../assets/js/gallery-filterable.js'), 'utf8');

const html = `<div class="ltxe-gf ltxe-gf--pill ltxe-gf--above" data-ltxe-gf='{"layout":"masonry","anim":"fade"}'>
	<div class="ltxe-gf__bar" role="toolbar">
		<button type="button" class="ltxe-gf__btn is-active" data-filter="*" aria-pressed="true">All</button>
		<button type="button" class="ltxe-gf__btn" data-filter=".cat-architecture" aria-pressed="false">Architecture</button>
		<button type="button" class="ltxe-gf__btn" data-filter=".cat-interior" aria-pressed="false">Interior</button>
	</div>
	<div class="ltxe-gf__grid">
		<figure class="ltxe-gf__item cat-architecture"><span class="ltxe-gf__ph">A1</span></figure>
		<figure class="ltxe-gf__item cat-interior"><span class="ltxe-gf__ph">I1</span></figure>
		<figure class="ltxe-gf__item cat-architecture"><span class="ltxe-gf__ph">A2</span></figure>
		<figure class="ltxe-gf__item cat-interior"><span class="ltxe-gf__ph">I2</span></figure>
		<figure class="ltxe-gf__item cat-landscape"><span class="ltxe-gf__ph">L1</span></figure>
		<figure class="ltxe-gf__item cat-people"><span class="ltxe-gf__ph">P1</span></figure>
		<figure class="ltxe-gf__item cat-landscape"><span class="ltxe-gf__ph">L2</span></figure>
		<figure class="ltxe-gf__item cat-people"><span class="ltxe-gf__ph">P2</span></figure>
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

test.describe('F5 Filterable Gallery', () => {
	test('all items are visible on load', async ({ page }) => {
		await load(page);
		await expect(page.locator('.ltxe-gf__item')).toHaveCount(8);
		await expect(page.locator('.ltxe-gf__item.is-hidden')).toHaveCount(0);
	});

	test('category filter hides items not in that category', async ({ page }) => {
		await load(page);
		await page.locator('.ltxe-gf__btn[data-filter=".cat-architecture"]').click();
		await expect(page.locator('.ltxe-gf__item.cat-architecture').first()).not.toHaveClass(/is-hidden/);
		await expect(page.locator('.ltxe-gf__item.cat-interior').first()).toHaveClass(/is-hidden/);
	});

	test('All restores every item', async ({ page }) => {
		await load(page);
		await page.locator('.ltxe-gf__btn[data-filter=".cat-architecture"]').click();
		await page.locator('.ltxe-gf__btn[data-filter="*"]').click();
		await expect(page.locator('.ltxe-gf__item.is-hidden')).toHaveCount(0);
	});

	test('masonry items do not share the same top-left', async ({ page }) => {
		await load(page);
		const boxes = await page.locator('.ltxe-gf__item').evaluateAll((els) =>
			els.map((el) => {
				const r = el.getBoundingClientRect();
				return `${Math.round(r.left)}:${Math.round(r.top)}`;
			})
		);
		expect(new Set(boxes).size).toBe(boxes.length);
	});

	test('prefers-reduced-motion keeps layout changes instant', async ({ page }) => {
		await load(page, true);
		const start = Date.now();
		await page.locator('.ltxe-gf__btn[data-filter=".cat-interior"]').click();
		await expect(page.locator('.ltxe-gf__item.cat-architecture').first()).toHaveClass(/is-hidden/);
		expect(Date.now() - start).toBeLessThan(250);
	});
});
