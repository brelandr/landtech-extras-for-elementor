/**
 * Minify org CSS/JS into matching *.min.* counterparts (esbuild).
 */
import * as esbuild from 'esbuild';
import { readFileSync, writeFileSync } from 'fs';
import { dirname, join } from 'path';
import { fileURLToPath } from 'url';

const root = join( dirname( fileURLToPath( import.meta.url ) ), '..' );

const pairs = [
	[ 'assets/css/frontend.css', 'assets/css/frontend.min.css' ],
	[ 'assets/css/frontend-rtl.css', 'assets/css/frontend-rtl.min.css' ],
	[ 'assets/js/frontend.js', 'assets/js/frontend.min.js' ],
	[ 'assets/js/notice.js', 'assets/js/notice.min.js' ],
];

async function minifyFile( relIn, relOut ) {
	const inPath = join( root, relIn );
	const outPath = join( root, relOut );
	const isCss = relIn.endsWith( '.css' );
	const result = await esbuild.transform( readFileSync( inPath, 'utf8' ), {
		loader: isCss ? 'css' : 'js',
		minify: true,
		target: isCss ? undefined : 'es2015',
	} );
	writeFileSync( outPath, result.code, 'utf8' );
}

for ( const [ a, b ] of pairs ) {
	await minifyFile( a, b );
}
