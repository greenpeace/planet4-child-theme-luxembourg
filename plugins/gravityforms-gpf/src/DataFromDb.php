<?php



namespace Greenpeacefrance\Gravityforms;

class DataFromDb extends \GFAddOn {

	protected $_version = '0.1';
    protected $_min_gravityforms_version = '2.5';
    protected $_slug = 'greenpeace-fromdb';
    protected $_full_path = __FILE__;
    protected $_title = 'Extraction de données';
    protected $_short_title = 'Base de données';

	protected $_capabilities_settings_page = 'manage_options';
	protected $_capabilities_form_settings = 'edit_posts';
	protected $_capabilities_uninstall = 'manage_options';
	protected $_capabilities_plugin_page = 'manage_options';
	protected $_capabilities_app_menu = 'manage_options';
	protected $_capabilities_app_settings = 'manage_options';



    private static $_instance = null;

	public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new DataFromDb();
        }

        return self::$_instance;
    }




	public function init_frontend() {
        parent::init_frontend();
		add_filter( 'gform_form_post_get_meta', [$this, 'get_data_from_db'], 1, 1 );

		add_filter( 'gform_pre_render', [$this, 'pre_render'], 10, 3 );

		// add_action( 'gform_after_submission', [$this, 'after_submission'], 10, 2 );

		add_filter( 'gform_confirmation', [$this, 'confirmation'], 10, 4 );
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
		return 'dashicons-database';
	}


	public function form_settings_fields($form) {

        return [
			[
				'title' => 'Base de données',
				'fields' => [
					[
						'type' => 'text',
						'name' => 'tablename',
						'label' => 'Nom de la table. Ne peut pas contenir le préfixe WP',
						'description' => "Si un nom de table est absent, alors on a le comportement habituel de Gravity Forms. Si le nom est présent, on recherche systématiquement dans la table, même si aucun paramètre n'est passé dans l'URL (et dans ce cas on affichera directement le message \"En cas d'absence\" car rien ne pourra être trouvé.)",
						'validation_callback' => [$this, 'validate_tablename'],
					],
					[
						'type' => 'generic_map',
						'name' => 'mapping',
						'label' => 'Mapping des paramètres d\'URL vers la table',
						'description' => "",
						'key_field' => [
							'title' => 'Paramètre URL',
						],
						'value_field' => [
							'title' => 'Champ de la BdD',
							'placeholder' => 'Choisissez l\'option custom',
							'allow_custom' => true
						],
						'validation_callback' => [$this, 'validate_mapping'],
					],
				]
			],
			[
				'title' => 'Options',
				'fields' => [
					[
						'name' => 'only_one_submit',
						'type' => 'toggle',
						'label' => "Ne permettre qu'un seul submit par contact",
						'description' => 'Ajouté un champs "is_done tinyint(1)" à la table. Les submits suivants seront marqués comme Spam.',
						'default_value' => "0",
					],
				]
			],
			[
				'title' => 'Messages',
				'fields' => [
					[
						'type' => 'textarea',
						'name' => 'message_if_missing',
						'label' => 'Affiché à la place du formulaire si rien n\'est trouvé en BdD.',
						'class' => 'large',
						'use_editor' => true,
					],
					[
						'type' => 'textarea',
						'name' => 'message_if_more_than_one',
						'label' => 'Affiché lors de la sauvegarde, si on a déjà participé au formulaire.',
						'class' => 'large',
						'use_editor' => true,
					],
				]
			],


		];
	}



	public function validate_tablename($field, $value) {
		$match = preg_match('/[^a-zA-Z0-9_]/', $value);

		$error = false;


		if ($match) {
			$error = true;
		}

		global $wpdb;
		$prefix =$wpdb->prefix;

		if ( substr( $value, 0, strlen($prefix) ) === $prefix) {
			$error = true;
		}

		if ($error) {
			$this->set_field_error($field, 'Le nom rentré est invalide.');
		}


	}


	public function validate_mapping($field) {
		$settings = $this->get_posted_settings();

		$mappings = $settings['mapping'];

		if ( empty($mappings) ) {
			return;
		}

		foreach ($mappings as $mapping) {
			$url_param = $mapping['custom_key'];
			$table_field = $mapping['custom_value'];
			$match = preg_match('/[^a-zA-Z0-9_]/', $url_param . $table_field);

			if ($match) {
				$this->set_field_error($field, 'Un des noms rentré est invalide.');
			}
 		}
	}



	public function get_data_from_db($form) {

		$model = ModelFromDb::getInstance();


		$settings = $this->get_form_settings( $form );


		$tablename = $settings['tablename'] ?? "";

		if ( ! $tablename) {
			return $form;
		}

		$model->is_valid = true;

		$mapping = $settings['mapping'] ?? [];


		$where = [];
		$values = [];


		$source = $_GET;

		$method = rgget('REQUEST_METHOD', $_SERVER);

		if ($method === 'POST') {
			parse_str(
				( $_POST['_fd_qs'] ?? "" ),
				$source);
		}


		add_filter( 'gform_form_tag', function($string, $form) use ($source) {
			return $string .= '<input type="hidden" name="_fd_qs" value="'.esc_attr( http_build_query($source) ).'"/>';
		}, 10, 2);



		foreach ($mapping as $map) {

			$param = $map['custom_key'] ?? '';
			$field = $map['custom_value'] ?? "";

			$value = rgget($param, $source);

			$where[] = $field . ' = %s';
			$values[] = $value;
		}



		if ( strlen($tablename) && count($where) ) {
			global $wpdb;

			$model->tablename = $tablename;

			$sql = "select * from {$tablename} where ";
			$sql .= implode(' and ', $where);

			$results = $wpdb->get_results(
				$wpdb->prepare(
					$sql, $values
				)
			);

			if ( $results && count($results) ) {
				$model->set( $results[0] );
			}
		}

		return $form;
	}


	public function confirmation($confirmation, $form, $entry, $is_ajax) {

		$settings = $this->get_form_settings($form);

		$only_one_submit = $settings['only_one_submit'];

		if ($only_one_submit) {
			$model = ModelFromDb::getInstance();

			if ( ! $model->is_valid ) {
				return $confirmation;
			}

			if ( ! $model->data['is_done'] ) {
				global $wpdb;

				$wpdb->query(
					$wpdb->prepare(
						"update {$model->tablename} set is_done = 1 where id = %s",
						($model->data['id'] ?? 0)
					)
				);
			}
			else {
				\GFFormsModel::update_entry_property( $entry['id'], 'status', 'spam', false, true );
				// ou \GFFormsModel::set_entry_meta();
				return $settings['message_if_more_than_one'] ?? "";
			}
		}


		return $confirmation;
	}


	public function pre_render($form, $is_ajax, $field_values) {

		$model = ModelFromDb::getInstance();

		if ( ! $model->is_valid ) {
			return $form;
		}

		if ( count($model->data) ) {
			$data = $model->data;

			add_filter( 'gform_replace_merge_tags', function( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) use ( $data ) {

				if ( strpos( $text, '{bdd:' ) === false ) {
					return $text;
				}

				$text = preg_replace_callback('/{bdd:([^}]+)}/', function($matches) use ($data) {
					return $data[ $matches[1] ] ?? "";
				}, $text);

				return $text;
			}, 10, 7 );
		}
		else {

			$settings = $this->get_form_settings($form);

			// rien trouvé, on bloque le formulaire
			$missing_message = $settings['message_if_missing'];

			$html_field = new \GF_Field_HTML();
			$html_field->id = 1;
			$html_field->formId = $form['id'];
			$html_field->content = $missing_message;

			$form['fields'] = [
				$html_field,
			];

			add_filter( 'gform_submit_button', function() {
				return '';
			});
		}

		return $form;
	}



}
