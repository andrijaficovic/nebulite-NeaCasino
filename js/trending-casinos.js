( function( window, document ) {
	'use strict';

	function initTrendingCasinosCopy() {
		var buttons = document.querySelectorAll( '.trending-casinos__code' );

		if ( ! buttons.length ) {
			return;
		}

		buttons.forEach( function( button ) {
			var valueEl   = button.querySelector( '.trending-casinos__code-value' );
			var feedbackEl = button.querySelector( '.trending-casinos__code-feedback' );
			var copyText  = button.getAttribute( 'data-copy-text' ) || ( valueEl ? valueEl.textContent.trim() : '' );

			if ( valueEl && ! button.hasAttribute( 'aria-label' ) ) {
				var labelEl = button.querySelector( '.trending-casinos__code-label' );
				var label   = labelEl ? labelEl.textContent.trim() + ' ' : '';
				button.setAttribute( 'aria-label', ( label + valueEl.textContent.trim() ).trim() );
			}

			button.addEventListener( 'click', function() {
				if ( ! copyText ) {
					return;
				}

				copyToClipboard( copyText )
					.then( function() {
						handleCopiedState( button, feedbackEl );
					} )
					.catch( function() {
						if ( fallbackCopy( copyText ) ) {
							handleCopiedState( button, feedbackEl );
						}
					} );
			} );
		} );
	}

	function copyToClipboard( text ) {
		if ( navigator.clipboard && navigator.clipboard.writeText ) {
			return navigator.clipboard.writeText( text );
		}

		return Promise.reject();
	}

	function fallbackCopy( text ) {
		var textarea = document.createElement( 'textarea' );
		textarea.value = text;
		textarea.setAttribute( 'readonly', '' );
		textarea.style.position = 'absolute';
		textarea.style.left = '-9999px';
		document.body.appendChild( textarea );

		textarea.select();
		textarea.setSelectionRange( 0, textarea.value.length );

		var successful = false;
		try {
			successful = document.execCommand( 'copy' );
		} catch ( err ) {
			successful = false;
		}

		document.body.removeChild( textarea );
		return successful;
	}

	function handleCopiedState( button, feedbackEl ) {
		button.classList.add( 'is-copied' );

		if ( feedbackEl ) {
			feedbackEl.textContent = button.getAttribute( 'data-copied-label' ) || '';
		}

		window.clearTimeout( button._copyTimeout );
		button._copyTimeout = window.setTimeout( function() {
			button.classList.remove( 'is-copied' );
			if ( feedbackEl ) {
				feedbackEl.textContent = '';
			}
		}, 1600 );
	}

	document.addEventListener( 'DOMContentLoaded', initTrendingCasinosCopy );

} )( window, document );
