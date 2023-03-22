<?php

namespace Greenpeacefrance\Gravityforms\Field;



class JaugeField extends \GF_Field_Text {

	public $type = 'gp_jauge';


	public function get_form_editor_field_title() {
		return esc_attr( 'Jauge' );
	}


	public function get_form_editor_button() {
		return [
			'group' => 'greenpeace_fields',
			'text'  => $this->get_form_editor_field_title(),
			'icon' => 'gpfgf-jauge',
		];
	}

	public function get_form_editor_field_description() {
		return esc_attr( 'Jauge customisable' );
	}

	public function is_conditional_logic_supported() {
		return true;
	}


	public function allow_html(){
    	return true;
	}


	public function get_value_save_entry($value, $form, $input_name, $lead_id, $lead) {
		return '';
	}

	public function get_form_editor_field_settings() {
		return [
			'conditional_logic_field_setting',
			'label_setting',
			'default_value_setting',
			'placeholder_setting',
			'description_setting',
			'css_class_setting',
			'gp_jauge_start_setting',
			'gp_jauge_target_setting',
			'gp_jauge_min_setting',
			'gp_jauge_text_setting',
			'gp_jauge_fgcolor_setting',
			'gp_jauge_bgcolor_setting',
		];
	}


	public function get_form_inline_script_on_page_render( $form ) {
		$id = "input_{$form['id']}_{$this->id}";
		return ;
		return <<<END
(function() {
	var input = document.getElementById("$id");
	var keepNumbers = false;

	if (input) {
		input.addEventListener('keypress', function(e) {
			e.preventDefault();
			gpfCleanInput(e.charCode, e.target, 80, keepNumbers);
			e.target.value = e.target.value.toUpperCase();
		});
	}
})();
END;
	}


	public function get_field_input($form, $value = '', $entry = null) {
		$form_id         = $form['id'];
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();
		$id              = (int) $this->id;

		if ($is_entry_detail || $is_form_editor) {
			return '<div class="gf-html-container"><span class="gf_blockheader"><i class="fa fa-code fa-lg"></i> Jauge </span><span>Configuration dans les settings</span></div>';
		}


		$count = \GFAPI::count_entries( $form->id );

		$min = intval($this->jauge_minimum);

		$number = intval($this->jauge_start) + $count;



		if ($number < $min) {
			return '';
		}

		$objectif = max( 1, intval($this->jauge_objectif) );

		$percent = min( 100, floor( $number / $objectif * 100 ) );

		// $data = [
		// 	'count' => $count,
		// 	'number' => $number,
		// 	'objectif' => $objectif,
		// 	'percent' => $percent,
		// 	'this' => $this,
		// ];

		// wp_mail('hugo.poncedeleon@greenpeace.org', 'jauge', print_r($data, true));


		$fgcolor = 'var(--primary-color)';
		$bgcolor = 'transparent';

		if ( ! empty($this->jauge_fgcolor) ) {
			$fgcolor = esc_attr($this->jauge_fgcolor);
		}

		if ( ! empty($this->jauge_bgcolor) ) {
			$bgcolor = esc_attr($this->jauge_bgcolor);
		}

		$id = 'progress-' . uniqid();

		$text = sprintf($this->jauge_text, "<span class=\"petition-counter\">{$count}</span>");


		return <<< "JAUGE"
		<div class="fc-wprogress">
			<div class="petition-progress show" id="{$id}">
				<div class="progress-item">
					<span>{$text}</span>
				</div>
				<div class="progress-bar" style="background-color:{$bgcolor}">
					<div class="progress-clip" style="background-color:{$fgcolor}"></div>
				</div>
			</div>
		</div>

		<script>
		jQuery(document).on('gform_post_render', function() {
			const jauge = document.querySelector("#{$id} .progress-clip");
			jauge.style.width = "{$percent}%";
		});
		</script>
		JAUGE;

	}


	public function get_field_content( $value, $force_frontend_label, $form ) {
		return "{FIELD}";
	}
}