/**
 * U30 — run axe-core against the WCAG 2.2 fixture.
 * Critical violations must be zero.
 */
import { readFileSync } from 'fs';
import { dirname, join } from 'path';
import { fileURLToPath } from 'url';
import { JSDOM } from 'jsdom';
import axe from 'axe-core';

const root = join( dirname( fileURLToPath( import.meta.url ) ), '..' );
const html = readFileSync( join( root, 'tests/fixtures/u30-wcag.html' ), 'utf8' );
const dom = new JSDOM( html, { url: 'https://extrasforelementor.com/demos/accessibility/' } );
const { window } = dom;

global.window = window;
global.document = window.document;
global.Node = window.Node;
global.Element = window.Element;
global.HTMLElement = window.HTMLElement;
global.DocumentFragment = window.DocumentFragment;
global.NodeList = window.NodeList;

window.HTMLCanvasElement.prototype.getContext = function () {
	return null;
};

window.eval( axe.source );

const results = await window.axe.run( window.document, {
	runOnly: {
		type: 'tag',
		values: [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa', 'best-practice' ],
	},
} );

const critical = results.violations.filter( ( v ) => 'critical' === v.impact );
const serious = results.violations.filter( ( v ) => 'serious' === v.impact );

function printGroup( label, items ) {
	if ( ! items.length ) {
		console.log( `${ label }: 0` );
		return;
	}
	console.log( `${ label }: ${ items.length }` );
	items.forEach( ( v ) => {
		console.log( `  - ${ v.id } (${ v.nodes.length } node${ 1 === v.nodes.length ? '' : 's' }): ${ v.help }` );
	} );
}

printGroup( 'Critical', critical );
printGroup( 'Serious', serious );

if ( critical.length ) {
	process.exitCode = 1;
} else {
	console.log( 'U30 axe-core: 0 critical violations' );
}
