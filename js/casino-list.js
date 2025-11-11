( function( $, window, document ) {
	'use strict';

	function initCasinoList( $root ) {
		var perPage = parseInt( $root.data( 'per-page' ), 10 ) || 10;
		var $tableBody = $root.find( '.casino-table__body' );
		var $loadMore = $root.find( '.casino-list__load-more' );

		if ( $loadMore.length === 0 ) {
			return;
		}

		function request( page, callback ) {
			$.ajax( {
				type: 'POST',
				url: ( window.NebuliteCasinoList && window.NebuliteCasinoList.ajaxUrl ) || '',
				data: {
					action: 'nebulite_casino_list',
					nonce: window.NebuliteCasinoList ? window.NebuliteCasinoList.nonce : '',
					per_page: perPage,
					page: page
				},
				dataType: 'json'
			} ).always( function( res ) {
				if ( typeof callback === 'function' ) {
					callback( res );
				}
			} );
		}

		function loadMoreHandler() {
			var $btn = $( this );
			var nextPage = parseInt( $btn.data( 'next-page' ), 10 ) || 2;

			$btn.prop( 'disabled', true ).text( 'Loading...' );

			request( nextPage, function( res ) {
				$btn.prop( 'disabled', false );

				if ( res && res.success && res.data && res.data.html ) {
					$tableBody.append( res.data.html );

					var totalShown = nextPage * perPage;
					var remaining = Math.max( 0, ( res.data.found || 0 ) - totalShown );

					if ( remaining > 0 ) {
						$btn.data( 'next-page', nextPage + 1 ).text( remaining + ' more to explore' );
					} else {
						$btn.remove();
					}
				} else {
					$btn.text( 'Error loading casinos' );
				}
			} );
		}

		$loadMore.on( 'click', loadMoreHandler );
	}

	$( function() {
		$( '.casino-list' ).each( function() {
			initCasinoList( $( this ) );
		} );
	} );

} )( jQuery, window, document );
