/**
 * Rename wp_register_script / dependency handles to landtech-extras-* prefix.
 * PHP only — avoids touching widget slugs, CSS classes, and layout values.
 */
import { readFileSync, writeFileSync, readdirSync, statSync } from 'fs';
import { join, dirname } from 'path';
import { fileURLToPath } from 'url';

const root = join( dirname( fileURLToPath( import.meta.url ) ), '..' );

const HANDLE_MAP = {
	'audio-player': 'landtech-extras-audio-player',
	'hc-sticky': 'landtech-extras-hc-sticky',
	'parallax-gallery': 'landtech-extras-parallax-gallery',
	'parallax-element': 'landtech-extras-parallax-element',
	'parallax-background': 'landtech-extras-parallax-background',
	'image-comparison': 'landtech-extras-image-comparison',
	'hotips': 'landtech-extras-hotips',
	'unfold': 'landtech-extras-unfold',
	'clndr': 'landtech-extras-clndr',
	'circle-progress': 'landtech-extras-circle-progress',
	'ee-scroll-indicator': 'landtech-extras-scroll-indicator',
	'gmap3': 'landtech-extras-gmap3',
	'ee-timeline': 'landtech-extras-timeline',
	'ee-switcher': 'landtech-extras-switcher',
	'toggle-element': 'landtech-extras-toggle-element',
	'slidebars': 'landtech-extras-slidebars',
	'slide-menu': 'landtech-extras-slide-menu',
	'jquery-appear': 'landtech-extras-jquery-appear',
	'jquery-visible': 'landtech-extras-jquery-visible',
	'jquery-easing': 'landtech-extras-jquery-easing',
	'jquery-mobile': 'landtech-extras-jquery-mobile',
	'jquery-long-shadow': 'landtech-extras-jquery-long-shadow',
	'video-player': 'landtech-extras-video-player',
	'iphone-inline-video': 'landtech-extras-iphone-inline-video',
	'tablesorter': 'landtech-extras-tablesorter',
	'isotope': 'landtech-extras-isotope',
	'packery': 'landtech-extras-packery',
	'isotope-packery-mode': 'landtech-extras-isotope-packery-mode',
	'filtery': 'landtech-extras-filtery',
	'jquery-resize-ee': 'landtech-extras-jquery-resize',
	'ee-gallery-masonry-editor': 'landtech-extras-gallery-masonry-editor',
	'ee-posts-loop-isotope-editor': 'landtech-extras-posts-loop-isotope-editor',
};

const SKIP_LINE = [
	/return\s+'[^']+'\s*;/,
	/'widgetType'\s*=>/,
	/get_control_id\(\s*'layout'\s*\)\s*=>\s*'packery'/,
	/===\s*'packery'/,
	/const\s+PARALLAX_/,
	/'prefix_class'/,
	/'class'\s*=>/,
	/add_render_attribute/,
	/addRenderAttribute/,
];

function walkPhp( dir, out = [] ) {
	for ( const name of readdirSync( dir ) ) {
		const path = join( dir, name );
		const st   = statSync( path );
		if ( st.isDirectory() && 'vendor' !== name && 'node_modules' !== name ) {
			walkPhp( path, out );
		} else if ( name.endsWith( '.php' ) ) {
			out.push( path );
		}
	}
	return out;
}

function shouldSkipLine( line, file ) {
	if ( file.endsWith( 'modules/unfold/module.php' ) || file.endsWith( 'modules/circle-progress/module.php' ) ) {
		return true;
	}
	if ( file.endsWith( 'includes/managers/modules.php' ) ) {
		return true;
	}
	if ( file.endsWith( 'includes/compatibility/wpml/compatibility.php' ) ) {
		return true;
	}
	if ( file.endsWith( 'includes/managers/extensions.php' ) ) {
		return true;
	}
	if ( file.endsWith( 'modules/image/widgets/image-comparison.php' ) && /return\s+'image-comparison'/.test( line ) ) {
		return true;
	}
	if ( file.endsWith( 'modules/unfold/widgets/unfold.php' ) && /return\s+'unfold'/.test( line ) ) {
		return true;
	}
	if ( file.endsWith( 'modules/circle-progress/widgets/circle-progress.php' ) && /return\s+'circle-progress'/.test( line ) ) {
		return true;
	}
	if ( file.endsWith( 'modules/scroll-indicator/widgets/scroll-indicator.php' ) && /return\s+'ee-scroll-indicator'/.test( line ) ) {
		return true;
	}
	if ( file.endsWith( 'modules/switcher/widgets/switcher.php' ) && /return\s+'ee-switcher'/.test( line ) ) {
		return true;
	}
	if ( file.includes( 'skin-classic.php' ) || file.includes( 'phase4-layout-policy.php' ) || file.includes( 'editor-posts-asset-policy.php' ) ) {
		return true;
	}
	for ( const re of SKIP_LINE ) {
		if ( re.test( line ) ) {
			return true;
		}
	}
	return false;
}

function renameHandlesInFile( file ) {
	let src  = readFileSync( file, 'utf8' );
	const lines = src.split( '\n' );
	let changed = false;

	const next = lines.map( ( line ) => {
		if ( shouldSkipLine( line, file ) ) {
			return line;
		}
		let out = line;
		for ( const [ oldHandle, newHandle ] of Object.entries( HANDLE_MAP ) ) {
			const re = new RegExp( `'${oldHandle.replace( /[.*+?^${}()|[\]\\]/g, '\\$&' )}'`, 'g' );
			if ( re.test( out ) ) {
				out = out.replace( re, `'${newHandle}'` );
				changed = true;
			}
		}
		return out;
	} );

	if ( changed ) {
		writeFileSync( file, next.join( '\n' ), 'utf8' );
		console.log( 'Updated handles:', file.replace( root + '/', '' ) );
	}
}

for ( const file of walkPhp( root ) ) {
	if ( file.includes( '/vendor/' ) || file.includes( '/node_modules/' ) ) {
		continue;
	}
	renameHandlesInFile( file );
}
