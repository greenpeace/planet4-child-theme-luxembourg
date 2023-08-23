<?php


namespace Greenpeacefrance\Gravityforms;


class Apparence extends \GFAddOn {

	protected $_version = '0.1';
    protected $_min_gravityforms_version = '2.5';
    protected $_slug = 'greenpeace-design';
    protected $_full_path = __FILE__;
    protected $_title = 'Apparence';
    protected $_short_title = 'Apparence';


	protected $_capabilities_form_settings = 'edit_posts';
	protected $_capabilities_uninstall = 'manage_options';
    private static $_instance = null;

	public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new Apparence();
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



	public function init() {
		parent::init();

		add_filter( 'gform_get_form_filter', [ $this, 'render' ], 10, 2 );

		add_filter( 'gform_get_form_confirmation_filter', [ $this, 'render' ], 10, 2 );

		// add_filter( 'gform_form_tag', [ $this, 'render' ], 10, 2 );
	}




	public function form_settings_fields( $form ) {

        return [


			[
				'title' => 'Bloc formulaire',
				'fields' => [
					// TODO: ces champs doivent aller dans le thème
					[
						'type' => 'text',
						'name' => 'form_text',
						'label' => 'Texte (noir)',
						'class' => 'large',
					],
					[
						'type' => 'text',
						'name' => 'form_background',
						'label' => 'Background (jaune)',
						'class' => 'large',
					],

					[
						'type' => 'text',
						'name' => 'input_label',
						'label' => 'Label des champs (noir)',
						'class' => 'large',
					],

					[
						'type' => 'text',
						'name' => 'input_placeholder',
						'label' => 'Placeholder des champs (gris)',
						'class' => 'large',
					],

				]
			],


			[
				'title' => 'CTA',
				'fields' => [
					[
						'type' => 'text',
						'name' => 'cta_background',
						'label' => 'Couleur du bouton (rouge)',
						'class' => 'large',
					],

					[
						'type' => 'text',
						'name' => 'cta_text',
						'label' => 'Couleur du texte (blanc)',
						'class' => 'large',
					],

					[
						'type' => 'text',
						'name' => 'cta_background_hover',
						'label' => 'Couleur du bouton en HOVER (blanc)',
						'class' => 'large',
					],

					[
						'type' => 'text',
						'name' => 'cta_text_hover',
						'label' => 'Couleur du texte en HOVER (rouge)',
						'class' => 'large',
					],

					[
						'type' => 'select',
						'name' => 'cta_picto',
						'label' => 'Picto du bouton',
						'choices' => [
							[
								'label' => 'Aucun',
								'value' => '',
							],
							[
								'label' => 'Crayon',
								'value' => 'pen',
							],
						]
					]
				],
			],

			[
				'title' => 'Barre de progression',
				'fields' => [

					[
						'type' => 'text',
						'name' => 'progress_foreground',
						'label' => 'Couleur de la barre (vert)',
						'class' => 'large',
					],

					[
						'type' => 'text',
						'name' => 'progress_background',
						'label' => 'Couleur de fond (blanc)',
						'class' => 'large',
					],


					[
						'type' => 'text',
						'name' => 'progress_counter',
						'label' => 'Couleur du chiffre (rouge)',
						'class' => 'large',
					],
				],
			],

			[
				'title' => 'Bouton de don',
				'description' => "Présent sur l'écran de confirmation (si écran custom).",
				'fields' => [
					[
						'type' => 'text',
						'name' => 'don_background',
						'label' => 'Couleur du bouton (vert)',
						'class' => 'large',
					],

					[
						'type' => 'text',
						'name' => 'don_text',
						'label' => 'Couleur du texte (blanc)',
						'class' => 'large',
					],

					[
						'type' => 'text',
						'name' => 'don_background_hover',
						'label' => 'Couleur du bouton en HOVER (transparent)',
						'class' => 'large',
					],

					[
						'type' => 'text',
						'name' => 'don_text_hover',
						'label' => 'Couleur du texte en HOVER (vert)',
						'class' => 'large',
					],
				],
			],

			[
				'title' => 'Bouton de pétition',
				'description' => "Présent sur l'écran de confirmation (si écran custom).",
				'fields' => [
					[
						'type' => 'text',
						'name' => 'petition_background',
						'label' => 'Couleur du bouton (transparent)',
						'class' => 'large',
					],

					[
						'type' => 'text',
						'name' => 'petition_text',
						'label' => 'Couleur du texte (rouge)',
						'class' => 'large',
					],

					[
						'type' => 'text',
						'name' => 'petition_background_hover',
						'label' => 'Couleur du bouton en HOVER (rouge)',
						'class' => 'large',
					],

					[
						'type' => 'text',
						'name' => 'petition_text_hover',
						'label' => 'Couleur du texte en HOVER (blanc)',
						'class' => 'large',
					],
				],
			],

			[
				'title' => 'Ecran de confirmation',
				'description' => 'Si écran custom.',
				'fields' => [
					[
						'type' => 'text',
						'name' => 'confirm_first_line',
						'label' => 'Couleur de la première ligne de chaque bloc (en gras) (rouge)',
						'class' => 'large',
					],

					[
						'type' => 'text',
						'name' => 'confirm_text',
						'label' => 'Couleur de la suite du texte de chaque bloc (noir)',
						'class' => 'large',
					],


					[
						'type' => 'text',
						'name' => 'confirm_block1_background',
						'label' => 'Couleur de fond du bloc en exergue (blanc)',
						'class' => 'large',
					],

					[
						'type' => 'text',
						'name' => 'confirm_block1_text',
						'label' => 'Couleur du texte du bloc en exergue (noir)',
						'class' => 'large',
					],

					[
						'type' => 'text',
						'name' => 'confirm_block1_first_line',
						'label' => 'Couleur de la première ligne (en gras) du bloc en exergue (rouge)',
						'class' => 'large',
					],
				],
			]


		];
	}



	public function render($form_string, $form) {

		$config = $form['greenpeace-design'] ?? false;

		if ( ! $config) {
			return '<div class="gpfgf_wrapper">'.$form_string.'</div>';
		}


		$rouge = "var(--secondary-color)";
		$vert = "var(--primary-color)";
		$blanc = "#fff";
		$gris = "#888";




		$properties = ['form_text', 'form_background', 'confirm_first_line','confirm_text','confirm_block1_background','confirm_block1_text','confirm_block1_first_line','input_label','input_placeholder','progress_foreground','progress_background','progress_counter','cta_background','cta_text','cta_background_hover','cta_text_hover','don_background','don_text','don_background_hover','don_text_hover','petition_background','petition_text','petition_background_hover','petition_text_hover'];

		$root = [];
		foreach ($properties as $prop) {
			$value = trim($config[$prop] ?: "");

			if ($value) {
				$root[] = '--' . str_replace('_', '-', $prop) . ': ' . $value . ';';
			}
		}

		if ( empty($root) ) {
			return '<div class="gpfgf_wrapper">'.$form_string.'</div>';
		}

		$styles = implode('', $root);


		return '<div class="gpfgf_wrapper" style="'.$styles.'">'.$form_string.'</div>';
//		return str_replace('>', $styles.'>', $tag);

	}

}
