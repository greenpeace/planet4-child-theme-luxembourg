<?php


namespace Greenpeacefrance\Gravityforms;


class Folders extends \GFAddOn {

	protected $_version = '0.1';
    protected $_min_gravityforms_version = '2.5';
    protected $_slug = 'greenpeace-folders';
    protected $_full_path = __FILE__;
    protected $_title = 'Dossiers';
    protected $_short_title = 'Dossiers';


	protected $_capabilities_form_settings = 'edit_posts';
	protected $_capabilities_uninstall = 'manage_options';
    private static $_instance = null;

	public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new Folders();
        }

        return self::$_instance;
    }




	public function get_menu_icon() {
		return 'dashicons-open-folder';
	}



	public function init() {
		parent::init();

		add_filter('gform_form_list_forms', [$this, 'list_forms'], 10, 6);

		add_filter( "views_toplevel_page_gf_edit_forms", [$this, 'show_folder_buttons'], 10, 1 );

		add_filter( 'gform_form_list_columns', [$this, 'folder_column'], 10, 1 );

		// add_filter('manage_toplevel_page_gf_edit_forms_columns', [$this, 'manage_columns']);

		add_action('gform_form_list_column_folders', function($item) {
			echo $item->folders ?? "";
		}, 10, 1);
	}

	public function manage_columns($columns) {

		return $columns;
	}

	public function plugin_settings_fields() {

		return [
			[
				'title' => 'Dossiers',
				'fields'      => [
					[
						'type' => 'textarea',
						'name' => 'folders',
						'label' => '',
						'description' => "Mettre un nom par ligne.",
						'class' => 'large',
					],
				]
			]
		];
	}


	public function form_settings_fields( $form ) {

		$folders = array_filter(
			explode("\n", $this->get_plugin_setting( 'folders' ) ),
			function($f) {
				$f = trim($f);
				return strlen($f);
			});


		$folders_values = array_map(function($folder) {
				$folder = trim($folder);

				return [
					'label' => $folder,
					'name' => $folder,
					'default_value' => 0,
				];
			},
			$folders
		);


        return [

			[
				'title' => 'Dossier',
				'fields' => [
					[
						'type' => 'checkbox',
						'name' => '_folders',
						'label' => 'Sélectionnez',
						'choices' => $folders_values,
					],

				]
			],

		];
	}


	public function folder_column($columns) {
		$columns['folders'] = 'Dossiers';
		return $columns;
	}


	public function show_folder_buttons($views) {

		$list = $this->get_plugin_setting( 'folders' );
		$folders = explode("\n", $list );

		$folders = array_filter( $folders, function($f) {
			$f = trim($f);
			return strlen($f);
		});


		$selected_folder = "";

		if ( ! empty($_GET['gp_folder'])) {
			$selected_folder = $_GET['gp_folder'];
		}



		if (count($folders)) {

			$title = 'Dossiers...';

			$params = $_GET;
			$params['gp_folder'] = "";


			$buttons = '<a href="?' . http_build_query( $params ).'" class="btn-folder"><i>Tous</i></a>';


			foreach ($folders as $folder) {
				$folder = trim($folder);

				if ( strlen($folder)) {

					if ($selected_folder === $folder) {
						$title .= ' <span class="gp-form-folders-selected">&gt; '.$folder.'</span>';
					}

					$params['gp_folder'] = urlencode( $folder );
					$link = http_build_query( $params );

					$buttons .= '<a href="?'.$link.'" class="btn-folder">'.$folder.'</a>';
				}
			}



			echo '<div class="gp-form-folders"><details><summary>'.$title.'</summary><div class="gp-form-folder-list">';
			echo $buttons;
			echo '</div></details></div>';
		}


		return $views;
	}



	public function list_forms($forms, $search_query, $active, $sort_column, $sort_direction, $trash) {

		$folder = "";

		if ( ! empty($_GET['gp_folder'])) {
			$folder = $_GET['gp_folder'];
		}

		$filtered_forms = [];

		foreach ($forms as $form) {
			$f = \GFAPI::get_form( $form->id );

			$folders = [];

			if ( ! empty($f['greenpeace-folders']) ) {
				$folders = [];
				foreach ($f['greenpeace-folders'] as $key => $actif) {
					if ($actif === '1') {
						$folders[] = $key;
					}
				}
			}

			// if (count($folders)) {
			// 	$form->title .= ' ['.implode(', ', $folders).']';
			// }

			$form->folders = implode(', ', $folders);

			if ($folder) {
				$folder =urldecode( $folder );

				if ( in_array($folder, $folders) ) {
					$filtered_forms[] = $form;
				}
			}
			else {
				$filtered_forms[] = $form;
			}

		}

		return $filtered_forms;
	}

}
