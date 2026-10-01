import { test, expect, type Page } from '@playwright/test';
import { readFileSync, existsSync } from 'fs';
import { join } from 'path';

const base = process.env.LTXE_E2E_BASE_URL || process.env.BASE_URL || 'http://127.0.0.1:8888';
const fixtureDir = join( process.cwd(), '../tests/fixtures' );
const csp = "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; connect-src 'self'; object-src 'none'; base-uri 'self'; form-action 'self'";

const mime: Record<string, string> = {
	'.html': 'text/html; charset=utf-8',
	'.js': 'text/javascript; charset=utf-8',
	'.css': 'text/css; charset=utf-8',
};

/**
 * U32 CSP — plan Browser Test Targets.
 *
 * Editor verify: Network tab
 * Frontend verify: Test page with CSP header active
 * Key assertions: 0 CSP errors in browser console
 * Demo: /demos/csp-safe/ (Playground: /demo-csp-safe/)
 */
async function gotoCspDemo( page: Page ): Promise<boolean> {
	const candidates = [ '/demos/csp-safe/', '/demo-csp-safe/' ];
	for ( const path of candidates ) {
		try {
			const response = await page.goto( `${ base }${ path }`, { waitUntil: 'domcontentloaded' } );
			if ( response && response.ok() ) {
				return true;
			}
		} catch ( e ) {
			// Local WP may be offline.
		}
	}
	return false;
}

test.describe( 'U32 CSP review', () => {
	test( 'strict CSP with no unsafe-inline reports 0 violations on plugin fixture', async ( { page } ) => {
		const violations: string[] = [];
		const consoleErrors: string[] = [];

		page.on( 'console', ( msg ) => {
			if ( 'error' === msg.type() && /Content-Security-Policy|Refused to/i.test( msg.text() ) ) {
				consoleErrors.push( msg.text() );
			}
		} );

		await page.route( 'https://ltxe.csp.test/**', async ( route ) => {
			const url = new URL( route.request().url() );
			const fileName = url.pathname.replace( /^\//, '' ) || 'u32-csp.html';
			const filePath = join( fixtureDir, fileName );
			if ( ! existsSync( filePath ) ) {
				await route.fulfill( { status: 404, body: 'not found' } );
				return;
			}
			const ext = fileName.includes( '.' ) ? fileName.slice( fileName.lastIndexOf( '.' ) ) : '.html';
			await route.fulfill( {
				status: 200,
				contentType: mime[ ext ] || 'application/octet-stream',
				headers: {
					'content-security-policy': csp,
				},
				body: readFileSync( filePath ),
			} );
		} );

		await page.addInitScript( () => {
			document.addEventListener( 'securitypolicyviolation', ( ev ) => {
				( window as unknown as { __ltxeCsp: string[] } ).__ltxeCsp =
					( window as unknown as { __ltxeCsp?: string[] } ).__ltxeCsp || [];
				( window as unknown as { __ltxeCsp: string[] } ).__ltxeCsp.push( ev.violatedDirective + ':' + ev.blockedURI );
			} );
		} );

		await page.goto( 'https://ltxe.csp.test/u32-csp.html', { waitUntil: 'domcontentloaded' } );

		await expect( page.locator( '[data-ltxe-action="copy-link"]' ) ).toBeVisible();
		await page.locator( '[data-ltxe-action="copy-link"]' ).click();
		await expect( page.locator( '[data-ltxe-copied="1"]' ) ).toHaveCount( 1 );

		const reported = await page.evaluate( () => ( window as unknown as { __ltxeCsp?: string[] } ).__ltxeCsp || [] );
		reported.forEach( ( v ) => violations.push( v ) );

		expect( violations, violations.join( '\n' ) ).toEqual( [] );
		expect( consoleErrors, consoleErrors.join( '\n' ) ).toEqual( [] );
	} );

	test( 'live demo (when available) uses data-ltxe-action and no onclick', async ( { page } ) => {
		const live = await gotoCspDemo( page );
		test.skip( ! live, 'Neither /demos/csp-safe/ nor /demo-csp-safe/ returned HTTP 200' );

		const onclickCount = await page.locator( '[onclick]' ).count();
		const jsHrefCount = await page.locator( '[href^="javascript:"]' ).count();
		expect( onclickCount ).toBe( 0 );
		expect( jsHrefCount ).toBe( 0 );
		await expect( page.locator( '[data-ltxe-action]' ).first() ).toBeVisible();
	} );
} );
