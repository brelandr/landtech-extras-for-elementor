/**
 * Build Infinite Scroll core bundle (vanilla DOM API; no jQuery bridge).
 */
import { readFileSync, writeFileSync } from 'fs';
import { dirname, join } from 'path';
import { fileURLToPath } from 'url';
import * as esbuild from 'esbuild';

const root = join( dirname( fileURLToPath( import.meta.url ) ), '..' );
const nm   = join( root, 'node_modules' );

const header = `/*!
 * Infinite Scroll v4.0.1 (core build; vanilla DOM API)
 * Automatically add next page
 *
 * Licensed GPLv3 for open source use
 * or Infinite Scroll Commercial License for commercial use
 *
 * https://infinite-scroll.com
 * Copyright 2018-2020 Metafizzy
 */

`;

const parts = [
	join( nm, 'ev-emitter/ev-emitter.js' ),
	join( nm, 'fizzy-ui-utils/utils.js' ),
	join( nm, 'infinite-scroll/js/core.js' ),
	join( nm, 'infinite-scroll/js/page-load.js' ),
	join( nm, 'infinite-scroll/js/scroll-watch.js' ),
	join( nm, 'infinite-scroll/js/history.js' ),
	join( nm, 'infinite-scroll/js/button.js' ),
	join( nm, 'infinite-scroll/js/status.js' ),
];

let body = parts.map( ( path ) => readFileSync( path, 'utf8' ) ).join( '\n' );

// Omit optional jQuery plugin bridge and data helpers (Posts Classic uses new InfiniteScroll()).
body = body.replace( /let jQuery = window\.jQuery;\n/, '' );
body = body.replace(
	/\s*\/\/ add jQuery\s*if \( jQuery \) \{\s*this\.\$element = jQuery\( this\.element \);\s*\}\n/,
	'\n'
);
body = body.replace(
	/proto\.dispatchEvent = function\( type, event, args \) \{[\s\S]*?\n\};\n\nlet loggers/,
	`proto.dispatchEvent = function( type, event, args ) {
  this.log( type, args );
  let emitArgs = event ? [ event ].concat( args ) : args;
  this.emitEvent( type, emitArgs );
};

let loggers`
);
body = body.replace(
	/\s*\/\/ remove jQuery data\. #807\s*if \( jQuery && this\.\$element \) \{\s*jQuery\.removeData\( this\.element, 'infiniteScroll' \);\s*\}/,
	''
);
body = body.replace(
	/\n\/\/ set internal jQuery[^\n]*\nInfiniteScroll\.setJQuery = function\( jqry \) \{\s*jQuery = jqry;\s*\};\n/,
	'\n'
);
body = body.replace( /\nutils\.htmlInit\( InfiniteScroll, 'infinite-scroll' \);\n/, '\n' );
body = body.replace(
	/\n\/\/ add noop _init method for jQuery Bridget\. #768\s*proto\._init = function\(\) \{\};\n/,
	'\n'
);
body = body.replace(
	/let \{ jQueryBridget \} = window;\s*if \( jQuery && jQueryBridget \) \{\s*jQueryBridget\( 'infiniteScroll', InfiniteScroll, jQuery \);\s*\}/,
	''
);
body = body.replace( /\s*let jQuery = global\.jQuery;\n\n/, '\n' );
body = body.replace(
	/\s*\/\/ make available via \$\(\)\.data\('namespace'\)\s*if \( jQuery \) \{\s*jQuery\.data\( elem, namespace, instance \);\s*\}/,
	''
);

const outJs  = join( root, 'assets/lib/infinite-scroll/infinite-scroll.js' );
const outMin = join( root, 'assets/lib/infinite-scroll/infinite-scroll.min.js' );

writeFileSync( outJs, header + body, 'utf8' );

const min = await esbuild.transform( header + body, {
	loader: 'js',
	minify: true,
	target: 'es2015',
} );

writeFileSync( outMin, min.code, 'utf8' );
console.log( 'Built infinite-scroll.js and infinite-scroll.min.js (core, vanilla DOM API)' );
