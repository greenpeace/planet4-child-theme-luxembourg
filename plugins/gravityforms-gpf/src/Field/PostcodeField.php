<?php

namespace Greenpeacefrance\Gravityforms\Field;

class PostcodeField extends \GF_Field_Text {

	public $type = 'gp_postcode';

	public $maxLength = 5;

	// public $inputMask = true;
	// public $inputMaskIsCustom = true;
	// public $inputMaskValue = '**********';


	public function get_form_editor_field_title() {
		return esc_attr( 'Code postal' );
	}


	public function get_form_inline_script_on_page_render($form) {

		$field_id = $this->id;

		$country_field_id = -1;

		foreach ($form['fields'] as $field) {
			if ($field->type === 'gp_country') {
				$country_field_id = $field->id;
			}
		}

		if ( $country_field_id === -1 ) {
			return;
		}

		return <<<END
		gform.addAction( "gform_input_change", function( elem, formId, fieldId ) {
			var mask = ""

			if ( parseInt(fieldId) === $country_field_id) {
				switch (elem.value) {
					case 'France':
						mask = "99999";
						break;
					case 'Luxembourg':
						mask = "L9999";
						break;
					case 'Belgique':
						mask = "B9999";
						break;
					case 'Suisse':
						mask = "9999";
						break;
					case 'Allemagne':
						mask = "99999";
						break;

				}

				if (mask === "") {
					jQuery("#input_"+formId+"_{$field_id}").unmask()
				}
				else {
					jQuery("#input_"+formId+"_{$field_id}").mask(mask)
				}
			}

		}, 10, 3 );
		END;
	}


	public function get_form_editor_button() {
		return [
			'group' => 'greenpeace_fields',
			'text'  => $this->get_form_editor_field_title(),
			'icon' => 'gpfgf-postcode',
		];
	}

	public function get_form_editor_field_description() {
		return esc_attr( "Code postal qui s'adapte au pays choisi." );
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