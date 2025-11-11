( function( window, document ) {
	'use strict';

	function initHeroBlock() {
		var heroBlocks = document.querySelectorAll( '.hero-block' );

		heroBlocks.forEach( function( block ) {
			var numberElements = block.querySelectorAll( '.hero-block__result-number-value' );
			var titleElement = block.querySelector( '.hero-block__title' );
			var descriptionElement = block.querySelector( '.hero-block__description' );
			var resultsElement = block.querySelector( '.hero-block__results' );

			// Animate text elements when block enters viewport
			var textObserver = new IntersectionObserver( function( entries ) {
				entries.forEach( function( entry ) {
					if ( entry.isIntersecting ) {
						// Animate title
						if ( titleElement ) {
							setTimeout( function() {
								titleElement.classList.add( 'hero-block__title--animated' );
							}, 100 );
						}

						// Animate description
						if ( descriptionElement ) {
							setTimeout( function() {
								descriptionElement.classList.add( 'hero-block__description--animated' );
							}, 200 );
						}

						// Animate results
						if ( resultsElement ) {
							setTimeout( function() {
								resultsElement.classList.add( 'hero-block__results--animated' );
							}, 400 );
						}

						textObserver.unobserve( entry.target );
					}
				} );
			}, {
				threshold: 0.2 // Trigger when 20% visible
			} );

			// Observe the hero block for text animations
			textObserver.observe( block );

			// Animate number from 0 to target
			function animateNumber( element, target ) {
				var duration = 2000; // 2 seconds
				var start = 0;
				var startTime = null;
				var animated = element.dataset.animated === 'true';

				if ( animated ) {
					return;
				}

				function step( timestamp ) {
					if ( ! startTime ) {
						startTime = timestamp;
					}

					var progress = Math.min( ( timestamp - startTime ) / duration, 1 );
					// Easing function (ease-out)
					var easeOut = 1 - Math.pow( 1 - progress, 3 );
					var current = Math.floor( start + ( target - start ) * easeOut );

					element.textContent = current;

					if ( progress < 1 ) {
						requestAnimationFrame( step );
					} else {
						element.textContent = target;
						element.dataset.animated = 'true';
					}
				}

				requestAnimationFrame( step );
			}

			// Intersection Observer for triggering number animation when in viewport
			var numberObserver = new IntersectionObserver( function( entries ) {
				entries.forEach( function( entry ) {
					if ( entry.isIntersecting ) {
						var element = entry.target;
						var target = parseInt( element.dataset.target, 10 ) || 0;

						if ( target > 0 ) {
							animateNumber( element, target );
							numberObserver.unobserve( element );
						}
					}
				} );
			}, {
				threshold: 0.5 // Trigger when 50% visible
			} );

			// Observe each number element
			numberElements.forEach( function( element ) {
				numberObserver.observe( element );
			} );
		} );
	}

	// Initialize on DOM ready
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initHeroBlock );
	} else {
		initHeroBlock();
	}

} )( window, document );



