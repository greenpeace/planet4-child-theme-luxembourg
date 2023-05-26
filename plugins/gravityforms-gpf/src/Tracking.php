<?php


namespace Greenpeacefrance\Gravityforms;


class Tracking extends \GFAddOn {

	protected $_version = '0.2';
    protected $_min_gravityforms_version = '2.5';
    protected $_slug = 'greenpeace-tracking';
    protected $_full_path = __FILE__;
    protected $_title = 'Tracking';
    protected $_short_title = 'Tracking';


	protected $_capabilities_form_settings = 'edit_posts';

    private static $_instance = null;

	public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new Tracking();
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
		return 'dashicons-chart-area';
	}



	public function init() {
		parent::init();

		add_filter( 'gform_entry_detail_meta_boxes', [ $this, 'register_meta_box' ], 10, 3 );

		add_filter( 'gform_confirmation', [ $this, 'send_datalayer_events' ], 10, 4 );

		add_filter( 'gform_custom_merge_tags', [ $this, 'merge_tags' ] );
		add_filter( 'gform_pre_replace_merge_tags', [ $this, 'replace_merge_tags' ], 10, 7 );

		add_filter( 'gform_entry_post_save', [ $this, 'entry_post_save' ], 10, 2 );

	}


	public function entry_post_save( $entry, $form ) {

		$entry_id = $entry['id'];
		$form_id = $form['id'];

		// On récupère les UTM
		parse_str( parse_url( $entry['source_url'], PHP_URL_QUERY), $query );

		$utm_params = [
			'utm_campaign',
			'utm_content',
			'utm_term',
			'utm_source',
			'utm_medium',
		];

		foreach ($utm_params as $utm) {
			$value = "";

			if ( isset( $query[ $utm ] ) ) {
				// on limite à 80 caractères max.
				$value = substr( sanitize_text_field( $query[ $utm ] ), 0, 80);
			}

			gform_update_meta( $entry_id, $utm, $value, $form_id );
			$entry[ $utm ] = $value;
		}

		// wp_mail('hugo.poncedeleon@greenpeace.org', 'entry_details', print_r($entry, true) );


		/*
		$sf_campaign_id = $query['sfdc'] ?: rgars( $form, 'greenpeace-crm/sf_campaign_id' );
		$sf_campaign_id = sanitize_text_field( $sf_campaign_id );
		gform_update_meta( $entry_id, 'sf_campaign_id', $sf_campaign_id, $form_id );
		$entry['sf_campaign_id'] = $sf_campaign_id;
		*/


		return $entry;

	}


	public function replace_merge_tags( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {

		if ( empty( $text) ) {
			return $text;
		}

		return str_replace( [
			'{utm_content}',
			'{utm_campaign}',
			'{utm_term}',
			'{utm_medium}',
			'{utm_source}',
		], [
			$entry['utm_content'],
			$entry['utm_campaign'],
			$entry['utm_term'],
			$entry['utm_medium'],
			$entry['utm_source'],
		],
		$text );

	}




	// fonction utile ?
	public function merge_tags( $merge_tags ) {
		return array_merge($merge_tags, [
			[
				'label' => 'utm_campaign',
				'tag' => '{utm_campaign}',
 			],
			[
				'label' => 'utm_content',
				'tag' => '{utm_content}',
 			],
			[
				'label' => 'utm_medium',
				'tag' => '{utm_medium}',
 			],
			[
				'label' => 'utm_source',
				'tag' => '{utm_source}',
 			],
			[
				'label' => 'utm_term',
				'tag' => '{utm_term}',
 			],
		]);

	}


	public function send_datalayer_events($confirmation, $form, $entry, $ajax) {

		$config = $form['greenpeace-tracking'] ?? false;

		if ( ! $config) {
			return $confirmation;
		}

		$event = [
			'event' => 'conversion',
			'eventCategory' => $config['event_category'] ?? "",
			'eventAction' => $config['event_action'] ?? "",
			'eventLabel' => $config['event_label'] ?? "",
		];


		return '<script>(function(){window.dataLayer=window.dataLayer||[];window.dataLayer.push('.json_encode($event).')})()</script>' . $confirmation;

	}



	public function register_meta_box($meta_boxes, $entry, $form ) {
		$meta_boxes[ $this->_slug ] = [
            'title'    => 'Greenpeace',
            'callback' => [ $this, 'add_details_meta_box' ],
            'context'  => 'normal',
		];

		return $meta_boxes;
	}



	public function add_details_meta_box( $args ) {
		$form  = $args['form'];
		$entry = $args['entry'];

		global $wpdb;

		// on récupère les metas en base, ici $entry ne les contient pas
		$metas = [];

		$table_name = \GFFormsModel::get_entry_meta_table_name();

        $results = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM {$table_name} WHERE entry_id=%d", $entry['id'] ) );


		foreach ($results as $result) {
			$metas[ $result->meta_key ] = $result->meta_value;
		}

		// wp_mail('hugo.poncedeleon@greenpeace.org', 'entry_details', print_r($metas, true) );

		$params = [
			'entry_reference' => 'API Reference',
			// 'sf_campaign_id' => 'Campaign ID Salesforce (codespec)',
			// 'codespec' => 'Codespec',
			'utm_campaign' => 'UTM_campaign',
			'utm_source' => 'UTM_source',
			'utm_medium' => 'UTM_medium',
			'utm_content' => 'UTM_content',
			'utm_term' => 'UTM_term',
		];

		$html = '<table>';





		foreach ($params as $key => $name) {
			// $value = rgar( $entry, $key );
			$value = $metas[ $key ];
			$html .= '<tr><td>' . $name . '</td><td>' . esc_html( $value ) . '</td></tr>';
		}

		$html .= '</table>';

		echo $html;
	}




	public function get_entry_meta($entry_meta, $form_id) {
		$entry_meta['entry_reference'] = [
			'label' => 'API Reference',
			'is_numeric' => false,
			'is_default_column' => true,
			'filter' => [
				'operators' => [ 'is', 'contains' ]
			],
		];

		return $entry_meta;
	}




	public function form_settings_fields( $form ) {

        return [
			[
				'title' => 'Tracking',
				'fields' => [
					[
						'type' => 'text',
						'name' => 'event_action',
						'label' => 'Event Action',
					],
					[
						'type' => 'text',
						'name' => 'event_category',
						'label' => 'Catégorie',
						'default_value' => "leads_acquisition",
						'description' => 'Par défaut : "leads_acquisition"',
					],
					[
						'type' => 'text',
						'name' => 'event_label',
						'label' => 'Label',
						'default_value' => "formulaire_complété",
						'description' => 'Par défaut : "formulaire_complété"',
					],
				]
			],

		];
	}

}
