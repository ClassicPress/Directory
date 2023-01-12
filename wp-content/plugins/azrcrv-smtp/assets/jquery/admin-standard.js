/*
 * Tabs
 */
jQuery(
	function($) {
		'use strict';

		$( '#tabs ul li a' ).on(
			'keyup click',
			function(e) {
				if (e.key === 'Enter' || e.type === 'click') {
					var id = $( this ).attr( 'href' );
					$( '.azrcrv-ui-state-active' ).removeClass( 'azrcrv-ui-state-active' ).attr( 'aria-selected', 'false' ).attr( 'aria-expanded', 'false' );
					$( this ).parent( 'li' ).addClass( 'azrcrv-ui-state-active' ).attr( 'aria-selected', 'true' ).attr( 'aria-expanded', 'true' );
					$( this ).closest( 'ul' ).siblings().addClass( 'azrcrv-ui-tabs-hidden' ).attr( 'aria-hidden', 'true' );
					$( id ).removeClass( 'azrcrv-ui-tabs-hidden' ).attr( 'aria-hidden', 'false' );
					e.preventDefault();
				}
			}
		);

		$( '#tabs ul li a' ).hover(
			function() { $( this ).addClass( 'azrcrv-ui-state-hover' ); },
			function() { $( this ).removeClass( 'azrcrv-ui-state-hover' ); }
		);
	}
);
