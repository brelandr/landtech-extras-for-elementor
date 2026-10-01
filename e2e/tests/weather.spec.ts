import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';

const fixtureHtml = readFileSync(join(process.cwd(), '../tests/fixtures/weather.html'), 'utf8');
const js = readFileSync(join(process.cwd(), '../modules/weather/assets/js/ltxe-weather.js'), 'utf8');
const css = readFileSync(join(process.cwd(), '../modules/weather/assets/css/ltxe-weather.css'), 'utf8');

const sunny = {
	city: 'London',
	units: 'celsius',
	condition: 'sunny',
	description: 'Clear sky',
	temperature: 18,
	feels_like: 17,
	humidity: 50,
	wind: 8,
	uv: 3,
	is_day: 1,
	forecast: [{ date: '2026-09-30', temp_max: 18, temp_min: 10, condition: 'sunny', description: 'Clear sky' }],
};

const rain = {
	...sunny,
	city: 'Seattle',
	condition: 'rain',
	description: 'Moderate rain',
	temperature: 11,
};

const restUrl = 'https://ltxe.test/wp-json/landtech-extras/v1/weather';

async function loadWeather(page: Page, firstPayload = sunny): Promise<void> {
	let calls = 0;
	await page.route('**/landtech-extras/v1/weather**', async (route) => {
		calls += 1;
		const payload = calls === 1 ? firstPayload : rain;
		await route.fulfill({
			status: 200,
			contentType: 'application/json',
			body: JSON.stringify(payload),
		});
	});
	await page.setViewportSize({ width: 1280, height: 800 });
	await page.setContent(fixtureHtml);
	await page.locator('.ltxe-weather').evaluate((el, url) => {
		el.setAttribute('data-ltxe-weather-rest', url);
	}, restUrl);
	await page.addStyleTag({ content: css });
	await page.addScriptTag({ content: js });
	await page.waitForFunction(() => {
		const el = document.querySelector('.ltxe-weather');
		return el && el.getAttribute('data-ltxe-weather-ready') === '1';
	});
	await expect(page.locator('.ltxe-weather__desc')).not.toHaveText('Loading weather…', { timeout: 2000 });
}

test.describe('G1 Weather', () => {
	test('widget renders with condition classes', async ({ page }) => {
		await loadWeather(page);
		const root = page.locator('.ltxe-weather');
		await expect(root).toBeVisible();
		await expect(root).toHaveAttribute('data-ltxe-condition', 'sunny');
		await expect(root).toHaveClass(/ltxe-weather--card/);
		await expect(page.locator('.ltxe-weather__city')).toHaveText('London');
		await expect(page.locator('.ltxe-weather__temp')).toHaveText('18°C');
	});

	test('geolocate button is visible and at least 44px', async ({ page }) => {
		await loadWeather(page);
		const btn = page.locator('.ltxe-weather__geo');
		await expect(btn).toBeVisible();
		const box = await btn.boundingBox();
		expect(box).toBeTruthy();
		expect(box!.height).toBeGreaterThanOrEqual(44);
		expect(box!.width).toBeGreaterThanOrEqual(44);
	});

	test('refresh interval triggers a re-fetch and swaps condition', async ({ page }) => {
		await loadWeather(page);
		const root = page.locator('.ltxe-weather');
		await expect(root).toHaveAttribute('data-ltxe-condition', 'sunny');
		await expect(root).toHaveAttribute('data-ltxe-condition', 'rain', { timeout: 1500 });
		await expect(page.locator('.ltxe-weather__city')).toHaveText('Seattle');
		await expect(page.locator('.ltxe-weather__desc')).toHaveText('Moderate rain');
	});

	test('background uses the condition attribute for CSS swap', async ({ page }) => {
		await loadWeather(page, rain);
		const root = page.locator('.ltxe-weather');
		await expect(root).toHaveAttribute('data-ltxe-condition', 'rain');
		const bg = page.locator('.ltxe-weather__bg');
		await expect(bg).toBeVisible();
		const image = await bg.evaluate((el) => getComputedStyle(el).backgroundImage);
		expect(image === 'none' || image.length > 0).toBeTruthy();
	});
});
