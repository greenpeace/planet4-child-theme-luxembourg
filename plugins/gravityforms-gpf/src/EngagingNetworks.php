<?php


namespace Greenpeacefrance\Gravityforms;


class EngagingNetworks extends \GFAddOn {

	protected $_version = '0.2';
    protected $_min_gravityforms_version = '2.5';
    protected $_slug = 'greenpeace-en';
    protected $_full_path = __FILE__;
    protected $_title = 'Engaging Networks';
    protected $_short_title = 'Engaging Networks';


	protected $_capabilities_form_settings = 'edit_posts';

    private static $_instance = null;

	public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new EngagingNetworks();
        }

        return self::$_instance;
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


	public function get_menu_icon() {
		return 'dashicons-networking';
	}



	public function init() {
		parent::init();

		// add_filter( 'gform_confirmation', [ $this, 'send_to_en' ], 10, 4 );
		add_action( 'gform_entry_created', [ $this, 'send_to_en' ], 10, 2 );

	}



	public function plugin_settings_fields() {

        return [
			[
				'title' => 'Configuration',
				'fields' => [
					[
						'type' => 'radio',
						'name' => 'nro',
						'label' => 'NRO',
						'default_value' => 'fr',
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
					[
						'type' => 'text',
						'name' => 'public_token',
						'label' => 'Token public Engaging Networks',
					],
					[
						'type' => 'text',
						'name' => 'dbname',
						'label' => 'Database Name des tokens',
					],
					[
						'type' => 'text',
						'name' => 'dbtable',
						'label' => 'Table Name des tokens',
					],


				]
			]
		];
	}


	public function form_settings_fields( $form ) {

        return [
			[
				'title' => 'Engaging Networks',
				'fields' => [
					[
						'type' => 'text',
						'name' => 'page_id',
						'label' => 'Page ID Engaging Networks',
					],
					[
						'type' => 'text',
						'name' => 'optin_nouveau',
						'label' => 'Opt-in pour Nouveau contact',
					],
					[
						'type' => 'textarea',
						'name' => 'js_filter_contact',
						'label' => 'Fonction JS de modification du contact avant envoi.',
						'description' => "On y écrit que le corps de la fonction. La fonction n'a qu'un argument, <code>contact</code>. La fonction DOIT retourner un objet de contact EN. Exemple : <br/><pre>
return {
	'txn1': '...', // utm_* de 1 à 5: source, medium, campaign, content, term
	...
	'txn6': campaign_id_salesforce,
	'txn7': 'Web Lead',
	'supporter': {
		'first_name': '...',
		'last_name': '...',
		'email': '',
		'questions': {
			'email_ok': 'Y',
		}
	}
};
</pre>",
						'class' => 'large',
					],
				]
			],
			[
				'title' => 'Mappings',
				'fields' => [
					[
						'type' => 'generic_map',
						'name' => 'mapping',
						'label' => 'Les attributs du contact',
						'key_field' => [
							'title' => 'Champ EN',
						],
						'value_field' => [
							'title' => 'Champ du formulaire ou valeur',
						],
					],

					[
						'type' => 'generic_map',
						'name' => 'mapping_questions',
						'label' => 'Les questions et opt-ins',
						'key_field' => [
							'title' => 'Question EN',
						],
						'value_field' => [
							'title' => 'Champ du formulaire ou valeur',
						],
					],
				]
			],

		];
	}


	protected function is_valid_db_field($value) {
		return boolval( preg_match("/^[a-zA-Z_]+$/", $value) );
	}


	// avec le hook gform_confirmation :
	// public function send_to_en( $confirmation, $form, $entry, $ajax ) {

	// avec le hook gform_entry_created :
	public function send_to_en( $entry, $form, $confirmation = "" ) {

		// wp_mail('hugo.poncedeleon@greenpeace.org', 'EN', print_r($entry, true) );

		$config = $form['greenpeace-en'] ?? false;
		$plugin_settings = $this->get_plugin_settings();


		if ( ! $config) {
			return $confirmation;
		}

		$page_id = $config['page_id'] ?? "";

		if ( ! $page_id) {
			return $confirmation;
		}



		$sf_campaign_id = $entry['sf_campaign_id'] ?? "";

		$en_data = [
			"txn6" => $sf_campaign_id,
			"txn7" => "Web Lead",
		];


		$en_data['txn1'] = $entry['utm_source'] ?? "";
		$en_data['txn2'] = $entry['utm_medium'] ?? "";
		$en_data['txn3'] = $entry['utm_campaign'] ?? "";
		$en_data['txn4'] = $entry['utm_content'] ?? "";
		$en_data['txn5'] = $entry['utm_term'] ?? "";


		$supporter = [];


		// On récupère les données du supporter

		$mapping = $config['mapping'];

		foreach ($mapping as $map) {
			$key = trim( $map['custom_key'] ) ;

			if ( $map['value'] === 'gf_custom' ) {
				$value = trim( $map['custom_value'] );
			}
			else {
				$value = trim( $entry[ $map['value'] ] );
			}

			$supporter[ $key ] = $value;
		}



		// Et les opt-ins

		$mapping = $config['mapping_questions'];

		foreach ($mapping as $map) {
			$key = trim( $map['custom_key'] ) ;

			if ( $map['value'] === 'gf_custom' ) {
				$value = trim( $map['custom_value'] );
			}
			else {
				$value = trim( $entry[ $map['value'] ] );
			}

			if ( ! isset($supporter['questions'])) {
				$supporter['questions'] = [];
			}

			$supporter['questions'][ $key ] = $value;
		}


		$en_data["supporter"] = $supporter;





		// envoi vers EN
		// On provoque l'envoi depuis la page de confirmation. C'est pas top,
		// mais je n'ai rien d'autre

		// Récupération du token
		$dbname = trim( $plugin_settings['dbname'] );
		$tablename = trim( $plugin_settings['dbtable'] );

		$auth_token = '';

		$nro = $plugin_settings['nro'];
		$public_token = preg_replace( '/[^a-fA-F0-9-]/', '', $plugin_settings['public_token'] );


		$url = 'https://e-activist.com/ens/service/page/' . $page_id . '/process';

		$optin_nouveau = $config['optin_nouveau'] ?? false;

		if ( $this->is_valid_db_field($dbname) && $this->is_valid_db_field($tablename) ) {

			$sql = 'select token from '.$dbname.'.'.$tablename.' where nro = %s';

			global $wpdb;

			$auth_token = $wpdb->get_var( $wpdb->prepare( $sql, $nro ) );

		}



		if ( ! $auth_token) {
			return $confirmation;
		}


		$filter_js = trim($config['js_filter_contact']);

		if ($filter_js === "") {
			$filter_js = 'return contact';
		}

		// les fonctions JS
		$functions = <<<END

function ENContactExists(email, callback) {

	var emailExists = false
		jQuery.ajax({
			"url": "https://e-activist.com/ea-dataservice/data.service",
			"type": "GET",
			"jsonp": "callback",
			"dataType": "jsonp",
			"data": {
				"service": "SupporterData",
				"token": "{$public_token}",
				"contentType": "json",
				"email": email,
				"resultType": "summary"
			}
		}).done(function (data) {
			var ee = ""

			if (data && data.rows && data.rows.length &&
				data.rows[0].columns && data.rows[0].columns.length &&
				data.rows[0].columns[2] && data.rows[0].columns[2].value) {

				ee = data.rows[0].columns[2].value;
			}

			if (ee === "Y") {
				emailExists = true
			}


		}).always(function () {
			callback(emailExists);
		});
}



function signatureEN(url, supporter, token) {
	jQuery.ajax({
		method: 'post',
		url: url,
		data: JSON.stringify(supporter),
		dataType: 'json',
		headers: {
			"Content-Type": "application/json",
			"ens-auth-token": token
		}
	});
}

function filterContact(contact) {
{$filter_js}
}
END;

		if ($optin_nouveau) {
			$script = <<<END
ENContactExists(contact.supporter.email, function (exists) {
	if (!exists) {
		if (!contact.supporter.questions) {
			contact.supporter.questions = {}
		}
		contact.supporter.questions['{$optin_nouveau}'] = 'Y'
	}
	signatureEN('{$url}', contact, '{$auth_token}')
})
END;
		}
		else {
			$script = <<<END
signatureEN('{$url}', contact, '{$auth_token}')
END;
		}


		$json = json_encode($en_data);

		$all_scripts = <<<END
<script async="async">
jQuery(function($) {
{$functions}
var contact = filterContact({$json});
$script
})
</script>
END;

		// wp_add_inline_script( 'jquery', $all_scripts);

		add_action( 'wp_footer', function() use ( $all_scripts ) {
			echo $all_scripts;
		}, 20);

		// return $all_scripts . $confirmation;

	}


}
