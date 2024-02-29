<?php


namespace Greenpeacefrance\Gravityforms\Feed;

class SfmcFeed extends \GFFeedAddOn {

	protected $_version = '0.2';
    protected $_min_gravityforms_version = '2.5';
    protected $_slug = 'greenpeace-sfmc';
    protected $_full_path = __FILE__;
    protected $_title = 'Configuration Marketing Cloud';
    protected $_short_title = 'Marketing Cloud';

    private static $_instance = null;

	//protected $_capabilities_settings_page = 'manage_options';
	protected $_capabilities_form_settings = 'edit_posts';
	protected $_capabilities_uninstall = 'manage_options';

	public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new SfmcFeed();
        }

        return self::$_instance;
    }

    // public function init() {
    //     parent::init();
    // }


	public function get_menu_icon() {
		return 'dashicons-star-filled';
	}



	public function get_entry_meta($entry_meta, $form_id) {
		$entry_meta['entry_reference'] = [
			'label' => 'API reference',
			'is_numeric' => false,
			'is_default_column' => true,
			'filter' => [
				'operators' => [ 'is', 'contains' ]
			],
		];

		$entry_meta['sf_campaign_id'] = [
			'label' => 'Campaign ID',
			'is_numeric' => false,
			'is_default_column' => false,
			'filter' => [
				'operators' => [ 'is', 'contains' ]
			],
		];


		$entry_meta['petition_id'] = [
			'label' => 'ID table petitions',
			'is_numeric' => true,
			'is_default_column' => false,
			'filter' => [
				'operators' => [ 'is' ]
			],
		];


		return $entry_meta;
	}



	public function plugin_settings_fields() {

        return [
			[
				'title' => "Configuration",
				'fields' => [
					[
						'type' => 'text',
						'name' => 'api_reference_prefix',
						'label' => 'Préfixe de l\'API Reference',
						'description' => "Uniquement des lettres majuscules.",
					],
				],
			],
		/*
			[
				'title' => 'Type de sauvegarde des données du Feed SF/MC',
				'fields' => [
					[
						'type' => 'radio',
						'name' => 'save_type',
						'default_value' => 'database',
						'choices' => [
							[
								'label' => 'Webhook',
								'value' => 'webhook',
							],
							[
								'label' => 'Base de données',
								'value' => 'database'
							]
						]
					]
				],
			],
*/
/*
			[
				'title' => 'Base de données',
				'description' => 'Table existante où sauvegarder les données.',
				'fields' => [
					[
						'type' => 'text',
						'name' => 'dbhost',
						'label' => 'Hostname',
					],
					[
						'type' => 'text',
						'name' => 'dbname',
						'label' => 'Database Name',
					],
					[
						'type' => 'text',
						'name' => 'dbtable',
						'label' => 'Table Name',
					],

				]
			],
*/
			[
				'title' => 'Webhook',
				'description' => 'Webhook recevant les pétitions.',
				'fields' => [
					[
						'type' => 'text',
						'name' => 'webhook_url',
						'label' => 'URL du Webhook',
					],
					[
						'type' => 'text',
						'name' => 'auth_token',
						'label' => 'Token d\'authentification',
					],

				]
			],

			[
				'title' => 'Alert Slack',
				'description' => 'URL du webhook Slack d\'alerte',
				'fields' => [
					[
						'type' => 'text',
						'name' => 'alert_url',
						'label' => 'URL du Webhook',
					],

				]
			],
		];
	}




	// protected function is_valid_db_host($value) {
	// 	return boolval( preg_match("/^[a-zA-Z0-9\.]+$/", $value) );
	// }

	// protected function is_valid_db_field($value) {
	// 	return boolval( preg_match("/^[a-zA-Z_]+$/", $value) );
	// }


	protected function is_valid_url($value) {
		return boolval( preg_match("#^https?://[a-zA-Z0-9:/_\.\?=-]+$#", $value) );
	}

	protected function is_valid_token($value) {
		return boolval( preg_match("/^[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}$/", $value) );
	}

	public function feed_settings_fields() {

		return [
			[
				'title' => 'Envoi des données vers Salesforce et Marketing Cloud',
				'fields' => [
					[
						'label' => 'Nom',
						'type' => 'text',
						'name' => 'name',
						'required' => true,
					],
				]
			],
			[
				'title' => 'Mapping',
				'description' => "<p>
				Il est nécessaire d'indiquer le mapping des champs du formulaire avec les champs Salesforce.
				</p>
				<p>
				Utiliser la nomenclature Salesforce pour retrouver le nom du champ.
				</p>
				<p>
				Toutes les valeurs configurées seront envoyées vers Salesforce. Il est possible d'indiquer des valeurs fixes (non liées à des champs du formulaire) avec la dernière option de la liste \"Add Custom Value\".
				</p>
				<p>
				Les champs salesforce de base sont : Email, Telephone, Title, First Name, Last Name, Adresse 3, Adresse 4, Ville, Code Postal, Pays.
				</p>
				<p>
				Il est également possible de mettre le nom API du champ Salesforce (s360aie__***).
				</p>
				",
				'fields' => [
					[
						'type' => 'generic_map',
						'name' => 'mapping',
						'key_field' => [
							'title' => 'Champ SF/MC',
						],
						'value_field' => [
							'title' => 'Champ du formulaire ou valeur',
						],
					],
				],
			],
			// [
			// 	'title' => 'Mapping Optins',
			// 	'description' => "",
			// 	'fields' => [
			// 		[
			// 			'type' => 'generic_map',
			// 			'name' => 'optin_mapping',
			// 			'key_field' => [
			// 				'title' => 'Champ SF/MC',
			// 			],
			// 			'value_field' => [
			// 				'title' => 'Champ du formulaire ou valeur',
			// 			],
			// 		],
			// 	],
			// ],
			[
				'fields' => [
					[
						'name' => 'conditions',
						'type' => 'feed_condition',
						'label' => 'Conditions logiques',
						'checkbox_label' => 'Activer la logique conditionnelle',
						'instructions' => 'Activer l\'envoi si...',
					],
				],
			],
		];
	}



	public function can_duplicate_feed( $id ) {
		return true;
	}



	public function is_asynchronous( $feed, $entry, $form ) {
		return false;
	}




	public function feed_list_columns() {
		return [
			'name' => 'Template de données',
		];
	}



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



	static public function alert($message, $url) {

        $slack_message = [
            'blocks' => [
                [
                    "type" => "section",
                    "text" => [
                        'type' => 'mrkdwn',
                        'text' => "*$message*"
                    ],
                ],
                [
                    'type' => 'context',
                    'elements' => [
                        [
                            'type' => 'mrkdwn',
                            'text' =>  "Section : *webform_vers_pogo*",
                        ]
                    ]
                ],
                [
                    'type' => 'divider',
                ]
            ],
        ];



        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url );
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER ,true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($slack_message) );
        curl_exec($curl);
        curl_close($curl);
	}




	public function process_feed( $feed, $entry, $form ) {

		$plugin_settings = $this->get_plugin_settings();

		$entry_id = $entry['id'];
		$form_id = $form['id'];
		$datetime = \DateTime::createFromFormat( 'Y-m-d H:i:s', $entry['date_created'], new \DateTimeZone("UTC") );
		$datetime->setTimezone(new \DateTimeZone("Europe/Paris"));


		$default_api_reference_prefix = $plugin_settings[ 'api_reference_prefix' ];
		$form_api_reference_prefix = rgars( $form, 'greenpeace-crm/api_reference_prefix' );

		$api_reference_prefix = $form_api_reference_prefix ?: $default_api_reference_prefix ?: 'XXX';

		// $data = [
		// 	'default' => $default_api_reference_prefix,
		// 	'form ref' => $form_api_reference_prefix,
		// 	'resultat' => $api_reference_prefix,
		// 	'plugin' => $plugin_settings,
		// 	'form' => $form,
		// ];
		// wp_mail('hugo.poncedeleon@greenpeace.org', 'feed sfmc', print_r($data, true) );


		// on crée l'API Reference
		$entry_reference = sprintf( '%s%s-%d', $api_reference_prefix, $datetime->format('Ymd'), $entry['id'] );
		gform_update_meta( $entry_id, 'entry_reference', $entry_reference, $form_id );
		$entry['entry_reference'] = $entry_reference;

		$sfmc_data = [];


		$mapping = $feed['meta']['mapping'];

		foreach ($mapping as $map) {
			$sf_key = trim( $map['custom_key'] ) ;

			if ( $map['value'] === 'gf_custom' ) {
				$value = \GFCommon::replace_variables( trim( $map['custom_value'] ), $form, $entry, false, false, true );
			}
			else {
				$value = trim( $entry[ $map['value'] ] );
			}

			$sfmc_data[ $sf_key ] = $value;
		}

		$utms = [
			'utm_campaign',
			'utm_content',
			'utm_term',
			'utm_source',
			'utm_medium',
		];

		foreach ($utms as $utm) {
			$value = "";
			if ( ! empty($entry[ $utm ] ) ) {
				$value = $entry[ $utm ];
			}

			$sfmc_data[ $utm ] = $value;
		}


		$lang = rgars( $form, 'greenpeace-crm/lang' );
		$nro = rgars( $form, 'greenpeace-crm/nro' );


		$transformer = trim (rgars( $form, 'greenpeace-crm/transformer' ) );

		$source_reference = trim( rgars( $form, 'greenpeace-crm/source_reference' ) );

		$journey_new = rgars( $form, 'greenpeace-crm/mc_journey_id_new_custom' );
		if ( $journey_new === "") {
			$journey_new = rgars( $form, 'greenpeace-crm/mc_journey_id_new' );
		}

		$journey_existing = rgars( $form, 'greenpeace-crm/mc_journey_id_existing_custom' );
		if ( $journey_existing === "") {
			$journey_existing = rgars( $form, 'greenpeace-crm/mc_journey_id_existing' );
		}


		// gform_update_meta( $entry_id, 'sf_campaign_id', $sf_campaign_id, $form_id );

		// $entry['sf_campaign_id'] = $sf_campaign_id;

		// $sf_campaign_id = $entry['sf_campaign_id'];
		$sf_campaign_id = rgars($form, 'greenpeace-crm/sf_campaign_id');

		$sfmc_data['campaign'] = $sf_campaign_id;


		$form_name = $form_id . ' - ' . $form['title'] . ' - ' . $feed['meta']['name'];

		global $wpdb;

		$values = [
			'created_at' => (new \DateTime( 'now', new \DateTimeZone("UTC") ))->format('Y-m-d H:i:s'),
			'api_reference' => $entry['entry_reference'],
			'email' => $sfmc_data['email'] ?? "",
			'json_data' => json_encode($sfmc_data),
			'journey_id_new' => $journey_new,
			'journey_id_existing' => $journey_existing,
			'nro' => $nro,
			'lang' => $lang,
			'campaign_id' => $sf_campaign_id,
			'optins' => '{}',
			// 'optins' => json_encode($optins),
			'signup_date' => $datetime->format('Y-m-d'),
			'signup_time' => $datetime->format('H:i:s'),
			'form_id' => $source_reference,
			'form_name' => $form_name,
			'entry_id' => $entry['id'],
			'source_url' => $entry['source_url'],
			'user_agent' => $entry['user_agent'],
			'user_ip' => $entry['ip'],
			'transformer' => $transformer,
		];


		$petition_id = 0;



		$webhook_url = trim( $plugin_settings['webhook_url'] );
		$auth_token = trim( $plugin_settings['auth_token'] );



		if ( $this->is_valid_url($webhook_url) && $this->is_valid_token($auth_token) ) {

			$args = [
				'body' => json_encode( $values ),
				'headers' => [
					'Authorization' => 'Bearer ' . $auth_token,
					'Content-Type' => 'application/json',
				]
			];

			$response = wp_remote_post($webhook_url, $args);


			// wp_mail('hugo.poncedeleon@greenpeace.org', 'feed sfmc', print_r([
			// 	'args' => $args,
			// 	'url' => $webhook_url
			// ], true) );

			if ( is_wp_error( $response ) ){

				$note_message = sprintf(
					'GP Webhook error : %s (%d)',
					$response->get_error_message(),
					$response->get_error_code()
				);


				$this->add_note($entry_id, $note_message, 'error');
				$this->add_feed_error(
					$note_message,
					$feed,
					$entry,
					$form
				);

				$alert_url = trim( $plugin_settings['alert_url'] );
				if ( ! empty($alert_url) ) {
					$this->alert($note_message, $alert_url);
				}

			}
			else {
				$petition_id = intval( $response['body'] );
				$this->add_note($entry_id, 'GP Webhook SUCCESS, ID : ' . $petition_id, 'success');
			}
		}

		gform_update_meta( $entry_id, 'petition_id', $petition_id, $form_id );

		$entry['petition_id'] = $petition_id;

		return $entry;
	}


}
