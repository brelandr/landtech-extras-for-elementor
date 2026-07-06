#!/usr/bin/env node
/**
 * Capture WordPress.org plugin screenshots from a local Playground site.
 *
 * Prerequisite: start Playground first, e.g.
 *   npx @wp-playground/cli@latest start --path=. --port=9400 --skip-browser --login \
 *     --blueprint=scripts/playground-screenshots-blueprint.json
 *
 * Usage:
 *   node scripts/capture-wordpress-org-screenshots.mjs
 *   PLAYGROUND_URL=http://127.0.0.1:9400 node scripts/capture-wordpress-org-screenshots.mjs
 */
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import puppeteer from 'puppeteer';

const __dirname = path.dirname( fileURLToPath( import.meta.url ) );
const ROOT = path.resolve( __dirname, '..' );
const BASE = process.env.PLAYGROUND_URL || 'http://127.0.0.1:9400';
const ORG_ASSETS = path.join( ROOT, '.plugin-check', '.wordpress-org' );
const SVN_ASSETS = path.resolve( ROOT, '..', 'landtech-extras-for-elementor-svn', 'assets' );

const shots = [
	{
		file: 'screenshot-1.png',
		url: `${ BASE }/wp-admin/admin.php?page=landtech-extras#landtech_extras_widgets`,
		caption: 'Enable or disable individual LandTech Extras widgets from Elementor → LandTech Extras.',
		waitMs: 1200,
		before: async ( page ) => {
			await page.click( 'a[href="#landtech_extras_widgets"]' );
			await delay( 400 );
			await hideAdminChrome( page );
		},
	},
	{
		file: 'screenshot-2.png',
		url: `${ BASE }/wp-admin/admin.php?page=landtech-extras#landtech_extras_extensions`,
		caption: 'Editor extensions such as display conditions, sticky elements, parallax, and tooltips.',
		waitMs: 1200,
		before: async ( page ) => {
			await page.click( 'a[href="#landtech_extras_extensions"]' );
			await delay( 400 );
			await hideAdminChrome( page );
		},
	},
	{
		file: 'screenshot-3.png',
		url: `${ BASE }/wp-admin/admin.php?page=landtech-extras#landtech_extras_apis`,
		caption: 'Optional API keys for Google Maps, Snazzy Maps, Instagram, and BYOK LLM credentials.',
		waitMs: 1200,
		before: async ( page ) => {
			await page.click( 'a[href="#landtech_extras_apis"]' );
			await delay( 400 );
			await hideAdminChrome( page );
		},
	},
	{
		file: 'screenshot-4.png',
		url: `${ BASE }/wp-admin/post.php?post=5&action=elementor`,
		caption: 'LandTech Extras widgets appear in the Elementor panel under the LandTech Extras category.',
		waitMs: 12000,
		before: async ( page ) => {
			try {
				await page.waitForSelector( '#elementor-panel', { timeout: 25000 } );
				await page.evaluate( () => {
					document.querySelectorAll( '.dialog-widget, .e-checklist' ).forEach( ( el ) => el.remove() );
				} );
				await page.evaluate( () => {
					const addBtn = document.querySelector( '#elementor-panel-header-add-button' );
					if ( addBtn ) {
						addBtn.click();
					}
				} );
				await page.waitForSelector( '#elementor-panel-elements-search-input', { timeout: 15000 } );
				await page.focus( '#elementor-panel-elements-search-input' );
				await page.keyboard.type( 'Gallery', { delay: 15 } );
				await delay( 1200 );
			} catch ( err ) {
				console.warn( 'Elementor panel interaction skipped:', err.message );
			}
		},
	},
];

async function ensureDir( dir ) {
	fs.mkdirSync( dir, { recursive: true } );
}

function delay( ms ) {
	return new Promise( ( resolve ) => setTimeout( resolve, ms ) );
}

async function hideAdminChrome( page ) {
	await page.evaluate( () => {
		document.getElementById( 'wpadminbar' )?.remove();
		document.getElementById( 'adminmenuwrap' )?.remove();
		document.getElementById( 'adminmenuback' )?.remove();
		document.getElementById( 'wpfooter' )?.remove();
		const content = document.getElementById( 'wpcontent' );
		if ( content ) {
			content.style.marginLeft = '24px';
		}
	} );
}

async function playgroundLogin( page ) {
	await page.goto( `${ BASE }/wp-admin/?playground-auto-login=true`, {
		waitUntil: 'networkidle2',
		timeout: 120000,
	} );

	if ( page.url().includes( 'wp-login.php' ) ) {
		await page.goto( `${ BASE }/wp-login.php`, { waitUntil: 'networkidle2', timeout: 120000 } );
		await page.type( '#user_login', 'admin', { delay: 10 } );
		await page.type( '#user_pass', 'password', { delay: 10 } );
		await Promise.all( [
			page.waitForNavigation( { waitUntil: 'networkidle2', timeout: 120000 } ),
			page.click( '#wp-submit' ),
		] );
	}

	if ( page.url().includes( 'wp-login.php' ) ) {
		throw new Error( 'Could not log in to Playground (admin/password). Is the local site running?' );
	}
}

async function main() {
	ensureDir( ORG_ASSETS );
	if ( fs.existsSync( path.dirname( SVN_ASSETS ) ) ) {
		ensureDir( SVN_ASSETS );
	}

	const browser = await puppeteer.launch( {
		headless: 'new',
		defaultViewport: { width: 1280, height: 720, deviceScaleFactor: 1 },
		args: [ '--no-sandbox', '--disable-setuid-sandbox' ],
	} );

	const page = await browser.newPage();
	await playgroundLogin( page );

	for ( const shot of shots ) {
		console.log( `Capturing ${ shot.file } …` );
		await page.goto( shot.url, { waitUntil: 'networkidle2', timeout: 120000 } );
		await delay( shot.waitMs );
		if ( shot.before ) {
			await shot.before( page );
			await delay( 400 );
		}

		const orgPath = path.join( ORG_ASSETS, shot.file );
		await page.screenshot( { path: orgPath, type: 'png' } );
		console.log( `  → ${ orgPath }` );

		if ( fs.existsSync( path.dirname( SVN_ASSETS ) ) ) {
			const svnPath = path.join( SVN_ASSETS, shot.file );
			fs.copyFileSync( orgPath, svnPath );
			console.log( `  → ${ svnPath }` );
		}
	}

	await browser.close();

	const readmePath = path.join( ROOT, 'readme.txt' );
	let readme = fs.readFileSync( readmePath, 'utf8' );
	const block = [
		'== Screenshots ==',
		'',
		...shots.map( ( shot, i ) => `${ i + 1 }. ${ shot.caption }` ),
		'',
	].join( '\n' );

	if ( readme.includes( '== Screenshots ==' ) ) {
		readme = readme.replace( /== Screenshots ==[\s\S]*?(?=\n== |\n===$|$)/, block.trimEnd() );
	} else {
		readme = readme.replace(
			/(== Description ==[\s\S]*?\n)(\n== Installation ==)/,
			`$1\n${ block }$2`
		);
	}

	fs.writeFileSync( readmePath, readme );
	console.log( 'Updated readme.txt Screenshots section.' );
	console.log( 'Done.' );
}

main().catch( ( err ) => {
	console.error( err );
	process.exit( 1 );
} );
