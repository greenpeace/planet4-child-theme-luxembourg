<?php



namespace Greenpeacefrance\Gravityforms;

class Crm extends \GFAddOn {

	protected $_version = '0.2';
    protected $_min_gravityforms_version = '2.5';
    protected $_slug = 'greenpeace-crm';
    protected $_full_path = __FILE__;
    protected $_title = 'Configuration Greenpeace';
    protected $_short_title = 'Greenpeace';

	protected $_capabilities_settings_page = 'manage_options';
	protected $_capabilities_form_settings = 'edit_posts';
	protected $_capabilities_uninstall = 'manage_options';
	protected $_capabilities_plugin_page = 'manage_options';
	protected $_capabilities_app_menu = 'manage_options';
	protected $_capabilities_app_settings = 'manage_options';



    private static $_instance = null;

	public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new Crm();
        }

        return self::$_instance;
    }

    // public function init() {
    //     parent::init();
    // }



	public function scripts() {
		if (is_admin()) {
			return parent::scripts();
		}

		return [];
	}

	public function styles() {
		if (is_admin()) {
			return parent::styles();
		}

		return [];
	}

	public function get_menu_icon() {
		return 'dashicons-id-alt';
	}


	public function plugin_settings_fields() {

		return [
			[
				'title' => 'Greenpeace',
				'description' => 'Configuration globale de Gravity Forms pour Greenpeace Fralux.',
				'fields'      => [
					[
						'type' => 'radio',
						'name' => 'nro',
						'label' => 'NRO',
						'default_value' => 'fr',
						'description' => 'Utilisé pour, par exemple, Country Of Ownership. Peut être modifié au niveau d\'une pétition.',
						'choices' => [
							[
								'label' => 'France',
								'value' => 'fr',
							],
							[
								'label' => 'Luxembourg',
								'value' => 'lu',
							]
						],
					],

				],
			],
		];


	}



	public function form_settings_fields($form) {

		$nro = $this->get_plugin_setting( 'nro');

		$journeys = [
			'fr' => [
				"APIEvent-2da7a81e-817a-f35e-0a0d-a9e82a85c377" => "FR_Event_Toutes_petitions_et_formulaires_de_telechargement_GP",
				"APIEvent-9e827b4a-10f5-c0f9-189d-c1e62d699125" => "FR_Event_Mardis_Verts",
				"APIEvent-d902bc5c-1025-0acf-7e56-7b21541a7f92" => "FR_Event_Formulaires_de_telechargements_specifiques",
			],

			'lu' => [
				"APIEvent-ccf251f7-f26b-736b-77ad-6c83cf1da93f" => "LUX_Event_Toutes_petitions_et_formulaires_de_telechargement_GP",
			]
		];

		$journey_list = array_map(function($event_key, $label) {
			return [
				"label" => $label . ' (' . $event_key . ')',
				"value" => $event_key,
			];
			},
			array_keys( $journeys[ $nro ] ),
			array_values( $journeys[ $nro ] )
		);


        return [
			[
				'title' => 'CRM',
				'fields' => [
					[
						'type' => 'text',
						'name' => 'sf_campaign_id',
						'label' => 'Campaign ID Salesforce',
					],
					[
						'type' => 'text',
						'name' => 'source_reference',
						'label' => 'ID Form',
						'description' => "Identifiant clair et compréhensible utilisé pour filtrer les parcours dans MC. Si vous le modifiez, vous devez répercuter le changement dans le parcours MC. Texte libre, max 200 caractères.",
					],
					[
						'type' => 'select_custom',
						'name' => 'mc_journey_id_new',
						'label' => 'ID du parcours Marketing Cloud pour les nouveaux contacts (Event Key / Journey Id)',
						'choices' => $journey_list,
					],
					[
						'type' => 'select_custom',
						'name' => 'mc_journey_id_existing',
						'label' => 'ID du parcours Marketing Cloud pour les contacts existants (Event Key / Journey Id)',
						'choices' => $journey_list,
					],

				]
			],

			[
				'title' => 'Localisation',
				'fields' => [
					[
						'type' => 'radio',
						'name' => 'nro',
						'label' => 'NRO',
						'description' => 'Utilisé pour, par exemple, Country Of Ownership.',
						'default_value' => $nro ?? 'fr',
						'choices' => [
							[
								'label' => 'France',
								'value' => 'fr',
							],
							[
								'label' => 'Luxembourg',
								'value' => 'lu',
							]
							],
							// 'onChange' => 'nroChanged(this)'
						],
					[
						'type' => 'select',
						'label' => 'Langue',
						'name' => 'lang',
						'default_value' => 'fr',
						'choices' => [
							[
								'label' => 'Français',
								'value' => 'French',
							],
							[
								'label' => 'Luxembourgeois',
								'value' => 'Luxembourgish',
							],
							[
								'label' => 'Allemand',
								'value' => 'German',
							],
							[
								'label' => 'Anglais',
								'value' => 'English',
							],
							[
								'label' => 'Flamand',
								'value' => 'Flemmish',
							],
							[
								'label' => 'Néerlandais',
								'value' => 'Dutch',
							]
						]
					],
				]
			],


			[
				'title' => 'Avancé',
				'fields' => [
					[
						'type' => 'text',
						'name' => 'api_reference_prefix',
						'label' => 'Préfixe de l\'API Reference',
						'description' => "Uniquement des lettres majuscules. <b>&Agrave; laisser vide sauf si besoin particulier.</b>",
					],
					[
						'type' => 'text',
						'name' => 'transformer',
						'label' => 'Transformation',
						'description' => 'Classe de POGO\\Services\\Transformers gérant la transformation des données de formulaires en données SF/MC. <b>&Agrave; laisser vide sauf si besoin particulier.</b>',
					],
				]
			],

		];
	}





}
