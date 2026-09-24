/**
 * Header interactions: mobile menu toggle and the search dialog.
 *
 * Loaded with `defer`, so the DOM is ready when this runs.
 */
( function () {
	// Matches Tailwind's xl breakpoint, where the header shows the full menu.
	const desktopQuery = window.matchMedia( '(min-width: 80rem)' );

	/**
	 * Mobile menu: the toggle button shows/hides #site-navigation below the xl breakpoint.
	 */
	const toggle = document.querySelector( '[data-lp-nav-toggle]' );
	const nav = document.getElementById( 'site-navigation' );

	const setMenuOpen = ( open ) => {
		toggle.setAttribute( 'aria-expanded', String( open ) );
		nav.classList.toggle( 'is-open', open );
	};

	if ( toggle && nav ) {
		toggle.addEventListener( 'click', () => {
			setMenuOpen( toggle.getAttribute( 'aria-expanded' ) !== 'true' );
		} );

		document.addEventListener( 'keydown', ( event ) => {
			if ( 'Escape' === event.key && 'true' === toggle.getAttribute( 'aria-expanded' ) ) {
				setMenuOpen( false );
				toggle.focus();
			}
		} );

		// The panel is desktop-hidden anyway; reset state so it is closed when returning to mobile.
		desktopQuery.addEventListener( 'change', ( event ) => {
			if ( event.matches ) {
				setMenuOpen( false );
			}
		} );
	}

	/**
	 * Search dialog: native <dialog> handles focus trapping, Escape and focus return.
	 */
	const dialog = document.getElementById( 'search-dialog' );

	if ( dialog && 'function' === typeof dialog.showModal ) {
		document.querySelectorAll( '[data-lp-search-open]' ).forEach( ( button ) => {
			button.addEventListener( 'click', () => {
				if ( toggle && nav ) {
					setMenuOpen( false );
				}
				dialog.showModal();
				dialog.querySelector( 'input[type="search"]' )?.focus();
			} );
		} );

		dialog.querySelector( '[data-lp-search-close]' )?.addEventListener( 'click', () => dialog.close() );

		// Clicks on the backdrop land on the <dialog> element itself; content sits in an inner wrapper.
		dialog.addEventListener( 'click', ( event ) => {
			if ( event.target === dialog ) {
				dialog.close();
			}
		} );
	}
} )();
