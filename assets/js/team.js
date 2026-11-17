// Delegated so instances re-rendered by the Elementor editor keep working without re-initialisation.
document.addEventListener( 'click', ( event ) => {
	// Not `instanceof Element`: editor re-renders insert nodes from the parent window's realm.
	const button = event.target.closest?.( '[data-nova-team-filter]' );
	const team = button ? button.closest( '.nova-team' ) : null;
	if ( ! team ) {
		return;
	}
	const role = button.dataset.novaTeamFilter;
	team.querySelectorAll( '[data-nova-team-filter]' ).forEach( ( item ) => item.setAttribute( 'aria-pressed', String( item === button ) ) );
	team.querySelectorAll( '[data-nova-team-role]' ).forEach( ( item ) => {
		item.hidden = '' !== role && item.dataset.novaTeamRole !== role;
	} );
} );
