import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';

const css = readFileSync(join(process.cwd(), '../modules/media-gallery/assets/css/ltxe-media-gallery.css'), 'utf8');
const js = readFileSync(join(process.cwd(), '../modules/media-gallery/assets/js/ltxe-media-gallery.js'), 'utf8');

const html = `
<div class="ltxe-mg ltxe-mg--grid" data-layout="grid" data-gallery="ltxe-mg-1" data-lightbox="1">
	<div class="ltxe-mg__filters" role="toolbar">
		<button type="button" class="ltxe-mg__filter is-active" data-filter="*" aria-pressed="true">All</button>
		<button type="button" class="ltxe-mg__filter" data-filter=".type-photo" aria-pressed="false">Photos</button>
		<button type="button" class="ltxe-mg__filter" data-filter=".type-video" aria-pressed="false">Videos</button>
		<button type="button" class="ltxe-mg__filter" data-filter=".tag-architecture" aria-pressed="false">Architecture</button>
	</div>
	<div class="ltxe-mg__grid">
		<div class="ltxe-mg__item type-photo tag-architecture">
			<a class="ltxe-mg__link glightbox" href="https://example.com/photo.jpg" data-gallery="ltxe-mg-1" data-type="image"><img class="ltxe-mg__img" alt="Hall" src="https://example.com/photo.jpg" /></a>
		</div>
		<div class="ltxe-mg__item type-video tag-interiors">
			<a class="ltxe-mg__link glightbox" href="https://www.youtube.com/watch?v=XHOmBV4js_E" data-gallery="ltxe-mg-1" data-type="video"><img class="ltxe-mg__img" alt="Tour" src="https://img.youtube.com/vi/XHOmBV4js_E/maxresdefault.jpg" /><span class="ltxe-mg__play"></span></a>
		</div>
	</div>
</div>`;

async function loadGallery(page: Page): Promise<void> {
	await page.setContent(html, { waitUntil: 'domcontentloaded' });
	await page.addStyleTag({ content: css });
	await page.addScriptTag({
		content: `
			window.GLightbox = function (opts) {
				document.querySelectorAll(opts.selector).forEach(function (link) {
					link.addEventListener('click', function (event) {
						event.preventDefault();
						document.body.setAttribute('data-lb', link.getAttribute('data-type') + ':' + link.getAttribute('href'));
					});
				});
				return {};
			};
		`,
	});
	await page.addScriptTag({ content: js });
}

test.describe('G11 Media Gallery', () => {
	test.beforeEach(async ({ page }) => {
		await loadGallery(page);
	});

	test('renders grid items and a video play button', async ({ page }) => {
		await expect(page.locator('.ltxe-mg__item')).toHaveCount(2);
		await expect(page.locator('.ltxe-mg__play')).toBeVisible();
		const box = await page.locator('.ltxe-mg__play').boundingBox();
		expect(box && box.width).toBeGreaterThanOrEqual(44);
		expect(box && box.height).toBeGreaterThanOrEqual(44);
	});

	test('filter bar hides non-matching items', async ({ page }) => {
		await expect(page.locator('.ltxe-mg__filter')).toHaveCount(4);
		await page.getByRole('button', { name: 'Photos' }).click();
		await expect(page.locator('.ltxe-mg__item.type-video')).toBeHidden();
		await expect(page.locator('.ltxe-mg__item.type-photo')).toBeVisible();
	});

	test('photo and video clicks open the lightbox stub', async ({ page }) => {
		const errors: string[] = [];
		page.on('pageerror', (error) => errors.push(error.message));
		await page.locator('.type-photo .ltxe-mg__link').click();
		await expect(page.locator('body')).toHaveAttribute('data-lb', /image:/);
		await page.locator('.type-video .ltxe-mg__link').click();
		await expect(page.locator('body')).toHaveAttribute('data-lb', /video:/);
		expect(errors).toEqual([]);
	});

	test('YouTube thumbnail URL is maxresdefault', async () => {
		const php = readFileSync(join(process.cwd(), '../modules/media-gallery/class-media-gallery-urls.php'), 'utf8');
		expect(php).toContain('https://img.youtube.com/vi/');
		expect(php).toContain('/maxresdefault.jpg');
	});
});
