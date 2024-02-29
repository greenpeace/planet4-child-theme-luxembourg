<?php

namespace Greenpeacefrance\Gravityforms\Field;



class FirstNameField extends \GF_Field_Text {

	public $type = 'gp_first_name';

	public $maxLength = 40;

	public $label = 'Prénom';

	public function get_form_editor_field_title() {
		return esc_attr( 'Prénom' );
	}


	public function get_form_editor_button() {
		return [
			'group' => 'greenpeace_fields',
			'text'  => $this->get_form_editor_field_title(),
			'icon' => 'gpfgf-first-name',
		];
	}

	public function get_form_editor_field_description() {
		return esc_attr( 'Champ Prénom norme GP (40 caractères max)' );
	}

	public function is_conditional_logic_supported() {
		return true;
	}


	public function allow_html(){
    	return false;
	}


	public function validate($value, $form) {

		$keep_numbers = false;
		$value = cleanCrmValue($value, $this->maxLength, $keep_numbers);

		if (strlen($value) === 0) {
			$this->failed_validation = true;
			$this->validation_message = 'Votre prénom semble invalide.';
		}

	}




	public function get_value_save_entry($value, $form, $input_name, $lead_id, $lead) {

		$keep_numbers = false;
		$value = cleanCrmValue($value, $this->maxLength, $keep_numbers);

		$value = mb_strtolower($value, 'UTF-8' );
		$value = mb_convert_case($value, MB_CASE_TITLE, 'UTF-8' );

		return $value;
	}


	public function get_form_inline_script_on_page_render( $form ) {
		$id = "input_{$form['id']}_{$this->id}";
		return <<<END
(function() {
	var input = document.getElementById("$id");
	var keepNumbers = false;

	if (input) {
		input.addEventListener('paste', function(e) {
			e.preventDefault()
			let value = (e.clipboardData || window.clipboardData).getData("text");
			// value = window.gpfRemoveAccents( value );

			if (value.match(/[^ 'A-Za-zÀ-ÖØ-öø-ÿ-]/)) {
				jQuery(e.target).parents('.gfield').addClass('field-invalid')
			}
			e.target.value = value
		})
		input.addEventListener('keypress', function(e) {
			e.preventDefault();
			window.gpfCleanInput(e.charCode, e.target, 40, keepNumbers);

			// var v = window.gpfRemoveAccents(e.target.value)
			var v = e.target.value
				.toLowerCase()
				.replace( /([ -])([^ -])/g, (match, p1, p2) => p1 + p2.toUpperCase() );

			e.target.value = v.charAt(0).toUpperCase() + v.slice(1);

			if (e.target.value.match(/[^ 'A-Za-zÀ-ÖØ-öø-ÿ-]/)) {
				jQuery(e.target).parents('.gfield').addClass('field-invalid')
			}
			else {
				jQuery(e.target).parents('.gfield').removeClass('field-invalid')
			}
		});
	}
})();
END;
	}


}