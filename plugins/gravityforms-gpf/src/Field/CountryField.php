<?php

namespace Greenpeacefrance\Gravityforms\Field;

class CountryField extends \GF_Field_Select {

	public $type = 'gp_country';

	public $label = 'Pays';

	public $choices = [
		[
		'value' => 'France',
		'text' => 'France',
		],
		[
		'value' => 'Luxembourg',
		'text' => 'Luxembourg',
		],
		[
		'value' => 'Belgique',
		'text' => 'Belgique',
		],
		[
		'value' => 'Suisse',
		'text' => 'Suisse',
		],
		[
		'value' => 'Afrique du Sud',
		'text' => 'Afrique du Sud',
		],
		[
		'value' => 'Algerie',
		'text' => 'Algerie',
		],
		[
		'value' => 'Allemagne',
		'text' => 'Allemagne',
		],
		[
		'value' => 'Andorre',
		'text' => 'Andorre',
		],
		[
		'value' => 'Arabie Saoudite',
		'text' => 'Arabie Saoudite',
		],
		[
		'value' => 'Argentine',
		'text' => 'Argentine',
		],
		[
		'value' => 'Australie',
		'text' => 'Australie',
		],
		[
		'value' => 'Autriche',
		'text' => 'Autriche',
		],
		[
		'value' => 'Bahrain',
		'text' => 'Bahrain',
		],
		[
		'value' => 'Bangladesh',
		'text' => 'Bangladesh',
		],
		[
		'value' => 'Benin',
		'text' => 'Benin',
		],
		[
		'value' => 'Bolivie',
		'text' => 'Bolivie',
		],
		[
		'value' => 'Bosnie Herzegovine',
		'text' => 'Bosnie Herzegovine',
		],
		[
		'value' => 'Bresil',
		'text' => 'Bresil',
		],
		[
		'value' => 'Bulgarie',
		'text' => 'Bulgarie',
		],
		[
		'value' => 'Burkina Faso',
		'text' => 'Burkina Faso',
		],
		[
		'value' => 'Cambodge',
		'text' => 'Cambodge',
		],
		[
		'value' => 'Cameroun',
		'text' => 'Cameroun',
		],
		[
		'value' => 'Canada',
		'text' => 'Canada',
		],
		[
		'value' => 'Cap Vert',
		'text' => 'Cap Vert',
		],
		[
		'value' => 'Chili',
		'text' => 'Chili',
		],
		[
		'value' => 'Chine',
		'text' => 'Chine',
		],
		[
		'value' => 'Chypre',
		'text' => 'Chypre',
		],
		[
		'value' => 'Colombie',
		'text' => 'Colombie',
		],
		[
		'value' => 'Comores',
		'text' => 'Comores',
		],
		[
		'value' => 'Congo',
		'text' => 'Congo',
		],
		[
		'value' => 'Coree du Sud',
		'text' => 'Coree du Sud',
		],
		[
		'value' => 'Costa Rica',
		'text' => 'Costa Rica',
		],
		[
		'value' => 'Cote d\'Ivoire',
		'text' => 'Cote d\'Ivoire',
		],
		[
		'value' => 'Croatie',
		'text' => 'Croatie',
		],
		[
		'value' => 'Danemark',
		'text' => 'Danemark',
		],
		[
		'value' => 'Djibouti',
		'text' => 'Djibouti',
		],
		[
		'value' => 'Egypte',
		'text' => 'Egypte',
		],
		[
		'value' => 'Emirats Arabes Unis',
		'text' => 'Emirats Arabes Unis',
		],
		[
		'value' => 'Equateur',
		'text' => 'Equateur',
		],
		[
		'value' => 'Espagne',
		'text' => 'Espagne',
		],
		[
		'value' => 'Etats Unis d\'Amerique',
		'text' => 'Etats Unis d\'Amerique',
		],
		[
		'value' => 'Finlande',
		'text' => 'Finlande',
		],
		[
		'value' => 'Gabon',
		'text' => 'Gabon',
		],
		[
		'value' => 'Gambie',
		'text' => 'Gambie',
		],
		[
		'value' => 'Georgie',
		'text' => 'Georgie',
		],
		[
		'value' => 'Ghana',
		'text' => 'Ghana',
		],
		[
		'value' => 'Grece',
		'text' => 'Grece',
		],
		[
		'value' => 'Guatemala',
		'text' => 'Guatemala',
		],
		[
		'value' => 'Guyana',
		'text' => 'Guyana',
		],
		[
		'value' => 'Hongrie',
		'text' => 'Hongrie',
		],
		[
		'value' => 'Inde',
		'text' => 'Inde',
		],
		[
		'value' => 'Indonesie',
		'text' => 'Indonesie',
		],
		[
		'value' => 'Irlande',
		'text' => 'Irlande',
		],
		[
		'value' => 'Islande',
		'text' => 'Islande',
		],
		[
		'value' => 'Israel',
		'text' => 'Israel',
		],
		[
		'value' => 'Italie',
		'text' => 'Italie',
		],
		[
		'value' => 'Japon',
		'text' => 'Japon',
		],
		[
		'value' => 'Kenya',
		'text' => 'Kenya',
		],
		[
		'value' => 'Lettonie',
		'text' => 'Lettonie',
		],
		[
		'value' => 'Liban',
		'text' => 'Liban',
		],
		[
		'value' => 'Lithuanie',
		'text' => 'Lithuanie',
		],
		[
		'value' => 'Madagascar',
		'text' => 'Madagascar',
		],
		[
		'value' => 'Malaisie',
		'text' => 'Malaisie',
		],
		[
		'value' => 'Mali',
		'text' => 'Mali',
		],
		[
		'value' => 'Maroc',
		'text' => 'Maroc',
		],
		[
		'value' => 'Maurice',
		'text' => 'Maurice',
		],
		[
		'value' => 'Mauritanie',
		'text' => 'Mauritanie',
		],
		[
		'value' => 'Mexique',
		'text' => 'Mexique',
		],
		[
		'value' => 'Monaco',
		'text' => 'Monaco',
		],
		[
		'value' => 'Mozambique',
		'text' => 'Mozambique',
		],
		[
		'value' => 'Niger',
		'text' => 'Niger',
		],
		[
		'value' => 'Norvege',
		'text' => 'Norvege',
		],
		[
		'value' => 'Nouvelle Caledonie',
		'text' => 'Nouvelle Caledonie',
		],
		[
		'value' => 'Nouvelle Zelande',
		'text' => 'Nouvelle Zelande',
		],
		[
		'value' => 'Pakistan',
		'text' => 'Pakistan',
		],
		[
		'value' => 'Panama',
		'text' => 'Panama',
		],
		[
		'value' => 'Pays-Bas',
		'text' => 'Pays-Bas',
		],
		[
		'value' => 'Perou',
		'text' => 'Perou',
		],
		[
		'value' => 'Philippines',
		'text' => 'Philippines',
		],
		[
		'value' => 'Pologne',
		'text' => 'Pologne',
		],
		[
		'value' => 'Polynesie Francaise',
		'text' => 'Polynesie Francaise',
		],
		[
		'value' => 'Portugal',
		'text' => 'Portugal',
		],
		[
		'value' => 'Republique Centrafricaine',
		'text' => 'Republique Centrafricaine',
		],
		[
		'value' => 'Republique Democratique du Congo',
		'text' => 'Republique Democratique du Congo',
		],
		[
		'value' => 'Republique Dominicaine',
		'text' => 'Republique Dominicaine',
		],
		[
		'value' => 'Republique Tcheque',
		'text' => 'Republique Tcheque',
		],
		[
		'value' => 'Roumanie',
		'text' => 'Roumanie',
		],
		[
		'value' => 'Royaume Uni',
		'text' => 'Royaume Uni',
		],
		[
		'value' => 'Russie',
		'text' => 'Russie',
		],
		[
		'value' => 'Senegal',
		'text' => 'Senegal',
		],
		[
		'value' => 'Serbie',
		'text' => 'Serbie',
		],
		[
		'value' => 'Singapour',
		'text' => 'Singapour',
		],
		[
		'value' => 'Slovenie',
		'text' => 'Slovenie',
		],
		[
		'value' => 'Suede',
		'text' => 'Suede',
		],
		[
		'value' => 'Tanzanie',
		'text' => 'Tanzanie',
		],
		[
		'value' => 'Tchad',
		'text' => 'Tchad',
		],
		[
		'value' => 'Thailande',
		'text' => 'Thailande',
		],
		[
		'value' => 'Togo',
		'text' => 'Togo',
		],
		[
		'value' => 'Tunisie',
		'text' => 'Tunisie',
		],
		[
		'value' => 'Turquie',
		'text' => 'Turquie',
		],
		[
		'value' => 'Ukraine',
		'text' => 'Ukraine',
		],
		[
		'value' => 'Uruguay',
		'text' => 'Uruguay',
		],
		[
		'value' => 'Venezuela',
		'text' => 'Venezuela',
		],
		[
		'value' => 'Viet Nam',
		'text' => 'Viet Nam',
		],
		[
		'value' => 'Wallis et Futuna',
		'text' => 'Wallis et Futuna',
		],

	];


	public function get_form_editor_field_title() {
		return esc_attr( 'Pays' );
	}



	public function get_form_editor_field_description() {
		return esc_attr( 'Liste des pays adaptée à GP' );
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
		return $value;
	}

	public function get_value_save_entry($value, $form, $input_name, $lead_id, $lead) {
		return $value;
	}


}