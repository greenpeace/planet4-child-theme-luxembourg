<?php


namespace Greenpeacefrance\Gravityforms\Feed;

class TestFeed extends \GFFeedAddOn {

	protected $_version = '0.1';
    protected $_min_gravityforms_version = '2.5';
    protected $_slug = 'greenpeace-test';
    protected $_full_path = __FILE__;
    protected $_title = 'Test';
    protected $_short_title = 'Test';


	protected $_capabilities_settings_page = 'manage_options';
	protected $_capabilities_form_settings = 'edit_posts';
	protected $_capabilities_uninstall = 'manage_options';


    private static $_instance = null;

	public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new TestFeed();
        }

        return self::$_instance;
    }



	public function get_menu_icon() {
		return 'dashicons-carrot';
	}



	public function feed_settings_fields() {
		return [
			[
				'title' => 'Test',
				'fields' => [
					[
						'label' => 'Nom',
						'type' => 'text',
						'name' => 'name',
						'required' => true,
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


		$data = [
			'entry' => $entry,
			'form' => $form,
			'feed' => $feed,
		];


wp_mail('hugo.poncedeleon@greenpeace.org', 'test feed', print_r($data, true) );


		return $entry;
	}


}
