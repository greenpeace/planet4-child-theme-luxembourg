

// function nroChanged($this) {
// 	console.log('nro changed', $this)
// 	if ($this.value === 'lu') {
// console.log('lux')
// 	}
// }


jQuery(document).ready(function($) {

	// $('#gform-settings-section-webhook').display
	// window.gf_vars._regex = 'match regex';

	// gform.addFilter('gform_conditional_logic_operators', function (operators, objectType, fieldId) {
	// 	operators.regex = "_regex"

	// 	return operators;
	// } );


	gform.addFilter( 'gform_editor_field_settings', function( settings, field ) {
		if (field.type === 'quiz') {
			settings.push('.gp_quiz_all_correct')
		}

		return settings;
	} );

});


jQuery( document ).on(
	'gform_load_field_settings',
	function ( event, field, form ) {

		if ( field.type === 'quiz' ) {
			jQuery( '#gpf-quiz-all-choices-correct' ).prop( 'checked', field.gpfQuizAllChoicesCorrect );
		}
})