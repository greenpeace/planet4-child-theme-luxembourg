<?php


namespace Greenpeacefrance\Gravityforms;


class CssJs extends \GFAddOn {

	protected $_version = '0.1';
    protected $_min_gravityforms_version = '2.5';
    protected $_slug = 'css-js';
    protected $_full_path = __FILE__;
    protected $_title = 'CSS & JS';
    protected $_short_title = 'CSS & JS';


	protected $_capabilities_form_settings = 'edit_posts';

    private static $_instance = null;

	public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new CssJs();
        }

        return self::$_instance;
    }



	public function get_menu_icon() {
		return 'dashicons-art';
	}



	public function init() {
		parent::init();

		add_action( 'gform_enqueue_scripts', [$this, 'enqueue_assets'], 10, 2 );

		add_action( 'gfiframe_head', function($form_id, $form) {

			if ( empty( $form['css-js']) ) {
				return;
			}

			$inline_css = trim( $form['css-js']['inline_css'] ?? "" );

			if ( $inline_css ) {
				echo '<style type="text/css">';
				echo $inline_css;
				echo '</style>';
			}
		}, 999, 2);
	}



	public function enqueue_assets($form, $is_ajax) {


		if ( empty( $form['css-js']) ) {
			return;
		}


		$css_files = explode( "\n", trim( $form['css-js']['css_files'] ?? "") );

		foreach ($css_files as $index => $css_file) {
			$css_file = trim( $css_file );
			if ($css_file) {
				// on est dans <head> alors c'est plus simple de faire des print
				echo '<link rel="stylesheet" type="text/css" href="'.$css_file.'"/>';
				// wp_enqueue_style( 'gpfgf-css-' . $index, $css_file );
			}
		}


		$js_files = explode( "\n", trim( $form['css-js']['js_files'] ?? "") );

		foreach ($js_files as $index => $js_file) {
			$js_file = trim( $js_file );
			if ($js_file) {
				// on enqueue dans le footer
				wp_enqueue_script( 'gpfgf-js-' . $index, $js_file, ['gpfgf-default-script'], null, true );

			}
		}


		$inline_css = trim( $form['css-js']['inline_css'] ?? "" );

		if ( $inline_css ) {
			add_action( 'wp_head', function() use ( $inline_css ) {
				echo '<style type="text/css">';
				echo $inline_css;
				echo '</style>';
			}, 999);
		}



		$inline_js = trim( $form['css-js']['inline_js'] ?? "" );

		if ( $inline_js ) {
			add_action( 'wp_footer', function() use ( $inline_js ) {
				echo '<script>';
				echo $inline_js;
				echo '</script>';
			}, 9999);
		}





		$inline_js_confirm = trim( $form['css-js']['inline_js_confirm'] ?? "" );

		if ( $inline_js_confirm ) {
			add_action( 'wp_footer', function() use ( $inline_js_confirm ) {
				echo '<script> jQuery(document).on("gform_confirmation_loaded", function(event, formId) {' . PHP_EOL;
				echo $inline_js_confirm;
				echo PHP_EOL . '})</script>';
			}, 9999);
		}




		// l'appel à cette méthode est doublée ...
		remove_action( 'gform_enqueue_scripts', [$this, 'enqueue_assets'], 10 );
	}



	public function form_settings_fields( $form ) {

        return [
			[
				'title' => 'CSS',
				'fields' => [
					[
						'type' => 'textarea',
						'name' => 'inline_css',
						'label' => 'CSS à intégrer dans le formulaire',
						'description' => "Des variables CSS peuvent être utilisées dans <code>:root</code> : --form-button-bgcolor, --form-button-fgcolor, --form-placeholder-color, --form-label-color",
						'class' => 'large',
					],
					[
						'type' => 'textarea',
						'name' => 'css_files',
						'label' => 'Fichiers CSS à appeler avec le formulaire. Un fichier par ligne.',
						'description' => "",
						'class' => 'large',
					],
				]
			],
			[
				'title' => 'Javascript',
				'fields' => [
					[
						'type' => 'textarea',
						'name' => 'inline_js',
						'label' => 'Javascript à intégrer dans le formulaire',
						'description' => "",
						'class' => 'large',
					],
					[
						'type' => 'textarea',
						'name' => 'js_files',
						'label' => 'Fichiers JS à appeler avec le formulaire. Un fichier par ligne.',
						'description' => "",
						'class' => 'large',
					],

					[
						'type' => 'textarea',
						'name' => 'inline_js_confirm',
						'label' => 'Javascript à intégrer sur le message de confirmation',
						'description' => "",
						'class' => 'large',
					],
				]
			],

		];
	}





}
