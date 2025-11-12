( function( $, window, document ) {
	'use strict';

	function initCasinoList( $root ) {
		var idsData = $root.attr( 'data-casino-ids' );
		if ( ! idsData ) {
			return;
		}

		var ids;
		try {
			ids = JSON.parse( idsData );
		} catch ( error ) {
			ids = [];
		}

		if ( ! Array.isArray( ids ) || ids.length === 0 ) {
			return;
		}

		var $tableBody = $root.find( '.casino-table__body' );
		var $loadMore = $root.find( '.casino-list__load-more' );

		if ( $loadMore.length === 0 ) {
			return;
		}

		var chunkSize = parseInt( $root.attr( 'data-chunk-size' ), 10 ) || 15;
		var offset = parseInt( $root.attr( 'data-offset' ), 10 ) || 0;

		if ( offset >= ids.length ) {
			$loadMore.remove();
			return;
		}

		function formatRemainingText( remaining ) {
			if ( typeof remaining !== 'number' || remaining <= 0 ) {
				return '';
			}

			return remaining + ' περισσότερα για εξερεύνηση';
		}

		$loadMore.text( formatRemainingText( ids.length - offset ) );

		function loadMoreHandler() {
			var $btn = $( this );
			var nextIds = ids.slice( offset, offset + chunkSize );

			if ( nextIds.length === 0 ) {
				$btn.remove();
				return;
			}

			$btn.prop( 'disabled', true ).text( 'Φόρτωση…' );

			$.ajax( {
				type: 'POST',
				url: ( window.NebuliteCasinoList && window.NebuliteCasinoList.ajaxUrl ) || '',
				data: {
					action: 'nebulite_casino_list',
					nonce: window.NebuliteCasinoList ? window.NebuliteCasinoList.nonce : '',
					ids: nextIds,
					offset: offset
				},
				dataType: 'json'
			} ).done( function( res ) {
				$btn.prop( 'disabled', false );

				if ( ! res || ! res.success || ! res.data || ! res.data.html ) {
					$btn.text( 'Error loading casinos' );
					return;
				}

				$tableBody.append( res.data.html );
				offset += nextIds.length;
				$root.attr( 'data-offset', offset );

				var remaining = ids.length - offset;
				if ( remaining > 0 ) {
					$btn.text( formatRemainingText( remaining ) );
				} else {
					$btn.remove();
				}
			} ).fail( function() {
				$btn.prop( 'disabled', false ).text( 'Error loading casinos' );
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
