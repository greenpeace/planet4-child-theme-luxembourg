<?php

namespace Greenpeacefrance\Gravityforms\Field;



class LastNameField extends \GF_Field_Text {

	public $type = 'gp_last_name';

	public $maxLength = 80;

	public function get_form_editor_field_title() {
		return esc_attr( 'Nom' );
	}


	public function get_form_editor_button() {
		return [
			'group' => 'greenpeace_fields',
			'text'  => $this->get_form_editor_field_title(),
			'icon' => 'gpfgf-last-name',
		];
	}

	public function get_form_editor_field_description() {
		return esc_attr( 'Champ Nom norme GP (80 caractères max)' );
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


		return $value;
	}



	public function get_form_inline_script_on_page_render( $form ) {
		$id = "input_{$form['id']}_{$this->id}";
		return <<<END
(function() {
	var input = document.getElementById("$id");
	var keepNumbers = false;

	if (input) {
		input.addEventListener('keypress', function(e) {
			e.preventDefault();
			gpfCleanInput(e.charCode, e.target, 80, keepNumbers);
			e.target.value = e.target.value.toUpperCase();
		});
	}
})();
END;
	}


}