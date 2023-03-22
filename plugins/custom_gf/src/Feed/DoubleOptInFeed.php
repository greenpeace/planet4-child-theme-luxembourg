<?php


namespace Greenpeacefrance\Gravityforms\Feed;

class DoubleOptInFeed extends \GFFeedAddOn {

	protected $_version = '0.1';
    protected $_min_gravityforms_version = '2.5';
    protected $_slug = 'greenpeace-double-optin';
    protected $_full_path = __FILE__;
    protected $_title = 'Double Opt-in';
    protected $_short_title = 'Double Opt-in';


	protected $_capabilities_settings_page = 'manage_options';
	protected $_capabilities_form_settings = 'edit_posts';
	protected $_capabilities_uninstall = 'manage_options';


    private static $_instance = null;

	public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new DoubleOptInFeed();
        }

        return self::$_instance;
    }




	public function init() {
		parent::init();

		add_filter( 'gform_custom_merge_tags', [ $this, 'merge_tags' ] );
		add_filter( 'gform_pre_replace_merge_tags', [ $this, 'replace_merge_tags' ], 10, 7 );

	}



	public function get_menu_icon() {
		return 'dashicons-update';
	}



	public function feed_settings_fields() {
		return [
			[
				'title' => 'Email double opt-in',
				'fields' => [
					[
						'label' => 'Objet de l\'email',
						'type' => 'text',
						'name' => 'email_subject',
						'required' => true,
					],
					[
						'label' => 'Contenu de l\'email',
						'type' => 'textarea',
						'name' => 'email_body',
						'required' => true,
						'use_editor' => true,
					],
					[
						'label' => 'Message de confirmation',
						'type' => 'textarea',
						'name' => 'confirmation_message',
						'required' => true,
						'use_editor' => true,
					],
				]
			],
			[
				'title' => 'Mapping',
				'description' => 'Nécessaire pour avoir l\'adresse email',
				'fields' => [
					[
						'type' => 'field_map',
						'name' => 'mapping',
						'field_map' => [
							[
								'name' => 'email',
								'label' => 'Adresse e-mail',
								'required' => true,
								'field_type' => ['email', 'hidden'],
								'default_value' => $this->get_first_field_by_type( 'email' ),
							]
						],
					],
				]
			],

		];
	}




	public function can_duplicate_feed( $id ) {
		return true;
	}


	public function is_asynchronous( $feed, $entry, $form ) {
		return false;
	}






	public function process_feed( $feed, $entry, $form ) {


		$string = "";
		foreach ($entry as $k => $v) {
			$string .= $k . $v;
		}

		$uuid_array = str_split( $string, 1 );

		shuffle($uuid_array);

		$uuid_string = join('', $uuid_array);

		$uuid = self::guidv4( $uuid_string );

		gform_update_meta( $entry['id'], 'double_optin_uuid', $uuid, $form['id']);
		$entry['double_optin_uuid'] = $uuid;


		gform_update_meta( $entry['id'], 'email_confirmed', 0, $form['id']);
		$entry['email_confirmed'] = 0;




		$email_subject = rgars( $feed, 'meta/email_subject' );
		$email_body = rgars( $feed, 'meta/email_body' );

		$subject = \GFCommon::replace_variables( $email_subject, $form, $entry, false, true, false);
		$body = \GFCommon::replace_variables( $email_body, $form, $entry, false, true, false);


		$mapping = $this->get_field_map_fields( $feed, 'mapping' );

		$email = trim( $entry[ $mapping['email'] ] );

		wp_mail( $email, $subject, $body );


		$data = [
			'email' => $email,
			'subject' => $subject,
			'body' => $body,
			'entry' => $entry,
			'form' => $form,
			'feed' => $feed,
		];


wp_mail('hugo.poncedeleon@greenpeace.org', 'double optin feed', print_r($data, true) );


		return $entry;
	}


	public function feed_list_columns() {
		return [
			'email_subject' => 'Email',
		];
	}


	public function merge_tags( $merge_tags ) {
		return array_merge($merge_tags, [
			[
				'label' => 'Double Opt-in Link',
				'tag' => '{email_confirmation_link}',
 			],

		]);

	}



	public function replace_merge_tags( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {

		if ( empty( $text) ) {
			return $text;
		}

		$link = parse_url( $entry['source_url'] );

		if (! $link ) {
			// Bizarre, on abandonne
			return $text;
		}


		parse_str( $link['query'], $query );


		$query['_do_cid'] = $entry['double_optin_uuid'];
		$query['_do_fid'] = $form['id'];
		$query['_do_eid'] = $entry['id'];

		$link['query'] = http_build_query( $query );

		$url = self::unparse_url($link);

		return str_replace( '{email_confirmation_link}', $url, $text );

	}

	public static function guidv4($data = null) {

		$data = $data ?? random_bytes(16);
		assert(strlen($data) == 16);

		$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80);

		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}


	public static function unparse_url($parsed_url) {
		$scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
		$host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
		$port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
		$user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
		$pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
		$pass     = ($user || $pass) ? "$pass@" : '';
		$path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
		$query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
		$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

		return $scheme . $user . $pass . $host . $port . $path . $query . $fragment;
	}
}
