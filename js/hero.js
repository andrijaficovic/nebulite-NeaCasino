( function( window, document ) {
	'use strict';

	function initHeroBlock() {
		var heroBlocks = document.querySelectorAll( '.hero-block' );

		heroBlocks.forEach( function( block ) {
			var numberElements = block.querySelectorAll( '.hero-block__result-number-value' );
			
			// Note: CSS animations now handle fade-in effects
			// Content is immediately visible for LCP optimization
			// JavaScript only handles number counting animation

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

			// Handle CTA button smooth scroll to casino list
			var ctaButton = block.querySelector( '.hero-block__cta' );
			if ( ctaButton ) {
				ctaButton.addEventListener( 'click', function( e ) {
					var href = this.getAttribute( 'href' );
					
					if ( ! href || href === '#' ) {
						return;
					}
					
					var targetElement = null;
					var targetId = null;
					
					// Check if it's a hash link
					if ( href.indexOf( '#' ) !== -1 ) {
						var hashIndex = href.indexOf( '#' );
						targetId = href.substring( hashIndex + 1 );
						
						if ( targetId ) {
							targetElement = document.getElementById( targetId );
						}
					}
					
					// If no target found, try to find casino-list section
					if ( ! targetElement ) {
						targetElement = document.querySelector( '.casino-list' );
					}
					
					// If still no target, check if URL contains casino-list reference
					if ( ! targetElement && href.indexOf( 'casino-list' ) !== -1 ) {
						targetElement = document.querySelector( '.casino-list' );
					}
					
					if ( targetElement ) {
						e.preventDefault();
						
						// Calculate offset for header (if fixed)
						var headerElement = document.getElementById( 'masthead' );
						var headerOffset = 0;
						
						if ( headerElement ) {
							var headerStyles = window.getComputedStyle( headerElement );
							var headerPosition = headerStyles.getPropertyValue( 'position' );
							
							if ( headerPosition === 'fixed' || headerPosition === 'sticky' ) {
								headerOffset = headerElement.offsetHeight;
							}
						}
						
						var targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerOffset;
						
						window.scrollTo( {
							top: targetPosition,
							behavior: 'smooth'
						} );
					}
				} );
			}
		} );
	}

	// Initialize on DOM ready
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initHeroBlock );
	} else {
		initHeroBlock();
	}

} )( window, document );



