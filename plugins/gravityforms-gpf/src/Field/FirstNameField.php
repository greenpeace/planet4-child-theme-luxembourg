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


	public function get_value_save_entry($value, $form, $input_name, $lead_id, $lead) {

		// $this->log_debug( __METHOD__ . "(): '.$lead_id.' Avant save entry du prénom : " .  $value );

		$keep_numbers = false;
		$value = cleanCrmValue($value, $this->maxLength, $keep_numbers);


		$value = mb_strtolower($value, 'UTF-8' );
		$value = mb_convert_case($value, MB_CASE_TITLE, 'UTF-8' );

		// $this->log_debug( __METHOD__ . "(): '.$lead_id.' Après save entry du prénom : " .  $value );
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
			window.gpfCleanInput(e.charCode, e.target, 40, keepNumbers);
			var v = e.target.value
				.toLowerCase()
				.replace( /([ -])([^ -])/g, (match, p1, p2) => p1 + p2.toUpperCase() );

			e.target.value = v.charAt(0).toUpperCase() + v.slice(1);
		});
	}
})();
END;
	}


}