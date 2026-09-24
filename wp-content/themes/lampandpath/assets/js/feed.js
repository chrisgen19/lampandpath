/**
 * "Load more" for paged lists (template-parts/components/feed-more.php).
 *
 * Fetches the next page of the listing as HTML and appends its
 * [data-lp-feed] items, so every listing (categories, topics, dates, writers,
 * collections, search, verses, plans, the prayer wall) loads more with exactly
 * the query of its numbered pages. Without JavaScript the numbered links work
 * as normal. Appended items are announced to other scripts with a bubbling
 * "lampandpath:feed" event (detail.items).
 *
 * Loaded with `defer`, so the DOM is ready when this runs.
 */
( function () {
	const feed = document.querySelector( '[data-lp-feed]' );
	const nav = document.querySelector( '[data-lp-feed-nav]' );
	const more = nav && nav.querySelector( '[data-lp-feed-more]' );

	if ( ! feed || ! more || ! window.fetch || ! window.DOMParser ) {
		return;
	}

	const pages = nav.querySelector( '[data-lp-feed-pages]' );
	const status = nav.querySelector( '[data-lp-feed-status]' );

	more.hidden = false;
	// On the first page "Load more" replaces the numbered links; on later pages they stay so readers can go back.
	if ( pages && '1' === nav.dataset.lpFeedPage ) {
		pages.hidden = true;
	}

	/**
	 * Returns what to focus in a new item: its title link, or the item itself.
	 *
	 * @param {Element} item List item.
	 * @return {HTMLElement} Element to focus.
	 */
	const focusTarget = ( item ) => {
		const link = item.querySelector( 'h2 a, h3 a' );
		if ( link ) {
			return link;
		}
		item.setAttribute( 'tabindex', '-1' );
		return item;
	};

	more.addEventListener( 'click', async ( event ) => {
		event.preventDefault();
		if ( 'true' === more.getAttribute( 'aria-disabled' ) ) {
			return;
		}

		more.setAttribute( 'aria-disabled', 'true' );
		feed.setAttribute( 'aria-busy', 'true' );

		try {
			const response = await fetch( more.href, { credentials: 'same-origin' } );
			if ( ! response.ok ) {
				throw new Error( `HTTP ${ response.status }` );
			}

			const next = new DOMParser().parseFromString( await response.text(), 'text/html' );
			const nextFeed = next.querySelector( '[data-lp-feed]' );
			if ( ! nextFeed ) {
				throw new Error( 'No list on the next page' );
			}

			// Skip anything already listed, e.g. a sticky article that also appears in its usual place.
			const items = [ ...nextFeed.children ].filter( ( item ) => ! item.id || ! document.getElementById( item.id ) );
			feed.append( ...items );

			const nextPages = next.querySelector( '[data-lp-feed-pages]' );
			if ( pages && nextPages ) {
				pages.innerHTML = nextPages.innerHTML;
			}

			const nextMore = next.querySelector( '[data-lp-feed-more]' );
			let message = ( nav.dataset.lpFeedLoaded || '' ).replace( '%d', String( items.length ) );
			if ( nextMore ) {
				more.href = nextMore.getAttribute( 'href' );
			} else {
				more.remove();
				message += ' ' + ( nav.dataset.lpFeedEnd || '' );
			}

			if ( status ) {
				status.textContent = message.trim();
			}
			if ( items.length ) {
				focusTarget( items[ 0 ] ).focus();
			}
			feed.dispatchEvent( new CustomEvent( 'lampandpath:feed', { bubbles: true, detail: { items } } ) );
		} catch ( error ) {
			// Open the next page instead, so the reader still gets there.
			window.location.assign( more.href );
		} finally {
			more.removeAttribute( 'aria-disabled' );
			feed.removeAttribute( 'aria-busy' );
		}
	} );
} )();
