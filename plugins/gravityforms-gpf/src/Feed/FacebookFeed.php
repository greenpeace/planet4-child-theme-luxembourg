<?php


namespace Greenpeacefrance\Gravityforms\Feed;

class FacebookFeed extends \GFFeedAddOn {

	protected $_version = '0.2';
    protected $_min_gravityforms_version = '2.5';
    protected $_slug = 'greenpeace-facebook';
    protected $_full_path = __FILE__;
    protected $_title = 'Configuration de l\'API Conversion de Facebook';
    protected $_short_title = 'API Conversion FB';

    private static $_instance = null;

	//protected $_capabilities_settings_page = 'manage_options';
	protected $_capabilities_form_settings = 'edit_posts';
	protected $_capabilities_uninstall = 'manage_options';

	public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new FacebookFeed();
        }

        return self::$_instance;
    }

    // public function init() {
    //     parent::init();
    // }


	public function get_menu_icon() {
		return 'dashicons-star-filled';
	}



	public function plugin_settings_fields() {

        return [
			[
				'title' => "Configuration",
				'fields' => [
					[
						'type' => 'text',
						'name' => 'token',
						'label' => 'Token',
						'description' => "",
					],
					[
						'type' => 'text',
						'name' => 'pixel_id',
						'label' => 'Pixel ID',
						'description' => "",
					],
					[
						'type' => 'text',
						'name' => 'api_version',
						'label' => 'Version de l\'api',
						'description' => "",
					],
				],
			],

		];
	}


	public function feed_settings_fields() {

		return [
			[
				'title' => 'Envoi des données vers Facebook (API Conversion',
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
				'description' => "",
				'fields' => [
					[
						'type' => 'generic_map',
						'name' => 'mapping',
						'key_field' => [
							'title' => 'Variable Facebook',
						],
						'value_field' => [
							'title' => 'Champ du formulaire ou valeur',
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


		$has_facebook_session = empty( $_SESSION['facebook_api_conversion'] ) ? false : true;


//		$this->log_debug( __METHOD__ . "(): FB en session ? : " .  intval($has_facebook_session) );


		if ( ! $has_facebook_session ) {
			return;
		}

		$plugin_settings = $this->get_plugin_settings();

		$entry_id = $entry['id'];
		$form_id = $form['id'];

		$event_id = $entry_id . '.' . $form_id;

		$token = $plugin_settings[ 'token' ];
		$pixel_id = $plugin_settings[ 'pixel_id' ];
		$api_version = $plugin_settings[ 'api_version' ];

		$s = $_SESSION['facebook_api_conversion'];

		$fbc = implode('.', [
			$s['version'],
			$s['subdomainIndex'],
			$s['creationTime'],
			$s['fbclid']
		]);

		$fbp = implode('.', [
			$s['version'],
			$s['subdomainIndex'],
			$s['creationTime'],
			$s['randomNumber']
		]);

		$objet = 	[
			"event_name" => "Lead",
			"event_time" => time(),
			"event_id" => $event_id,
			"event_source_url" => $entry['source_url'],
			"action_source" => "website",
		];

		$user_data = [
			"client_user_agent" => $entry['user_agent'],
			"client_ip_address" => $entry['ip'],
			"fbc" => $fbc,
			"fbp" => $fbp,
		];

		$mapping = $feed['meta']['mapping'];
		$algo = 'sha256';

		foreach ($mapping as $map) {
			$key = trim( $map['custom_key'] ) ;

			if ( $map['value'] === 'gf_custom' ) {
				$value = trim( $map['custom_value'] );
			}
			else {
				$value = trim( $entry[ $map['value'] ] );
			}

			$user_data[ $key ] = hash($algo, $value);
		}

		$objet['user_data'] = $user_data;

		$data = [$objet];

		$url = "https://graph.facebook.com/{$api_version}/{$pixel_id}/events"; // ?access_token={$token}";


		$fields = [
			'access_token' => $token,
			//'data' => json_encode( [ $objet ] ),
			'data' => [ $objet ],
		];

		$args = [
			'method' => 'POST',
			'body' => $fields,
			'headers' => [
				//'Content-Type' => 'multipart/form-data',
				'Content-Type' => 'application/json',
			]
		];


		//error_log('FACEBOOK ' . json_encode($args));

		$response = wp_remote_request($url, $args);


		if ( is_wp_error( $response ) ){

			$note_message = sprintf(
				'Facebook API Conversion error : %s (%d)',
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
			$this->add_note($entry_id, 'Facebook API Conversion SUCCESS', 'success');
		}

		return $entry;
	}


}
