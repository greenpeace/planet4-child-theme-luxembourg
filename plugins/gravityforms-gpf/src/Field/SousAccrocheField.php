<?php

namespace Greenpeacefrance\Gravityforms\Field;

class SousAccrocheField extends \GF_Field_HTML {

	public $type = 'gp_sousaccroche';

	public $label = 'Citation';

	public $displayOnly = true;


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
			return '<div class="gf-html-container"><span class="gf_blockheader"><i class="fa fa-code fa-lg"></i> Citation </span><span>Texte dans les settings</span></div>';

		}


		$content = trim( $this->content );

		$content = \GFCommon::replace_variables_prepopulate( $content ); // merge tags


		$chunks = explode(' ', $content);
		$chunks = array_reverse($chunks);
		$done = [];

		foreach ($chunks as $word) {
			$done[] = array_shift($chunks);

			if ( preg_match("/[a-zA-Z0-9]/", $word) ) {
				$debut = join(' ', array_reverse($chunks));
				$fin = join(' ', array_reverse($done));
				break;
			}
		}


		// $last_space = strrpos($content, " ");
		// $debut = substr( $content, 0, $last_space );
		// $fin = substr( $content, $last_space + 1 );


		return '<div class="sous-accroche">

		<span class="picto-quote-left">
			<svg x="0px" y="0px" viewBox="0 0 41 32"
			enable-background="new 0 0 41 32"
			xml:space="preserve"
			>
			<use href="#picto-quote"/>
			</svg>
			</span>
			'.$debut.' <span class="nobr">'.$fin.'
			<span class="picto-quote-right">
			<svg x="0px" y="0px" viewBox="0 0 41 32"
			enable-background="new 0 0 41 32"
			xml:space="preserve"
			>
			<use href="#picto-quote"/>
			</svg>
			</span>
			</span>
			</div>';
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