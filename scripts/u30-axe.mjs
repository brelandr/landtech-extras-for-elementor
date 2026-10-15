/**
 * U30 — run axe-core against the WCAG fixture and any real render() snapshots.
 * Critical and serious violations must be zero.
 */
import { existsSync, readFileSync, readdirSync } from 'fs';
import { dirname, join, resolve } from 'path';
import { fileURLToPath } from 'url';
import { JSDOM } from 'jsdom';
import axe from 'axe-core';

const root = join( dirname( fileURLToPath( import.meta.url ) ), '..' );
const documents = [
	join( root, 'tests/fixtures/u30-wcag.html' ),
];
const renderDir = join( root, 'tests/fixtures/u30-render' );
if ( existsSync( renderDir ) ) {
	for ( const name of readdirSync( renderDir ) ) {
		if ( name.endsWith( '.html' ) ) {
			documents.push( join( renderDir, name ) );
		}
	}
}

function inlineLocalStyles( html, htmlPath ) {
	return html.replace( /<link\s+rel="stylesheet"\s+href="([^"]+)"\s*>/g, ( match, href ) => {
		if ( /^https?:/i.test( href ) ) {
			return match;
		}
		const cssPath = resolve( dirname( htmlPath ), href );
		if ( ! existsSync( cssPath ) ) {
			return match;
		}
		return '<style>' + readFileSync( cssPath, 'utf8' ) + '</style>';
	} );
}

let failed = false;

for ( const file of documents ) {
	const html = inlineLocalStyles( readFileSync( file, 'utf8' ), file );
	const dom = new JSDOM( html, { url: 'https://extrasforelementor.com/demos/accessibility/' } );
	const { window } = dom;

	global.window = window;
	global.document = window.document;
	global.Node = window.Node;
	global.Element = window.Element;
	global.HTMLElement = window.HTMLElement;
	global.DocumentFragment = window.DocumentFragment;
	global.NodeList = window.NodeList;

	if ( window.HTMLCanvasElement ) {
		window.HTMLCanvasElement.prototype.getContext = function () {
			return null;
		};
	}

	window.eval( axe.source );

	const results = await window.axe.run( window.document, {
		runOnly: {
			type: 'tag',
			values: [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa', 'best-practice' ],
		},
	} );

	const critical = results.violations.filter( ( v ) => 'critical' === v.impact );
	const serious = results.violations.filter( ( v ) => 'serious' === v.impact );
	const label = file.replace( root + '/', '' );

	console.log( `${ label }: critical ${ critical.length }, serious ${ serious.length }` );
	[ ...critical, ...serious ].forEach( ( v ) => {
		console.log( `  - ${ v.id } (${ v.impact }, ${ v.nodes.length } node${ 1 === v.nodes.length ? '' : 's' }): ${ v.help }` );
	} );

	if ( critical.length || serious.length ) {
		failed = true;
	}
}

if ( failed ) {
	process.exitCode = 1;
} else {
	console.log( 'U30 axe-core: 0 critical or serious violations' );
}
