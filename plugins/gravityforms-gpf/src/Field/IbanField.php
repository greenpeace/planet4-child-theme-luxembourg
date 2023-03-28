<?php

namespace Greenpeacefrance\Gravityforms\Field;



class IbanField extends \GF_Field_Text {

	public $type = 'gp_iban';

	public $label = 'IBAN';

	public function get_form_editor_field_title() {
		return esc_attr( $this->label );
	}


	public function get_form_editor_button() {
		return [
			'group' => 'pricing_fields',
			'text'  => $this->get_form_editor_field_title(),
			'icon' => 'gpfgf-iban',
		];
	}

	public function get_form_editor_field_description() {
		return esc_attr( 'Champ pour numéro IBAN' );
	}

	public function is_conditional_logic_supported() {
		return true;
	}


	public function allow_html(){
    	return false;
	}


	public function get_value_save_entry($value, $form, $input_name, $lead_id, $lead) {
		$value = strtoupper( trim($value) );
		if ( strlen( $value ) > 8) {
			$debut = substr($value, 0, 8);
		}

		if (strlen($value) > 10) {
			$fin = substr($value, -2);
		}

		return $debut . '...' . $fin;
	}

	public function get_value_entry_detail($value, $currency = '', $use_text = false, $format = 'html', $media = 'screen') {
		$value = strtoupper( trim($value) );
		if ( strlen( $value ) > 8) {
			$debut = substr($value, 0, 8);
		}

		if (strlen($value) > 10) {
			$fin = substr($value, -2);
		}

		return $debut . '...' . $fin;
	}


	public function validate($value, $form) {

		$value = trim( $value );

		wp_mail('hugo.poncedeleon@greenpeace.org', 'iban 1', print_r($value, true) );

		if ( strlen( $value ) < 2) {
			$this->failed_validation = true;
			$this->validation_message = __( "IBAN invalide.", "gravityforms" );
			wp_mail('hugo.poncedeleon@greenpeace.org', 'iban 2', print_r($this, true) );
			return;
		}


		$value = strtoupper( $value );


		$country = substr($value, 0, 2);

		switch ($country) {
			case "AL": $regex = "\d{8}[\dA-Z]{16}"; break;
			case "AD": $regex = "\d{8}[\dA-Z]{12}"; break;
			case "AT": $regex = "\d{16}"; break;
			case "AZ": $regex = "[\dA-Z]{4}\d{20}"; break;
			case "BE": $regex = "\d{12}"; break;
			case "BH": $regex = "[A-Z]{4}[\dA-Z]{14}"; break;
			case "BA": $regex = "\d{16}"; break;
			case "BR": $regex = "\d{23}[A-Z][\dA-Z]"; break;
			case "BG": $regex = "[A-Z]{4}\d{6}[\dA-Z]{8}"; break;
			case "CR": $regex = "\d{17}"; break;
			case "HR": $regex = "\d{17}"; break;
			case "CY": $regex = "\d{8}[\dA-Z]{16}"; break;
			case "CZ": $regex = "\d{20}"; break;
			case "DK": $regex = "\d{14}"; break;
			case "DO": $regex = "[A-Z]{4}\d{20}"; break;
			case "EE": $regex = "\d{16}"; break;
			case "FO": $regex = "\d{14}"; break;
			case "FI": $regex = "\d{14}"; break;
			case "FR": $regex = "\d{10}[\dA-Z]{11}\d{2}"; break;
			case "GE": $regex = "[\dA-Z]{2}\d{16}"; break;
			case "DE": $regex = "\d{18}"; break;
			case "GI": $regex = "[A-Z]{4}[\dA-Z]{15}"; break;
			case "GR": $regex = "\d{7}[\dA-Z]{16}"; break;
			case "GL": $regex = "\d{14}"; break;
			case "GT": $regex = "[\dA-Z]{4}[\dA-Z]{20}"; break;
			case "HU": $regex = "\d{24}"; break;
			case "IS": $regex = "\d{22}"; break;
			case "IE": $regex = "[\dA-Z]{4}\d{14}"; break;
			case "IL": $regex = "\d{19}"; break;
			case "IT": $regex = "[A-Z]\d{10}[\dA-Z]{12}"; break;
			case "KZ": $regex = "\d{3}[\dA-Z]{13}"; break;
			case "KW": $regex = "[A-Z]{4}[\dA-Z]{22}"; break;
			case "LV": $regex = "[A-Z]{4}[\dA-Z]{13}"; break;
			case "LB": $regex = "\d{4}[\dA-Z]{20}"; break;
			case "LI": $regex = "\d{5}[\dA-Z]{12}"; break;
			case "LT": $regex = "\d{16}"; break;
			case "LU": $regex = "\d{3}[\dA-Z]{13}"; break;
			case "MK": $regex = "\d{3}[\dA-Z]{10}\d{2}"; break;
			case "MT": $regex = "[A-Z]{4}\d{5}[\dA-Z]{18}"; break;
			case "MR": $regex = "\d{23}"; break;
			case "MU": $regex = "[A-Z]{4}\d{19}[A-Z]{3}"; break;
			case "MC": $regex = "\d{10}[\dA-Z]{11}\d{2}"; break;
			case "MD": $regex = "[\dA-Z]{2}\d{18}"; break;
			case "ME": $regex = "\d{18}"; break;
			case "NL": $regex = "[A-Z]{4}\d{10}"; break;
			case "NO": $regex = "\d{11}"; break;
			case "PK": $regex = "[\dA-Z]{4}\d{16}"; break;
			case "PS": $regex = "[\dA-Z]{4}\d{21}"; break;
			case "PL": $regex = "\d{24}"; break;
			case "PT": $regex = "\d{21}"; break;
			case "RO": $regex = "[A-Z]{4}[\dA-Z]{16}"; break;
			case "SM": $regex = "[A-Z]\d{10}[\dA-Z]{12}"; break;
			case "SA": $regex = "\d{2}[\dA-Z]{18}"; break;
			case "RS": $regex = "\d{18}"; break;
			case "SK": $regex = "\d{20}"; break;
			case "SI": $regex = "\d{15}"; break;
			case "ES": $regex = "\d{20}"; break;
			case "SE": $regex = "\d{20}"; break;
			case "CH": $regex = "\d{5}[\dA-Z]{12}"; break;
			case "TN": $regex = "\d{20}"; break;
			case "TR": $regex = "\d{5}[\dA-Z]{17}"; break;
			case "AE": $regex = "\d{3}\d{16}"; break;
			case "GB": $regex = "[A-Z]{4}\d{14}"; break;
			case "VG": $regex = "[\dA-Z]{4}\d{16}"; break;
			default:
				$this->failed_validation = true;
				$this->validation_message = __( "IBAN invalide.", "gravityforms" );
				wp_mail('hugo.poncedeleon@greenpeace.org', 'iban 3', print_r($this, true) );
				return;
		}


		if ( preg_match('/'.$regex.'/', $value) ) {
			$this->failed_validation = false;
		}
		else {
			$this->failed_validation = true;
			$this->validation_message = __( "IBAN invalide.", "gravityforms" );
		}

		wp_mail('hugo.poncedeleon@greenpeace.org', 'iban 4', print_r($this, true) );
	}

}