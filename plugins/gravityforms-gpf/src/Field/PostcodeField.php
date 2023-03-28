<?php

namespace Greenpeacefrance\Gravityforms\Field;

class PostcodeField extends \GF_Field_Text {

	public $type = 'gp_postcode';

	public $maxLength = 5;

	public $inputMask = true;
	public $inputMaskIsCustom = true;
	public $inputMaskValue = '99999';


	public function get_form_editor_field_title() {
		return esc_attr( 'Code postal FR' );
	}


	public function get_form_editor_button() {
		return [
			'group' => 'greenpeace_fields',
			'text'  => $this->get_form_editor_field_title(),
			'icon' => 'gpfgf-postcode',
		];
	}

	public function get_form_editor_field_description() {
		return esc_attr( 'Code posal uniquement pour la France (5 chiffres)' );
	}

	public function is_conditional_logic_supported() {
		return true;
	}


	public function allow_html(){
    	return false;
	}


	public function get_value_save_entry($value, $form, $input_name, $lead_id, $lead) {
		$value = preg_replace('/[^0-9]/', '', $value);
		return $value;
	}



}