<?php

namespace Greenpeacefrance\Gravityforms\Field;

class FakeCountryField extends \GF_Field_Select {

	public $type = 'gp_fakecountry';


	public $label = 'Pays';

	public $choices = [
		[
			'text' => 'France',
			'value' => 'France',
			'isSelected' => true,
		]
	];

	public function get_form_editor_field_title() {
		return esc_attr( 'Pays Fake' );
	}


	public function get_form_editor_field_description() {
		return esc_attr( 'Fausse liste déroulante ne contenant que le pays France pour nudger les utilisateurs.' );
	}


	public function get_form_editor_button() {
		return [
			'group' => 'greenpeace_fields',
			'text'  => $this->get_form_editor_field_title(),
			'icon' => 'gpfgf-country',
		];
	}

	public function is_conditional_logic_supported() {
		return true;
	}

	public function sanitize_entry_value( $value, $form_id ) {
		return 'France';
	}

	public function get_value_save_entry($value, $form, $input_name, $lead_id, $lead) {
		return 'France';
	}

	public function get_field_input( $form, $value = '', $entry = null ) {
		$form_id         = absint( $form['id'] );
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();

		$id       = $this->id;
		$field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";

		$size                   = $this->size;
		$class_suffix           = $is_entry_detail ? '_admin' : '';
		$class                  = $size . $class_suffix;
		$css_class              = trim( esc_attr( $class ) . ' gfield_select' );
		$tabindex               = $this->get_tabindex();
		// $disabled_text          = 'disabled="disabled"';
		$required_attribute     = $this->isRequired ? 'aria-required="true"' : '';
		// $invalid_attribute      = 'aria-invalid="false"';
		$describedby_attribute = $this->get_aria_describedby();
		$autocomplete_attribute = $this->enableAutocomplete ? $this->get_field_autocomplete_attribute() : '';

		return sprintf( '<div class="ginput_container ginput_container_select"><select name="input_%d" id="%s" class="%s" readonly="readonly" aria-invalid="false" %s %s %s %s>%s</select></div>',
			$id, $field_id, $css_class,
			$tabindex, $describedby_attribute,
			$required_attribute, $autocomplete_attribute,

			$this->get_choices( $value ) );

}


}