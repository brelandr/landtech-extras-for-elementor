import { test, expect, type Page } from '@playwright/test';

const base = process.env.LTXE_E2E_BASE_URL || process.env.BASE_URL || 'http://127.0.0.1:8888';

/**
 * U29 Loop Builder — plan Browser Test Targets.
 *
 * Editor verify: Loop Builder template
 * Frontend verify: Posts loop on test page
 * Key assertions: All widgets render correctly per post, no JS instance collision
 * Demo: /demos/loop-builder/ (Playground: /demo-loop-builder/)
 */
async function gotoLoopDemo( page: Page ): Promise<string> {
	const candidates = [ '/demos/loop-builder/', '/demo-loop-builder/' ];
	for ( const path of candidates ) {
		const response = await page.goto( `${ base }${ path }`, { waitUntil: 'domcontentloaded' } );
		if ( response && response.ok() ) {
			return path;
		}
	}
	throw new Error( 'Neither /demos/loop-builder/ nor /demo-loop-builder/ returned HTTP 200' );
}

test.describe( 'U29 Loop Builder', () => {
	test( 'repeating widgets render per instance without JS collision', async ( { page } ) => {
		await page.setViewportSize( { width: 375, height: 812 } );
		await gotoLoopDemo( page );

		await expect( page.locator( '.elementor-widget-posts-extra, .ee-loop' ).first() ).toBeVisible();
		await expect( page.locator( '.ee-gallery, .ee-gallery-wrapper' ).first() ).toBeVisible();
		await expect( page.locator( '.ltxe-testimonials' ).first() ).toBeVisible();
		await expect( page.locator( '.ltxe-team' ).first() ).toBeVisible();

		const postsCount = await page.locator( '.elementor-widget-posts-extra' ).count();
		const galleryCount = await page.locator( '.elementor-widget-gallery-extra' ).count();
		const testimonialsCount = await page.locator( '.elementor-widget-ltxe-testimonials' ).count();
		const teamCount = await page.locator( '.elementor-widget-ltxe-team-members' ).count();

		expect( postsCount ).toBeGreaterThanOrEqual( 2 );
		expect( galleryCount ).toBeGreaterThanOrEqual( 2 );
		expect( testimonialsCount ).toBeGreaterThanOrEqual( 2 );
		expect( teamCount ).toBeGreaterThanOrEqual( 2 );

		const collision = await page.evaluate( () => {
			const testimonialRoots = Array.from( document.querySelectorAll( '.ltxe-testimonials' ) );
			const teamRoots = Array.from( document.querySelectorAll( '.ltxe-team' ) );
			const galleryRoots = Array.from( document.querySelectorAll( '.ee-gallery-wrapper' ) );

			const uninitedTestimonials = testimonialRoots.filter( ( el ) => ! el.getAttribute( 'data-ltxe-tm-init' ) && el.querySelector( '.ltxe-testimonials__track' ) );
			const uninitedTeams = teamRoots.filter( ( el ) => ! el.getAttribute( 'data-ltxe-team-init' ) );
			const uninitedGalleries = galleryRoots.filter( ( el ) => ! el.getAttribute( 'data-ltxe-gallery-init' ) );

			return {
				uninitedTestimonials: uninitedTestimonials.length,
				uninitedTeams: uninitedTeams.length,
				uninitedGalleries: uninitedGalleries.length,
			};
		} );

		expect( collision.uninitedTestimonials ).toBe( 0 );
		expect( collision.uninitedTeams ).toBe( 0 );
		expect( collision.uninitedGalleries ).toBe( 0 );
	} );

	test( 'element_ready hooks exist and each demo inits its own instance', async ( { page } ) => {
		await page.goto( `${ base }/demo-testimonials/`, { waitUntil: 'domcontentloaded' } );
		await expect( page.locator( '.ltxe-testimonials' ).first() ).toBeVisible();

		await page.goto( `${ base }/demo-team-members/`, { waitUntil: 'domcontentloaded' } );
		await expect( page.locator( '.ltxe-team' ).first() ).toBeVisible();
		await expect( page.locator( '.ltxe-team[data-ltxe-team-init]' ).first() ).toBeVisible();

		await page.goto( `${ base }/demo-gallery/`, { waitUntil: 'domcontentloaded' } );
		await expect( page.locator( '.ee-gallery, .ee-gallery-wrapper' ).first() ).toBeVisible();

		await page.goto( `${ base }/demo-posts-extra/`, { waitUntil: 'domcontentloaded' } );
		await expect( page.locator( '.ee-loop, .elementor-widget-posts-extra' ).first() ).toBeVisible();

		const hookApi = await page.evaluate( () => {
			const hooks = window.elementorFrontend && window.elementorFrontend.hooks;
			return ! ! ( hooks && typeof hooks.addAction === 'function' );
		} );
		expect( hookApi ).toBeTruthy();
	} );
} );
