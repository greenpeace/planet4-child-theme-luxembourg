

jQuery(document).ready(function($) {

	gform.addFilter( 'gform_editor_field_settings', function( settings, field ) {

		settings.push('.remove_from_url_setting');

		return settings;
	} );

});


jQuery( document ).on('gform_load_field_settings',
	function ( event, field, form ) {

		jQuery('#field_remove_from_url').prop( 'checked', !! field.removeFromUrl)
})