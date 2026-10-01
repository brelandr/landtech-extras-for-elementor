import { test, expect, type Page } from '@playwright/test';
import { readFileSync } from 'fs';
import { join } from 'path';

const base = process.env.LTXE_E2E_BASE_URL || process.env.BASE_URL || 'http://127.0.0.1:8888';
const fixturePath = join( process.cwd(), '../tests/fixtures/u30-wcag.html' );

/**
 * U30 WCAG 2.2 — plan Browser Test Targets.
 *
 * Editor verify: axe DevTools on all widget output
 * Frontend verify: All widget test pages
 * Key assertions: 0 critical violations, keyboard nav passes all interactive widgets
 * Demo: /demos/accessibility/ (Playground: /demo-accessibility/)
 */
async function gotoA11yDemo( page: Page ): Promise<boolean> {
	const candidates = [ '/demos/accessibility/', '/demo-accessibility/' ];
	for ( const path of candidates ) {
		try {
			const response = await page.goto( `${ base }${ path }`, { waitUntil: 'domcontentloaded' } );
			if ( response && response.ok() ) {
				return true;
			}
		} catch ( e ) {
			// Local WP may be offline — fixture test still covers markup.
		}
	}
	return false;
}

test.describe( 'U30 WCAG 2.2 audit', () => {
	test( 'fixture markup has roles, ARIA, range handle, and 44px targets', async ( { page } ) => {
		await page.setContent( readFileSync( fixturePath, 'utf8' ) );

		await expect( page.locator( '.ee-popup__content[role="dialog"]' ) ).toHaveAttribute( 'aria-modal', 'true' );
		await expect( page.locator( '.ee-popup__trigger' ) ).toHaveAttribute( 'aria-expanded', 'false' );
		await expect( page.locator( '.ee-popup__trigger' ) ).toHaveAttribute( 'aria-controls', 'ltxe_popup__trigger-demo' );

		await expect( page.locator( '[role="tablist"]' ) ).toHaveCount( 1 );
		await expect( page.locator( '[role="tab"]' ).first() ).toHaveAttribute( 'aria-selected', 'true' );
		await expect( page.locator( '[role="tabpanel"]' ).first() ).toBeVisible();

		const handle = page.locator( 'input.ltxe-ic__handle[type="range"]' );
		await expect( handle ).toBeVisible();
		await expect( handle ).toHaveAttribute( 'aria-label', 'Image comparison slider' );
		await handle.focus();
		await page.keyboard.press( 'ArrowRight' );
		await expect( handle ).toHaveValue( '51' );

		await expect( page.locator( '[aria-roledescription="carousel"]' ) ).toBeVisible();
		await expect( page.locator( '.ltxe-testimonials__pause' ) ).toHaveAttribute( 'aria-pressed', 'false' );
		await expect( page.locator( '.ltxe-countdown' ) ).toHaveAttribute( 'aria-live', 'polite' );
		await expect( page.locator( '.ltxe-countdown' ) ).toHaveAttribute( 'aria-atomic', 'true' );
		await expect( page.locator( '.ltxe-faq__question' ) ).toHaveAttribute( 'aria-expanded', 'false' );
		await expect( page.locator( '.ltxe-pricing-toggle__btn.is-active' ) ).toHaveAttribute( 'aria-pressed', 'true' );

		const sizes = await page.evaluate( () => {
			const selectors = [
				'.ee-popup__trigger',
				'.ee-popup__close',
				'.ltxe-tabs__tab',
				'.ltxe-testimonials__btn--next',
				'.ltxe-faq__question',
				'.ltxe-pricing-toggle__btn',
			];
			return selectors.map( ( sel ) => {
				const el = document.querySelector( sel ) as HTMLElement | null;
				if ( ! el ) {
					return { sel, w: 0, h: 0 };
				}
				const box = el.getBoundingClientRect();
				return { sel, w: box.width, h: box.height };
			} );
		} );

		sizes.forEach( ( item ) => {
			expect( item.w, item.sel ).toBeGreaterThanOrEqual( 44 );
			expect( item.h, item.sel ).toBeGreaterThanOrEqual( 44 );
		} );
	} );

	test( 'live demo (when available) exposes keyboard-accessible interactive widgets', async ( { page } ) => {
		const live = await gotoA11yDemo( page );
		test.skip( ! live, 'Neither /demos/accessibility/ nor /demo-accessibility/ returned HTTP 200' );

		const tabs = page.locator( '.ltxe-tabs__tab' );
		if ( await tabs.count() ) {
			await tabs.first().focus();
			await page.keyboard.press( 'ArrowRight' );
			await expect( tabs.nth( 1 ) ).toHaveAttribute( 'aria-selected', 'true' );
		}

		const range = page.locator( 'input.ltxe-ic__handle, input.ee-image-comparison__handle[type="range"]' ).first();
		if ( await range.count() ) {
			await expect( range ).toBeVisible();
		}

		const pause = page.locator( '.ltxe-testimonials__pause' ).first();
		if ( await pause.count() ) {
			await expect( pause ).toHaveAttribute( 'aria-pressed', /true|false/ );
		}

		await expect( page.locator( '.ltxe-countdown[aria-live="polite"]' ).first() ).toBeVisible();
	} );
} );
