<?php

namespace Greenpeacefrance\Gravityforms\Field;

class SousAccrocheField extends \GF_Field_HTML {

	public $type = 'gp_sousaccroche';

	public $label = 'Sous Accroche';



	public function get_form_editor_field_title() {
		return esc_attr( $this->label );
	}


	public function get_form_editor_field_description() {
		return esc_attr( 'Texte en exergue entre guillemets' );
	}


	public function get_form_editor_button() {
		return [
			'group' => 'greenpeace_fields',
			'text'  => $this->get_form_editor_field_title(),
			'icon' => 'gpfgf-html',
		];
	}

	public function is_conditional_logic_supported() {
		return true;
	}

	public function get_field_input( $form, $value = '', $entry = null ) {

		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();

		if ($is_entry_detail || $is_form_editor) {
			return '<div class="gf-html-container"><span class="gf_blockheader"><i class="fa fa-code fa-lg"></i> Sous-accroche </span><span>Texte dans les settings</span></div>';

		}


		$content = $this->content;

		$content = \GFCommon::replace_variables_prepopulate( $content ); // merge tags

		return '<div class="sous-accroche">'.$content.'</div>';
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

	public function get_value_save_entry( $value, $form, $input_name, $lead_id, $lead ) {
		return '';
	}
}