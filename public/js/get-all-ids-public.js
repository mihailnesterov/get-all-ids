(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 */
	 jQuery(function() {
		
		$('#wp-admin-bar-get-all-ids-admin-bar-item').on('mouseover', function() {
			$('.get-all-ids-toolbar-container').show();
		});

		$('#wp-admin-bar-get-all-ids-admin-bar-item').on('mouseleave', function() {
			$('#get-all-ids-toolbar-attach').prop('checked') === false ?
			$('.get-all-ids-toolbar-container').hide() :
			$('.get-all-ids-toolbar-container').show();
		});

		$('#get-all-ids-toolbar-attach').prop(
			'checked', 
			localStorage.getItem('_gai_public_attach') === 'true' ? true : false
		);
		
		localStorage.getItem('_gai_public_attach') === 'false' ?
		$('.get-all-ids-toolbar-container').hide() :
		$('.get-all-ids-toolbar-container').show();

		$('#get-all-ids-toolbar-attach').on('click', function() {
			if ($(this).is(':checked')){
				localStorage.setItem('_gai_public_attach', true);
			} else {
				localStorage.setItem('_gai_public_attach', false);
			}
		});
	 });
	 /*
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
})( jQuery );
