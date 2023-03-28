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
						'choices' => [
							// [
							// 	'label' => 'FR Don',
							// 	'value' => 'APIEvent-e7d79a8b-0bc7-bff8-0a5a-bab51ebee41a',
							// ],
							[
								'label' => 'FR Pétition - APIEvent-9c4988d2-fff2-5bde-4cb2-496e41649013',
								'value' => 'APIEvent-9c4988d2-fff2-5bde-4cb2-496e41649013',
							],
							// [
							// 	'label' => 'LU Don',
							// 	'value' => 'APIEvent-17ce0fc1-4d88-d93b-4bfe-da5ec5748c5c',
							// ],
							[
								'label' => 'LU Pétition - APIEvent-17ce0fc1-4d88-d93b-4bfe-da5ec5748c5c',
								'value' => 'APIEvent-17ce0fc1-4d88-d93b-4bfe-da5ec5748c5c',
							],
						]
					],
					[
						'type' => 'select_custom',
						'name' => 'mc_journey_id_existing',
						'label' => 'ID du parcours Marketing Cloud pour les contacts existants (Event Key / Journey Id)',
						'choices' => [
							// [
							// 	'label' => 'FR Don',
							// 	'value' => 'APIEvent-bde38f77-357f-dabf-102e-aa12588004ea',
							// ],
							[
								'label' => 'FR Pétition - APIEvent-9c4988d2-fff2-5bde-4cb2-496e41649013',
								'value' => 'APIEvent-9c4988d2-fff2-5bde-4cb2-496e41649013',
							],
							// [
							// 	'label' => 'LU Don',
							// 	'value' => 'APIEvent-17ce0fc1-4d88-d93b-4bfe-da5ec5748c5c',
							// ],
							[
								'label' => 'LU Pétition - APIEvent-17ce0fc1-4d88-d93b-4bfe-da5ec5748c5c',
								'value' => 'APIEvent-17ce0fc1-4d88-d93b-4bfe-da5ec5748c5c',
							],
						]
					],
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


		];
	}





}
