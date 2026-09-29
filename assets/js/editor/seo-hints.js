( function () {
	'use strict';

	if ( ! window.elementor ) {
		return;
	}

	var ltxeSeoTimer;

	function ensurePanel() {
		var existing = document.getElementById( 'ltxe-seo-hints' );
		if ( existing ) {
			return existing;
		}
		var panel = document.createElement( 'div' );
		panel.id = 'ltxe-seo-hints';
		panel.className = 'ltxe-seo-hints';
		panel.innerHTML = '<button type="button" class="ltxe-seo-hints__toggle">SEO Hints</button><ul class="ltxe-seo-hints__list"></ul>';
		document.body.appendChild( panel );
		panel.querySelector( '.ltxe-seo-hints__toggle' ).addEventListener( 'click', function () {
			panel.classList.toggle( 'is-open' );
		} );
		return panel;
	}

	function ltxeRenderSeoPanel( results ) {
		var panel = ensurePanel();
		var list = panel.querySelector( '.ltxe-seo-hints__list' );
		list.innerHTML = '';
		results.forEach( function ( item ) {
			var li = document.createElement( 'li' );
			li.className = 'ltxe-seo-hints__item ltxe-seo-hints__item--' + item.level;
			li.textContent = item.msg;
			list.appendChild( li );
		} );
	}

	function ltxeRunSeoChecks() {
		var results = [];
		if ( ! elementor.$preview || ! elementor.$preview[ 0 ] || ! elementor.$preview[ 0 ].contentDocument ) {
			return;
		}
		var doc = elementor.$preview[ 0 ].contentDocument;
		var h1s = doc.querySelectorAll( 'h1' );
		if ( h1s.length === 0 ) {
			results.push( { level: 'error', msg: 'No H1 heading found.' } );
		} else if ( h1s.length > 1 ) {
			results.push( { level: 'warning', msg: h1s.length + ' H1 headings found — a page should have only one.' } );
		} else {
			results.push( { level: 'pass', msg: 'H1 heading present.' } );
		}

		var words = ( doc.body && doc.body.innerText ) ? doc.body.innerText.trim().split( /\s+/ ).length : 0;
		if ( words >= 300 && ! doc.querySelector( 'h2' ) ) {
			results.push( { level: 'warning', msg: 'No H2 heading on a long page.' } );
		}

		var imgs = doc.querySelectorAll( 'img' );
		var noAlt = [].slice.call( imgs ).filter( function ( img ) {
			return ! img.getAttribute( 'alt' );
		} );
		if ( noAlt.length ) {
			results.push( { level: 'warning', msg: noAlt.length + ' image(s) missing alt text.' } );
		} else if ( imgs.length ) {
			results.push( { level: 'pass', msg: 'All images have alt text.' } );
		}

		var generic = [].slice.call( doc.querySelectorAll( 'a,button' ) ).filter( function ( b ) {
			return /^(click here|read more|here|more|learn more)$/i.test( ( b.textContent || '' ).trim() );
		} );
		if ( generic.length ) {
			results.push( { level: 'warning', msg: generic.length + ' button(s) with generic text — use descriptive labels.' } );
		}

		var title = doc.title || '';
		if ( title.length > 60 ) {
			results.push( { level: 'warning', msg: 'Page title is longer than 60 characters.' } );
		}

		ltxeRenderSeoPanel( results );
	}

	elementor.hooks.addAction( 'editor/after_section_end', function () {
		window.clearTimeout( ltxeSeoTimer );
		ltxeSeoTimer = window.setTimeout( ltxeRunSeoChecks, 3000 );
	} );

	elementor.on( 'preview:loaded', function () {
		window.setTimeout( ltxeRunSeoChecks, 1500 );
	} );
}() );
