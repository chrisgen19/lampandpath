/**
 * Page interactions: topic filters and Load more, Copy and Share, Save for
 * later, "I prayed", the prayer and newsletter forms, and the article view counter.
 *
 * All of it is progressive enhancement. Without JavaScript the filters and
 * Load more are links and the forms post to admin-post.php. If a request fails,
 * links fall back to normal navigation. Settings and messages come from
 * window.lampandpath (see lampandpath_interactions_config() in inc/enqueue.php).
 */
( function () {
	const config = window.lampandpath || {};
	const strings = config.strings || {};
	const live = document.getElementById( 'lp-live' );

	const format = ( template, value ) => String( template ).replace( /%[sd]/, value );

	/** Announces a message to screen reader users through the page's live region. */
	const announce = ( message ) => {
		if ( ! live || ! message ) {
			return;
		}
		live.textContent = '';
		window.setTimeout( () => {
			live.textContent = message;
		}, 50 );
	};

	/** localStorage that tolerates private mode and full storage. */
	const storage = {
		get( key, fallback ) {
			try {
				return JSON.parse( window.localStorage.getItem( key ) ) ?? fallback;
			} catch {
				return fallback;
			}
		},
		set( key, value ) {
			try {
				window.localStorage.setItem( key, JSON.stringify( value ) );
			} catch {}
		},
	};

	/**
	 * Calls a lampandpath/v1 route. Write routes first fetch a fresh token, so a
	 * page served from a cache never sends an expired one.
	 */
	const api = async ( path, { method = 'GET', body, query, token = false } = {} ) => {
		const url = new URL( config.rest + path, window.location.href );
		Object.entries( query || {} ).forEach( ( [ key, value ] ) => url.searchParams.set( key, value ) );

		const headers = { Accept: 'application/json' };
		if ( body ) {
			headers[ 'Content-Type' ] = 'application/json';
		}
		if ( token ) {
			headers[ 'X-LP-Token' ] = ( await api( 'token' ) ).token;
		}

		const response = await fetch( url, { method, headers, body: body ? JSON.stringify( body ) : undefined, credentials: 'same-origin' } );
		const data = 204 === response.status ? null : await response.json().catch( () => null );
		if ( ! response.ok ) {
			throw new Error( ( data && data.message ) || strings.error );
		}
		return data;
	};

	/* Copy and Share ------------------------------------------------------ */

	const copyText = async ( text ) => {
		try {
			await navigator.clipboard.writeText( text );
			return true;
		} catch {
			// Older browsers and non-secure contexts: copy through a temporary textarea.
			const area = document.createElement( 'textarea' );
			area.value = text;
			area.setAttribute( 'readonly', '' );
			area.style.cssText = 'position:fixed;opacity:0';
			document.body.append( area );
			area.select();
			const copied = document.execCommand( 'copy' );
			area.remove();
			return copied;
		}
	};

	/** Briefly swaps a button's visible label, e.g. to "Copied". */
	const flashLabel = ( button, text ) => {
		const label = button.querySelector( '[data-lp-label]' );
		if ( ! label ) {
			return;
		}
		label.dataset.lpOriginal = label.dataset.lpOriginal || label.textContent;
		label.textContent = text;
		window.clearTimeout( label.lpTimer );
		label.lpTimer = window.setTimeout( () => {
			label.textContent = label.dataset.lpOriginal;
		}, 2000 );
	};

	const onCopy = async ( button ) => {
		if ( await copyText( button.dataset.lpCopy ) ) {
			flashLabel( button, strings.copiedShort );
			announce( strings.copied );
		} else {
			announce( strings.copyFailed );
		}
	};

	const onShare = async ( button ) => {
		const text = button.dataset.lpShare;
		const url = button.dataset.lpShareUrl || window.location.href;
		if ( navigator.share ) {
			try {
				await navigator.share( { text, url } );
			} catch {} // Closing the share sheet is not an error.
			return;
		}
		if ( await copyText( `${ text } ${ url }` ) ) {
			flashLabel( button, strings.linkCopied );
			announce( strings.linkCopied );
		} else {
			announce( strings.copyFailed );
		}
	};

	/* Save for later ------------------------------------------------------ */

	const SAVED = 'lampandpath:saved';

	const syncSaved = () => {
		const saved = storage.get( SAVED, [] );
		document.querySelectorAll( '[data-lp-save]' ).forEach( ( button ) => {
			button.setAttribute( 'aria-pressed', String( saved.includes( Number( button.dataset.lpSave ) ) ) );
		} );
	};

	const onSave = ( button ) => {
		const id = Number( button.dataset.lpSave );
		const saved = storage.get( SAVED, [] );
		const isSaved = saved.includes( id );
		storage.set( SAVED, isSaved ? saved.filter( ( item ) => item !== id ) : [ ...saved, id ] );
		syncSaved();
		announce( isSaved ? strings.unsaved : strings.saved );
	};

	/* I prayed ------------------------------------------------------------ */

	const PRAYED = 'lampandpath:prayed';

	const syncPrayed = () => {
		const prayed = storage.get( PRAYED, [] );
		document.querySelectorAll( '[data-lp-prayed]' ).forEach( ( button ) => {
			button.setAttribute( 'aria-pressed', String( prayed.includes( Number( button.dataset.lpPrayed ) ) ) );
		} );
	};

	/** Counts once per browser; the button then stays pressed. */
	const onPrayed = async ( button ) => {
		const id = Number( button.dataset.lpPrayed );
		if ( 'true' === button.getAttribute( 'aria-pressed' ) || button.getAttribute( 'aria-busy' ) ) {
			return;
		}
		button.setAttribute( 'aria-busy', 'true' );
		try {
			const result = await api( `prayers/${ id }/prayed`, { method: 'POST', token: true } );
			storage.set( PRAYED, [ ...storage.get( PRAYED, [] ), id ] );
			syncPrayed();
			announce( result.message );
		} catch ( error ) {
			announce( error.message );
		} finally {
			button.removeAttribute( 'aria-busy' );
		}
	};

	/* Topic filters and Load more ----------------------------------------- */

	const list = document.querySelector( '[data-lp-articles]' );
	const more = document.querySelector( '[data-lp-load-more]' );
	const listState = { category: 0, page: 1 };

	const fetchArticles = ( category, page ) => api( 'articles', {
		query: {
			category,
			page,
			per_page: list.dataset.lpPerPage || 5,
			heading: list.dataset.lpHeading || 'h2',
			exclude: list.dataset.lpExclude || '',
		},
	} );

	const emptyMessage = () => {
		const message = document.createElement( 'p' );
		message.className = 'py-8 text-lg text-ink-soft';
		message.textContent = strings.noArticles;
		return message;
	};

	const onFilter = async ( link, event ) => {
		if ( ! list || '' === link.dataset.lpFilter ) {
			return; // Not a category chip: follow the link.
		}
		event.preventDefault();
		const category = Number( link.dataset.lpFilter );
		list.setAttribute( 'aria-busy', 'true' );
		try {
			const data = await fetchArticles( category, 1 );
			Object.assign( listState, { category, page: 1 } );
			list.innerHTML = data.html;
			if ( ! data.count ) {
				list.append( emptyMessage() );
			}
			document.querySelectorAll( '[data-lp-filter]' ).forEach( ( chip ) => chip.removeAttribute( 'aria-current' ) );
			link.setAttribute( 'aria-current', 'true' );
			if ( more ) {
				more.hidden = ! data.has_more;
			}
			announce( category ? format( strings.filtered, link.textContent.trim() ) : strings.filteredAll );
		} catch {
			window.location.href = link.href;
		} finally {
			list.removeAttribute( 'aria-busy' );
		}
	};

	/** Appends the next page and moves focus to the first new article for keyboard users. */
	const onLoadMore = async ( link, event ) => {
		if ( ! list ) {
			return;
		}
		event.preventDefault();
		list.setAttribute( 'aria-busy', 'true' );
		try {
			const data = await fetchArticles( listState.category, listState.page + 1 );
			listState.page += 1;
			const firstNew = list.children.length;
			list.insertAdjacentHTML( 'beforeend', data.html );
			list.children[ firstNew ]?.querySelector( 'h2 a, h3 a' )?.focus();
			link.hidden = ! data.has_more;
			announce( format( strings.loaded, data.count ) );
		} catch {
			window.location.href = link.href;
		} finally {
			list.removeAttribute( 'aria-busy' );
		}
	};

	/* Chip styles follow aria-current, so the active chip looks selected. */
	const styleChips = () => {
		const active = [ 'border-accent', 'bg-accent', 'font-semibold', 'text-white' ];
		const idle = [ 'border-line-strong', 'bg-white', 'font-medium', 'text-ink', 'hover:border-ink' ];
		document.querySelectorAll( '[data-lp-filter]' ).forEach( ( chip ) => {
			const current = 'true' === chip.getAttribute( 'aria-current' );
			chip.classList.remove( ...( current ? idle : active ) );
			chip.classList.add( ...( current ? active : idle ) );
		} );
	};

	/* Forms --------------------------------------------------------------- */

	const setStatus = ( status, state, message ) => {
		status.dataset.state = state;
		status.textContent = message;
	};

	const onFormSubmit = async ( form, route, event ) => {
		if ( ! config.rest ) {
			return; // Post normally to admin-post.php.
		}
		event.preventDefault();
		const status = form.querySelector( '[data-lp-form-status]' );
		const button = form.querySelector( '[type="submit"]' );
		const data = Object.fromEntries( new FormData( form ) );
		if ( form.elements.wall_consent ) {
			data.wall_consent = form.elements.wall_consent.checked;
		}

		button.disabled = true;
		setStatus( status, 'info', strings.sending );
		try {
			const result = await api( route, { method: 'POST', token: true, body: data } );
			form.reset();
			setStatus( status, 'success', result.message );
		} catch ( error ) {
			setStatus( status, 'error', error.message );
		} finally {
			button.disabled = false;
			status.focus();
		}
	};

	/* View counter -------------------------------------------------------- */

	/** Counts one view per article per browser per day; editors are skipped server-side. */
	const countView = () => {
		const id = config.viewPost;
		if ( ! id || ! config.rest ) {
			return;
		}
		const today = new Date().toISOString().slice( 0, 10 );
		const viewed = Object.fromEntries( Object.entries( storage.get( 'lampandpath:viewed', {} ) ).filter( ( [ , day ] ) => day === today ) );
		if ( viewed[ id ] ) {
			return;
		}
		viewed[ id ] = today;
		storage.set( 'lampandpath:viewed', viewed );
		fetch( new URL( `${ config.rest }views/${ id }`, window.location.href ), { method: 'POST', keepalive: true } ).catch( () => {} );
	};

	/* Wiring -------------------------------------------------------------- */

	document.addEventListener( 'click', ( event ) => {
		const target = event.target.closest( '[data-lp-copy], [data-lp-share], [data-lp-save], [data-lp-prayed], [data-lp-filter], [data-lp-load-more]' );
		if ( ! target ) {
			return;
		}
		if ( target.matches( '[data-lp-copy]' ) ) {
			onCopy( target );
		} else if ( target.matches( '[data-lp-share]' ) ) {
			onShare( target );
		} else if ( target.matches( '[data-lp-save]' ) ) {
			onSave( target );
		} else if ( target.matches( '[data-lp-prayed]' ) ) {
			onPrayed( target );
		} else if ( target.matches( '[data-lp-filter]' ) ) {
			onFilter( target, event ).then( styleChips );
		} else {
			onLoadMore( target, event );
		}
	} );

	document.addEventListener( 'submit', ( event ) => {
		if ( event.target.matches( '[data-lp-prayer-form]' ) ) {
			onFormSubmit( event.target, 'prayers', event );
		} else if ( event.target.matches( '[data-lp-newsletter-form]' ) ) {
			onFormSubmit( event.target, 'subscribers', event );
		}
	} );

	syncSaved();
	syncPrayed();
	countView();
} )();
