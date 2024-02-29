<?php

namespace Greenpeacefrance\Gravityforms\Field;



class CityField extends \GF_Field_Text {

	public $type = 'gp_city';

	public $maxLength = 32;

	public function get_form_editor_field_title() {
		return esc_attr( 'Ville' );
	}


	public function get_form_editor_button() {
		return [
			'group' => 'greenpeace_fields',
			'text'  => $this->get_form_editor_field_title(),
			'icon' => 'gpfgf-city1',
		];
	}

	public function get_form_editor_field_description() {
		return esc_attr( 'Ville norme GP / AFNOR (32 caractères max)' );
	}


	public function is_conditional_logic_supported() {
		return true;
	}


	public function allow_html(){
    	return false;
	}


	public function get_value_save_entry($value, $form, $input_name, $lead_id, $lead) {

		$keep_numbers = false;
		$value = cleanCrmValue( $value, $this->maxLength, $keep_numbers );
		$value = mb_strtoupper( $value, 'UTF-8' );

		// suppression des accents à l'ancienne
		$value = htmlentities( $value );
		$value = preg_replace('/&([A-Z])[^;]+;/', '$1', $value);


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
			value = window.gpfRemoveAccents( value.toUpperCase() );
			if (value.match(/[^ A-Za-z-]/)) {
				jQuery(e.target).parents('.gfield').addClass('field-invalid')
			}
			e.target.value = value
		})
		input.addEventListener('keypress', function(e) {
			e.preventDefault();
			window.gpfCleanInput(e.charCode, e.target, 40, keepNumbers);
			e.target.value = window.gpfRemoveAccents( e.target.value.toUpperCase() );
			if (e.target.value.match(/[^ A-Za-z-]/)) {
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