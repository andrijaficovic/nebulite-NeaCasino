( function( $, window, document ) {
	'use strict';

	function initCasinoList( $root ) {
		var perPage = parseInt( $root.data( 'per-page' ), 10 ) || 10;
		var $table = $root.find( '.casino-table' );
		var $tableBody = $root.find( '.casino-table__body' ); // Add reference to body wrapper
		var $loadMore = $root.find( '.casino-list__load-more' );
		var $sort = $root.find( '.casino-list__sort' );
		var $filtersBtn = $root.find( '.casino-list__filters' );
		var $modal = $root.find( '.casino-filters-modal' );
		var $modalOverlay = $modal.find( '.casino-filters-modal__overlay' );
		var $modalClose = $modal.find( '.casino-filters-modal__close' );
		var $modalClear = $modal.find( '.casino-filters-modal__clear' );
		var $modalApply = $modal.find( '.casino-filters-modal__apply' );
		var filters = {};
		var updateCountTimeout = null;

		// Open modal
		$filtersBtn.on( 'click', function() {
			$modal.removeAttr( 'hidden' ).focus();
			$filtersBtn.attr( 'aria-expanded', 'true' );
			$('body').addClass('modal-open');
			
			// Reset button text to default when opening
			$modalApply.text( 'Apply Filters' );
			
			// Update count after a short delay to allow modal to render
			setTimeout( function() {
				updateResultsCount();
			}, 100 );
		} );

		// Close modal
		function closeModal() {
			$modal.attr( 'hidden', 'hidden' );
			$filtersBtn.attr( 'aria-expanded', 'false' ).focus();
			$('body').removeClass('modal-open');
			
			// Reset button text
			$modalApply.text( 'Apply Filters' );
			
			// Clear any pending count update
			if ( updateCountTimeout ) {
				clearTimeout( updateCountTimeout );
				updateCountTimeout = null;
			}
		}

		$modalClose.on( 'click', closeModal );
		$modalOverlay.on( 'click', closeModal );

		// Close on Escape key
		$( document ).on( 'keydown', function( e ) {
			if ( e.key === 'Escape' && ! $modal.attr( 'hidden' ) ) {
				closeModal();
			}
		} );

		// Update results count on filter change
		function updateResultsCount() {
			// Clear previous timeout
			if ( updateCountTimeout ) {
				clearTimeout( updateCountTimeout );
			}

			// Debounce: wait 300ms before making request
			updateCountTimeout = setTimeout( function() {
				var currentFilters = getFilters();
				
				// Check if any filters are selected
				var hasFilters = Object.keys( currentFilters ).length > 0;
				
				if ( !hasFilters ) {
					// No filters selected, show default text
					$modalApply.prop( 'disabled', false ).text( 'Apply Filters' );
					return;
				}
				
				// Show loading state
				var originalText = 'Apply Filters';
				$modalApply.prop( 'disabled', true ).text( 'Loading...' );

				// Request count only (per_page: 1 for faster response)
				$.ajax( {
					type: 'POST',
					url: ( window.NebuliteCasinoList && window.NebuliteCasinoList.ajaxUrl ) || '',
					data: {
						action: 'nebulite_casino_list',
						nonce: window.NebuliteCasinoList ? window.NebuliteCasinoList.nonce : '',
						per_page: 1,
						page: 1,
						sort: $sort.val(),
						filters: currentFilters
					},
					dataType: 'json'
				} ).done( function( res ) {
					$modalApply.prop( 'disabled', false );
					
					if ( res && res.success && res.data && res.data.found !== undefined ) {
						var count = parseInt( res.data.found, 10 ) || 0;
						if ( count > 0 ) {
							$modalApply.text( 'Apply Filters (' + count + ')' );
						} else {
							$modalApply.text( originalText );
						}
					} else {
						$modalApply.text( originalText );
					}
				} ).fail( function() {
					$modalApply.prop( 'disabled', false ).text( originalText );
				} );
			}, 300 );
		}

		// Listen to checkbox changes
		$modal.on( 'change', '.casino-filters-modal__checkbox', updateResultsCount );

		// Clear all filters
		$modalClear.on( 'click', function() {
			$modal.find( '.casino-filters-modal__checkbox' ).prop( 'checked', false );
			filters = {};
			updateResultsCount();
			applyFilters();
		} );

		// Apply filters
		$modalApply.on( 'click', function() {
			filters = getFilters();
			applyFilters();
			closeModal();
		} );

		// Get current filter values
		function getFilters() {
			var filterObj = {};
			
			// General filters
			if ( $modal.find( 'input[name="low_wagering"]' ).is( ':checked' ) ) {
				filterObj.low_wagering = 1;
			}
			if ( $modal.find( 'input[name="low_deposit"]' ).is( ':checked' ) ) {
				filterObj.low_deposit = 1;
			}
			if ( $modal.find( 'input[name="no_kyc"]' ).is( ':checked' ) ) {
				filterObj.no_kyc = 1;
			}
			if ( $modal.find( 'input[name="chatroom"]' ).is( ':checked' ) ) {
				filterObj.chatroom = 1;
			}
			if ( $modal.find( 'input[name="discord"]' ).is( ':checked' ) ) {
				filterObj.discord = 1;
			}

			// Games
			var games = [];
			$modal.find( 'input[name="games[]"]:checked' ).each( function() {
				games.push( $( this ).val() );
			} );
			if ( games.length > 0 ) {
				filterObj.games = games;
			}

			// Cryptocurrencies
			var cryptocurrencies = [];
			$modal.find( 'input[name="cryptocurrencies[]"]:checked' ).each( function() {
				cryptocurrencies.push( $( this ).val() );
			} );
			if ( cryptocurrencies.length > 0 ) {
				filterObj.cryptocurrencies = cryptocurrencies;
			}

			return filterObj;
		}

		// Apply filters and reload list
		function applyFilters() {
			$table.css( 'opacity', '0.5' );
			
			request( 1, $sort.val(), filters, function( res ) {
				$table.css( 'opacity', '1' );
				
				if ( res && res.success && res.data ) {
					// Update only the body content, not the header
					$tableBody.html( res.data.html || '' );
					var remaining = Math.max( 0, ( res.data.found || 0 ) - perPage );
					
					if ( remaining > 0 ) {
						if ( $loadMore.length === 0 ) {
							$loadMore = $( '<button class="casino-list__load-more" type="button" />' ).appendTo( $root.find( '.container' ) );
							$loadMore.on( 'click', loadMoreHandler );
						}
						$loadMore.data( 'next-page', 2 ).text( remaining + ' more to explore' );
					} else {
						$loadMore.remove();
					}
				}
			} );
		}

		// Expand/collapse details
		$root.on( 'click', '.casino-table__toggle', function() {
			var $btn = $( this );
			var $row = $btn.closest( '.casino-table__row' );
			var $details = $row.find( '.casino-table__details' );
			var $icon = $btn.find( '.casino-table__toggle-icon' );
			var $text = $btn.find( '.casino-table__toggle-text' );
			var expanded = $btn.attr( 'aria-expanded' ) === 'true';
			var isDesktop = window.innerWidth >= 1024;
			
			$btn.attr( 'aria-expanded', expanded ? 'false' : 'true' );
			$details.prop( 'hidden', expanded );
			
			// On mobile, update text; on desktop, rotate icon
			if ( isDesktop ) {
				// Rotate icon: 0deg when collapsed (pointing down), 180deg when expanded (pointing up)
				if ( expanded ) {
					$icon.css( 'transform', 'rotate(0deg)' );
				} else {
					$icon.css( 'transform', 'rotate(180deg)' );
				}
			} else {
				// Update text on mobile
				var buttonText = expanded ? 'More Details' : 'Less Details';
				$text.text( buttonText );
			}
		} );

		// Load more handler
		function loadMoreHandler() {
			var $btn = $( this );
			var nextPage = parseInt( $btn.data( 'next-page' ), 10 ) || 2;
			
			$btn.prop( 'disabled', true ).text( 'Loading...' );
			
			request( nextPage, $sort.val(), filters, function( res ) {
				$btn.prop( 'disabled', false );
				
				if ( res && res.success && res.data && res.data.html ) {
					// Append to body, not replace
					$tableBody.append( res.data.html );
					var totalShown = nextPage * perPage;
					var remaining = Math.max( 0, ( res.data.found || 0 ) - totalShown );
					
					if ( remaining > 0 ) {
						$btn.data( 'next-page', nextPage + 1 );
						$btn.text( remaining + ' more to explore' );
					} else {
						$btn.remove();
					}
				} else {
					$btn.text( 'Error loading casinos' );
				}
			} );
		}

		$loadMore.on( 'click', loadMoreHandler );

		// Sort change
		$sort.on( 'change', function() {
			var $select = $( this );
			$select.prop( 'disabled', true );
			$table.css( 'opacity', '0.5' );
			
			request( 1, $select.val(), filters, function( res ) {
				$select.prop( 'disabled', false );
				$table.css( 'opacity', '1' );
				
				if ( res && res.success && res.data ) {
					// Update only the body content, not the header
					$tableBody.html( res.data.html || '' );
					var remaining = Math.max( 0, ( res.data.found || 0 ) - perPage );
					
					// Always remove existing load more button first
					$loadMore.off( 'click', loadMoreHandler ).remove();
					
					if ( remaining > 0 ) {
						// Create new button and attach handler
						$loadMore = $( '<button class="casino-list__load-more" type="button" />' );
						$loadMore.data( 'next-page', 2 ).text( remaining + ' more to explore' );
						$loadMore.on( 'click', loadMoreHandler );
						$loadMore.appendTo( $root.find( '.container' ) );
					}
				}
			} );
		} );

		function request( page, sort, filters, callback ) {
			$.ajax( {
				type: 'POST',
				url: ( window.NebuliteCasinoList && window.NebuliteCasinoList.ajaxUrl ) || '',
				data: {
					action: 'nebulite_casino_list',
					nonce: window.NebuliteCasinoList ? window.NebuliteCasinoList.nonce : '',
					per_page: perPage,
					page: page,
					sort: sort,
					filters: filters
				},
				dataType: 'json'
			} ).always( function( res ) {
				if ( typeof callback === 'function' ) {
					callback( res );
				}
			} );
		}
	}

	$( function() {
		$( '.casino-list' ).each( function() {
			initCasinoList( $( this ) );
		} );
	} );

} )( jQuery, window, document );


