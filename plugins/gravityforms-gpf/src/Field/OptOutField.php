<?php

namespace Greenpeacefrance\Gravityforms\Field;



class OptOutField extends \GF_Field_Checkbox {

	public $type = 'gp_optout';

	public $label = 'Opt-out';

	public $enableChoiceValue = false;


	public function __construct($data = []) {

		if ( empty($data['choices'])) {
			$data['choices'] = [
				[
					'text' => 'Oui je veux',
					'value' => 'Y',
					'isSelected' => true,
				]
			];
		}



		parent::__construct($data);
	}

	public function get_form_editor_field_title() {
		return esc_attr( $this->label );
	}


	public function get_form_editor_button() {
		return [
			'group' => 'greenpeace_fields',
			'text'  => $this->get_form_editor_field_title(),
			'icon' => 'gpfgf-optout',
		];
	}

	public function get_form_editor_field_description() {
		return esc_attr( 'Checkbox Opt-out.' );
	}

	public function is_conditional_logic_supported() {
		return true;
	}


	public function get_value_save_entry($value, $form, $input_name, $lead_id, $lead) {

		$key = $input_name . '_1';
		if ( isset($_POST[$key])) {
			// on ne s'occupe pas de la valeur. La seule présence du champ équivaut à Yes
			$value = 'Y';
		}
		else {
			$value = 'N';
		}

		return $value;
	}


	public function get_field_container_tag($form) {
		return 'div';
	}



	public function get_field_input( $form, $value = '', $entry = null ) {

		$form_id = absint( $form['id'] );

		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();

		$id            = $this->id;
		$field_id      = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";
		$tabindex           = $this->get_tabindex();
		$disabled_text = $is_form_editor ? 'disabled="disabled"' : '';

		// Get checkbox choices markup.
		$choices_markup = $this->get_checkbox_choices( $value, $disabled_text, $form_id );

		return sprintf(
				"<div class='ginput_container ginput_container_checkbox'><div class='gfield_checkbox' id='%s'>%s</div></div>",
				esc_attr( $field_id ),
				$choices_markup
		);

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


	public function get_value_export( $entry, $input_id = '', $use_text = false, $is_csv = false ) {
		return '';
   	}


}