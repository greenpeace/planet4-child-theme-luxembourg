<?php

namespace Greenpeacefrance\Gravityforms\Field;


class StateButtonField extends \GF_Field_Checkbox {

	public $type = 'gp_statebutton';
	public $label = 'Bouton à Etat';

	public function __construct($data = []) {

		if ( empty($data['choices'])) {
			$data['choices'] = [
				[
					'text' => 'Configurez-moi',
					'isSelected' => false,
				]
			];
		}

		parent::__construct($data);
	}




	public function get_form_editor_field_title() {
		return esc_attr( $this->label );
	}

	public function get_form_editor_field_description() {
		return esc_attr( 'Affiche un bouton avec un état (comme une checkbox) pour utilisation dans des conditions.' );
	}

	public function get_form_editor_button() {
		return array(
			'group' => 'standard_fields',
			'text'  => $this->get_form_editor_field_title(),
			'icon' => 'gform-icon--consent',
		);
	}


	public function get_form_editor_field_settings() {
		return array(
			'conditional_logic_field_setting',
			'label_setting',
			'rules_setting',
			'choices_setting',
			'description_setting',
			'css_class_setting',
		);
	}


	public function is_conditional_logic_supported() {
		return true;
	}



	public function get_value_save_entry( $value, $form, $input_name, $lead_id, $lead ) {
		return "";
	}


	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {
		return "";
	}


	public function get_field_content( $value, $force_frontend_label, $form ) {
		$is_form_editor  = $this->is_form_editor();
		$is_entry_detail = $this->is_entry_detail();
		$is_admin        = $is_form_editor || $is_entry_detail;

		if ( ! $is_admin ) {
			return '{FIELD}';
		}

		return parent::get_field_content( $value, $force_frontend_label, $form );
	}


	public function get_field_label_class() {
		return parent::get_field_label_class() . ' statebutton_label';
	}


}
