<?php

namespace Greenpeacefrance\Gravityforms\Field;

class DirtyHtmlField extends \GF_Field_HTML {

	public $type = 'gp_html';

	public $label = 'Dirty HTML';

	public $displayOnly = true;


	public function get_form_editor_field_title() {
		return esc_attr( $this->label );
	}


	public function get_form_editor_field_description() {
		return esc_attr( 'HTML non nettoyé (attention).' );
	}


	public function get_form_editor_button() {
		return [
			'group' => 'advanced_fields',
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
			return '<div class="gf-html-container"><span class="gf_blockheader"><i class="fa fa-code fa-lg"></i> HTML "sale" </span><span>Contenu HTML dans les settings</span></div>';

		}


		$content = $this->content;

		$content = \GFCommon::replace_variables_prepopulate( $content ); // merge tags

		if( isset($GLOBALS['wp_embed']) ) {
			// adds support for the [embed] shortcode
			$content = $GLOBALS['wp_embed']->run_shortcode( $content );
		}


		$content = do_shortcode( $content );

		return $content;
	}

	// public function get_field_content( $value, $force_frontend_label, $form ) {
	// 	return '{FIELD}';
	// }

	// public function get_field_container( $atts, $form ) {

	// 	$is_entry_detail = $this->is_entry_detail();
	// 	$is_form_editor  = $this->is_form_editor();

	// 	if ($is_entry_detail || $is_form_editor) {
	// 		return parent::get_field_container( $atts, $form );
	// 	}
	// 	return '{FIELD_CONTENT}';
	// }

	public function do_shortcode( $content ){

		if( isset($GLOBALS['wp_embed']) ) {
			// adds support for the [embed] shortcode
			$content = $GLOBALS['wp_embed']->run_shortcode( $content );
		}
		// executes all other shortcodes
		$content = do_shortcode( $content );

		return $content;
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