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
		return 'dashicons-art';
	}


	public function render($form_string, $form) {

		if ( empty( $form['css-js']) ) {
			return $form_string;
		}

		$css_string = "";

		$inline_css = trim( $form['css-js']['inline_css'] ?? "" );

		if ( $inline_css ) {
			$css_string = <<< END
			<style type="text/css">
			$inline_css
			</style>
			END;
		}

		return $css_string . $form_string;
	}


	public function init() {
		parent::init();


		add_filter( 'gform_get_form_filter', [ $this, 'render' ], 9999, 2 );

		add_filter( 'gform_get_form_confirmation_filter', [ $this, 'render' ], 9999, 2 );


		add_action( 'gform_enqueue_scripts', [$this, 'enqueue_assets'], 10, 2 );

		add_action( 'gform_footer_init_scripts_filter', function($form_string, $form) {

			$return_script = '';

			$inline_js = trim( $form['css-js']['inline_js'] ?? "" );

			if ( $inline_js ) {
				$return_script .= '<script>(function(formId) {'
					. $inline_js
					. '})('.$form['id'].')</script>';

			}


			$inline_js_confirm = trim( $form['css-js']['inline_js_confirm'] ?? "" );

			if ( $inline_js_confirm ) {

				$return_script .= '<script> jQuery(document).on("gform_confirmation_loaded", function(event, formId) {'
					. $inline_js_confirm
					. '})</script>';

			}

			return $form_string . $return_script;

		}, 9999, 2);








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
				wp_enqueue_style( 'gpfgf-css-' . $index, $css_file );
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
						'label' => 'Javascript à intégrer dans le formulaire. Ce code est dans une IIFE dans laquelle passe le paramètre formId.',
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
						'label' => 'Javascript à intégrer sur le message de confirmation. Ce code est dans l\'événement "gform_confirmation_loaded" dans lequel passe le paramètre formId. Ne fonctionne que sur les formulaires en Ajax.',
						'description' => "",
						'class' => 'large',
					],
				]
			],

		];
	}





}
