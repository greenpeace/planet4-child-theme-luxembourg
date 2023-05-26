<?php

namespace Greenpeacefrance\Gravityforms\Field;



class AddressField extends \GF_Field_Text {

	public $type = 'gp_address';

	public $maxLength = 38;

	public $label = 'Adresse postale';


	public function get_form_editor_field_title() {
		return esc_attr( $this->label );
	}



	public function get_form_editor_button() {
		return [
			'group' => 'greenpeace_fields',
			'text'  => $this->get_form_editor_field_title(),
			'icon' => 'gpfgf-address1',
		];
	}

	public function get_form_editor_field_description() {
		return esc_attr( 'Champ d\'adresse norme GP / AFNOR (38 caractères max)' );
	}

	public function is_conditional_logic_supported() {
		return true;
	}


	public function allow_html(){
    	return false;
	}


	public function get_value_save_entry($value, $form, $input_name, $lead_id, $lead) {
		$value = cleanCrmValue($value, $this->maxLength);
		$value = mb_strtoupper( $value, 'UTF-8' );
		return $value;
	}


	public function get_form_inline_script_on_page_render( $form ) {
		$id = "input_{$form['id']}_{$this->id}";
		return <<<END
(function() {
	var input = document.getElementById("$id");
	var keepNumbers = true;

	if (input) {
		input.addEventListener('keypress', function(e) {
			e.preventDefault();
			window.gpfCleanInput(e.charCode, e.target, {$this->maxLength}, keepNumbers);
			e.target.value = window.gpfRemoveAccents( e.target.value.toUpperCase() );
		});
	}
})();
END;
	}


}