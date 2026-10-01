/**
 * U32 — fail if first-party PHP/JS still needs script-src unsafe-inline.
 */
import { execSync } from 'child_process';
import { dirname, join } from 'path';
import { fileURLToPath } from 'url';

const root = join( dirname( fileURLToPath( import.meta.url ) ), '..' );

const checks = [
	{ label: 'onclick=', pattern: 'onclick=', dirs: [ 'modules', 'includes', 'admin', 'base' ] },
	{ label: 'onmouseover=', pattern: 'onmouseover=', dirs: [ 'modules', 'includes', 'admin', 'base' ] },
	{ label: 'javascript:', pattern: 'javascript:', dirs: [ 'modules', 'includes', 'admin', 'base' ] },
	{ label: 'eval(', pattern: 'eval(', dirs: [ 'assets/js' ] },
	{ label: 'new Function(', pattern: 'new Function(', dirs: [ 'assets/js' ] },
];

let failed = false;

for ( const check of checks ) {
	const cmd = `grep -R --line-number --include='*.php' --include='*.js' ${ JSON.stringify( check.pattern ) } ${ check.dirs.join( ' ' ) } || true`;
	const output = execSync( cmd, { encoding: 'utf8', cwd: root } );
	const lines = output
		.split( '\n' )
		.filter( Boolean )
		.filter( ( line ) => ! line.includes( 'landtech-extras-svg-sanitizer.php' ) );

	if ( lines.length ) {
		failed = true;
		console.error( `FAIL ${ check.label }` );
		lines.forEach( ( line ) => console.error( '  ' + line ) );
	} else {
		console.log( `PASS ${ check.label }` );
	}
}

if ( failed ) {
	process.exitCode = 1;
} else {
	console.log( 'U32 CSP audit: first-party PHP/JS has no onclick, javascript: hrefs, eval, or new Function' );
}
