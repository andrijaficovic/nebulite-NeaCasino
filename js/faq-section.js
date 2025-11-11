/**
 * FAQ Section Accordion Functionality
 */
(function() {
	'use strict';

	// Wait for DOM to be ready
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initFAQ );
	} else {
		initFAQ();
	}

	function initFAQ() {
		const faqSections = document.querySelectorAll( '.faq-section' );

		faqSections.forEach( function( section ) {
			const questions = section.querySelectorAll( '.faq-section__question' );

			questions.forEach( function( question ) {
				question.addEventListener( 'click', function() {
					toggleFAQ( question );
				} );

				// Keyboard support
				question.addEventListener( 'keydown', function( e ) {
					if ( e.key === 'Enter' || e.key === ' ' ) {
						e.preventDefault();
						toggleFAQ( question );
					}
				} );
			} );
		} );
	}

	function toggleFAQ( question ) {
		const isExpanded = question.getAttribute( 'aria-expanded' ) === 'true';
		const answer = document.getElementById( question.getAttribute( 'aria-controls' ) );

		if ( ! answer ) {
			return;
		}

		// Toggle aria-expanded
		question.setAttribute( 'aria-expanded', ! isExpanded );

		// Toggle answer visibility
		if ( isExpanded ) {
			answer.setAttribute( 'hidden', '' );
		} else {
			answer.removeAttribute( 'hidden' );
		}
	}
})();

