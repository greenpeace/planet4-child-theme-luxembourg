<?php


namespace Greenpeacefrance\Gravityforms\Feed;

class Web2caseFeed extends \GFFeedAddOn {

	protected $_version = '0.1';
    protected $_min_gravityforms_version = '2.5';
    protected $_slug = 'greenpeace-web2case';
    protected $_full_path = __FILE__;
    protected $_title = 'Web2case Salesforce';
    protected $_short_title = 'Web2Case';


	protected $_capabilities_settings_page = 'manage_options';
	protected $_capabilities_form_settings = 'edit_posts';
	protected $_capabilities_uninstall = 'manage_options';


    private static $_instance = null;

	public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new Web2caseFeed();
        }

        return self::$_instance;
    }



	public function get_menu_icon() {
		return 'dashicons-admin-comments';
	}


	public function plugin_settings_fields() {
		return [
			[
				'title' => 'Salesforce',
				'fields' => [
					[
						'label' => 'Org ID',
						'type' => 'text',
						'name' => 'orgid',
						'required' => true,
					],
					[
						'label' => 'URL de service Web2Case',
						'type' => 'text',
						'name' => 'web2case_url',
						'default_value' => 'https://webto.salesforce.com/servlet/servlet.WebToCase?encoding=UTF-8',
						// 'default_value' => 'https://www.greenpeace.fr/functions/salesforce/web2case',
						'required' => true,
					],
				],
			],
		];
	}

	public function feed_settings_fields() {
		return [
			[
				'title' => 'Web2case Salesforce',
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
				'title' => 'Champ Description',
				'fields' => [
					[
						'label' => 'Message envoyé dans le Case. Les Merge Tags de Gravity Forms sont disponibles (voir bouton "Merge Tags" en haut)',
						'type' => 'textarea',
						'name' => 'message',
						'required' => true,
						'use_editor' => true,
						// 'class' => 'large',
					],
				],
			],
			[
				'title' => 'Mapping et valeurs par défaut',
				'fields' => [
					[
						'name' => 'mapping',
						'type' => 'generic_map',
						'key_field' => [
							'title' => 'Champ SF',
						],
						'value_field' => [
							'title' => 'Champ du formulaire ou valeur',
							'allow_custom' => true,
						],
					],
				],
			],
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
			'name' => "Case",
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

	public function process_feed( $feed, $entry, $form ) {

		$entry_id = $entry['id'];
		$form_id = $form['id'];

		$plugin_settings = $this->get_plugin_settings();

		// $mapping = $this->get_generic_map_fields( $feed, 'mapping' );

		$mapping = $feed['meta']['mapping'];

		$case_data = [];

		foreach ($mapping as $map) {
			$sf_key = trim( $map['custom_key'] ) ;

			if ( $map['value'] === 'gf_custom' ) {
				$value = \GFCommon::replace_variables( trim( $map['custom_value'] ), $form, $entry, false, false, false  );
			}
			else {
				$value = trim( $entry[ $map['value'] ] );
			}

			$case_data[ $sf_key ] = $value;
		}


		$description = trim( rgars( $feed, 'meta/message' ) );
		$description = \GFCommon::replace_variables( $description, $form, $entry, false, false, false );
		$description = strip_tags( $description );
		$description = html_entity_decode( $description );
		$description = str_replace( '&#039;', "'", $description );

		$case_data['description'] = $description;
		$case_data['retURL'] = 'https://greenpeace.fr/';
		$case_data['orgid'] = $plugin_settings['orgid']; // get_option('gpf_sf_orgid_prod');

		// array_walk( $case_data, "html_entity_decode" );

		// $data = [
		// 	'case' => $case_data,
		// 	'entry' => $entry,
		// 	'feed' => $feed,
		// 	'form' => $form,
		// ];



		// $this->log_debug('case data : ' . print_r($data, true));

// wp_mail('hugo.poncedeleon@greenpeace.org', 'web2case', print_r($data, true) );


		// $request_url = 'https://www.greenpeace.fr/functions/salesforce/web2case';

		$request_url = $plugin_settings['web2case_url'];

		$request_args = [
			'method' => 'POST',
			'body' => http_build_query($case_data),
		];


		$response = wp_remote_request( $request_url, $request_args );

		// $this->log_debug('reponse SF : ' . print_r($response, true));
		// wp_mail('hugo.poncedeleon@greenpeace.org', 'réponse', print_r($response, true) );

		// Log error or success based on response.
		if ( is_wp_error( $response ) ) {

			$note_message = sprintf(
				'Web2Case error : %s (%d)',
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

		}
		else {
			$note_message = "Envoi du WebCase correctement effectué.";
			$this->add_note($entry_id, $note_message );


			// $this->log_debug( sprintf( '%s(): Web2case envoyé. code: %s; body: %s', __METHOD__, wp_remote_retrieve_response_code( $response ), wp_remote_retrieve_body( $response ) ) );
		}

		return $entry;
	}


}
