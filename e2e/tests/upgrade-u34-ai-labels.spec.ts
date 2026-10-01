import { test, expect, type Page } from '@playwright/test';

const base = process.env.LTXE_E2E_BASE_URL || process.env.BASE_URL || 'http://127.0.0.1:8888';

/**
 * U34 AI Widget Labels — plan Browser Test Targets.
 *
 * Editor verify: Add heading widget with content
 * Frontend verify: Panel layer list / CSS ID field
 * Key assertions: Header button calls landtech-extras/v1/ai/widget-label and fills _element_id
 * Demo: /demos/ai-labels/ (Playground: /demo-ai-labels/)
 */
async function gotoLabelsDemo( page: Page ): Promise<boolean> {
	const candidates = [ '/demos/ai-labels/', '/demo-ai-labels/' ];
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

test.describe( 'U34 AI widget labels', () => {
	test( 'REST route is registered for editors', async ( { request } ) => {
		const payload = {
			data: {
				widget_type: 'heading',
				content: 'Welcome to our services',
			},
		};
		// Pretty /wp-json/ can 404 on this wp-env Apache map; rest_route is the same endpoint.
		let res = await request.post( `${ base }/wp-json/landtech-extras/v1/ai/widget-label`, payload );
		if ( 404 === res.status() ) {
			res = await request.post( `${ base }/?rest_route=/landtech-extras/v1/ai/widget-label`, payload );
		}
		// Logged-out requests must not succeed (permission_callback is edit_posts).
		expect( [ 401, 403 ] ).toContain( res.status() );
	} );

	test( 'demo page renders heading content for the editor flow', async ( { page } ) => {
		const live = await gotoLabelsDemo( page );
		test.skip( ! live, 'Neither /demos/ai-labels/ nor /demo-ai-labels/ returned HTTP 200' );
		await expect( page.getByText( /Welcome to our services|Build the next page faster/i ).first() ).toBeVisible();
	} );
} );
