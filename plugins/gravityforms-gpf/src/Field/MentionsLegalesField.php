<?php

namespace Greenpeacefrance\Gravityforms\Field;

class MentionsLegalesField extends \GF_Field_HTML {

	public $type = 'gp_mentions_legales';

	public $label = '';

	public $displayOnly = true;

	public $content = '<p>
	En cliquant sur &laquo;&nbsp;%s&nbsp;&raquo; bla bla
</p>';


	public $description = 'Un %s sera remplacé par le label du CTA.';



	public function __construct() {
		parent::__construct();
		$this->label = 'Mentions légales';
	}

	public function get_form_editor_field_title() {
		return esc_attr( $this->label );
	}



	public function get_form_editor_button() {
		return [
			'group' => 'greenpeace_fields',
			'text'  => $this->get_form_editor_field_title(),
			'icon' => 'gpfgf-mentions',
		];
	}


	public function is_conditional_logic_supported() {
		return true;
	}



	public function get_value_save_entry($value, $form, $input_name, $lead_id, $lead) {
		return '';
	}


	public function get_field_input( $form, $value = '', $entry = null ) {

		$button_label = $form['button']['text'];

		return sprintf( $this->content, $button_label );
	}


	public function get_field_content( $value, $force_frontend_label, $form ) {
		$is_form_editor  = $this->is_form_editor();
		$is_entry_detail = $this->is_entry_detail();
		$is_admin        = $is_form_editor || $is_entry_detail;

		if ( $is_admin ) {
			return parent::get_field_content( $value, $force_frontend_label, $form );

		}

		return '<div>{FIELD}</div>';
	}



	public function get_value_merge_tag( $value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br ) {
		return '';
	}


	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {
		return '';
  	}


	public function get_value_export( $entry, $input_id = '', $use_text = false, $is_csv = false ) {
		return '';
   	}

}

