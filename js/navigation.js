/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens, smooth scroll,
 * and enables TAB key navigation support for dropdown menus.
 */
( function() {
	const siteNavigation = document.getElementById( 'site-navigation' );

	// Return early if the navigation doesn't exist.
	if ( ! siteNavigation ) {
		return;
	}

	const button = siteNavigation.getElementsByTagName( 'button' )[ 0 ];
	const menu = siteNavigation.getElementsByTagName( 'ul' )[ 0 ];

	// Hide menu toggle button if menu is empty and return early.
	if ( 'undefined' === typeof menu ) {
		if ( button ) {
			button.style.display = 'none';
		}
		return;
	}

	if ( ! menu.classList.contains( 'nav-menu' ) ) {
		menu.classList.add( 'nav-menu' );
	}

	// Toggle the .toggled class and the aria-expanded value each time the button is clicked.
	if ( button ) {
		button.addEventListener( 'click', function() {
			siteNavigation.classList.toggle( 'toggled' );
			document.body.classList.toggle( 'menu-open' );

			if ( button.getAttribute( 'aria-expanded' ) === 'true' ) {
				button.setAttribute( 'aria-expanded', 'false' );
			} else {
				button.setAttribute( 'aria-expanded', 'true' );
			}
		} );
	}

	// Handle sub-menu toggle using event delegation (works for dynamic content)
	// This ensures it works even if menu is loaded dynamically
	menu.addEventListener( 'click', function( e ) {
		// Find the clicked link (could be the link itself or a child element)
		let clickedLink = e.target;
		
		// If target is not a link, try to find the closest link
		if ( clickedLink.tagName !== 'A' ) {
			clickedLink = clickedLink.closest( 'a' );
		}
		
		// Check if this link is inside a menu item with children
		if ( clickedLink ) {
			const menuItem = clickedLink.closest( '.menu-item-has-children, .page_item_has_children' );
			
			if ( menuItem ) {
				const isMobile = window.innerWidth <= 1024;
				
				if ( isMobile ) {
					// Prevent default behavior and stop propagation
					e.preventDefault();
					e.stopImmediatePropagation();
					
					// Toggle submenu
					const isOpen = menuItem.classList.contains( 'submenu-open' );
					const allMenuItemsWithChildren = menu.querySelectorAll( '.menu-item-has-children, .page_item_has_children' );
					
					// Close all other submenus first
					allMenuItemsWithChildren.forEach( function( otherItem ) {
						if ( otherItem !== menuItem ) {
							otherItem.classList.remove( 'submenu-open' );
						}
					} );
					
					// Toggle current submenu
					if ( isOpen ) {
						menuItem.classList.remove( 'submenu-open' );
					} else {
						menuItem.classList.add( 'submenu-open' );
					}
					
					// Force a reflow to ensure CSS transition works
					void menuItem.offsetHeight;
					
					return false;
				} else {
					// On desktop, prevent navigation for placeholder links
					const href = clickedLink.getAttribute( 'href' );
					const isPlaceholderLink = ! href || href === '#' || href === '';
					if ( isPlaceholderLink ) {
						e.preventDefault();
					}
				}
			}
		}
	}, true ); // Use capture phase to run before other handlers

	// Smooth scroll for anchor links (but skip links with sub-menus on mobile)
	const menuLinks = menu.querySelectorAll( 'a[href^="#"]' );
	menuLinks.forEach( function( link ) {
		link.addEventListener( 'click', function( e ) {
			// Skip if default was already prevented (e.g., by sub-menu handler)
			if ( e.defaultPrevented ) {
				return;
			}
			
			// IMPORTANT: Skip if this link has a sub-menu and we're on mobile
			const parentItem = link.closest( '.menu-item-has-children, .page_item_has_children' );
			if ( parentItem && window.innerWidth <= 1024 ) {
				// Sub-menu handler should handle it
				return;
			}
			
			const href = this.getAttribute( 'href' );
			
			// Only handle if it's a hash link (not just #)
			if ( href && href !== '#' && href.length > 1 ) {
				const targetId = href.substring( 1 );
				// Try to find element by ID
				const targetElement = document.getElementById( targetId ) || 
					document.querySelector( '[data-section="' + targetId + '"]' ) ||
					document.querySelector( 'section[id*="' + targetId + '"]' );
				
				if ( targetElement ) {
					e.preventDefault();
					
					// Close mobile menu if open
					if ( button && button.getAttribute( 'aria-expanded' ) === 'true' ) {
						siteNavigation.classList.remove( 'toggled' );
						document.body.classList.remove( 'menu-open' );
						button.setAttribute( 'aria-expanded', 'false' );
					}
					
					// Calculate offset for fixed header
					const headerHeight = document.getElementById( 'masthead' )?.offsetHeight || 80;
					const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;
					
					window.scrollTo( {
						top: targetPosition,
						behavior: 'smooth'
					} );
				}
			}
		}, false ); // Use bubble phase (after capture phase handlers)
	} );

	// Header scroll effect (optional - add background when scrolled)
	const header = document.getElementById( 'masthead' );
	if ( header ) {
		window.addEventListener( 'scroll', function() {
			const currentScroll = window.pageYOffset;
			
			if ( currentScroll > 50 ) {
				header.classList.add( 'scrolled' );
			} else {
				header.classList.remove( 'scrolled' );
			}
		} );
	}

	// Remove the .toggled class and set aria-expanded to false when the user clicks outside the navigation.
	document.addEventListener( 'click', function( event ) {
		const isClickInside = siteNavigation.contains( event.target );

		if ( ! isClickInside && siteNavigation.classList.contains( 'toggled' ) ) {
			siteNavigation.classList.remove( 'toggled' );
			document.body.classList.remove( 'menu-open' );
			if ( button ) {
				button.setAttribute( 'aria-expanded', 'false' );
			}
		}
	} );

	// Get all the link elements within the menu.
	const links = menu.getElementsByTagName( 'a' );

	// Handle window resize - close submenus when switching to desktop
	window.addEventListener( 'resize', function() {
		// If switched to desktop, close all submenus
		if ( window.innerWidth > 1024 ) {
			const allMenuItemsWithChildren = menu.querySelectorAll( '.menu-item-has-children, .page_item_has_children' );
			allMenuItemsWithChildren.forEach( function( menuItem ) {
				menuItem.classList.remove( 'submenu-open' );
			} );
		}
	} );

	// Get all the link elements with children within the menu.
	const linksWithChildren = menu.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' );

	// Toggle focus each time a menu link is focused or blurred.
	for ( const link of links ) {
		link.addEventListener( 'focus', toggleFocus, true );
		link.addEventListener( 'blur', toggleFocus, true );
	}

	// Toggle focus each time a menu link with children receive a touch event.
	// BUT: Skip if we're on mobile - sub-menu toggle handles it
	for ( const link of linksWithChildren ) {
		link.addEventListener( 'touchstart', function( e ) {
			// On mobile, don't use focus toggle - use sub-menu toggle instead
			if ( window.innerWidth > 1024 ) {
				toggleFocus.call( this, e );
			}
		}, false );
	}

	/**
	 * Sets or removes .focus class on an element.
	 */
	function toggleFocus() {
		if ( event.type === 'focus' || event.type === 'blur' ) {
			let self = this;
			// Move up through the ancestors of the current link until we hit .nav-menu.
			while ( ! self.classList.contains( 'nav-menu' ) ) {
				// On li elements toggle the class .focus.
				if ( 'li' === self.tagName.toLowerCase() ) {
					self.classList.toggle( 'focus' );
				}
				self = self.parentNode;
			}
		}

		if ( event.type === 'touchstart' ) {
			const menuItem = this.parentNode;
			event.preventDefault();
			for ( const link of menuItem.parentNode.children ) {
				if ( menuItem !== link ) {
					link.classList.remove( 'focus' );
				}
			}
			menuItem.classList.toggle( 'focus' );
		}
	}
}() );
