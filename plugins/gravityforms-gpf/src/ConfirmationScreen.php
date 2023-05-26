<?php


namespace Greenpeacefrance\Gravityforms;


class ConfirmationScreen extends \GFAddOn {

	protected $_version = '0.1';
    protected $_min_gravityforms_version = '2.5';
    protected $_slug = 'greenpeace-confirmation';
    protected $_full_path = __FILE__;
    protected $_title = 'Confirmation GP';
    protected $_short_title = 'Confirmation GP';


	protected $_capabilities_form_settings = 'edit_posts';
	protected $_capabilities_uninstall = 'manage_options';
    private static $_instance = null;

	public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new ConfirmationScreen();
        }

        return self::$_instance;
    }



	public function get_menu_icon() {
		return 'dashicons-art';
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

	public function init() {
		parent::init();

		add_filter( 'gform_confirmation', [ $this, 'render' ], 10, 4 );
	}




	public function form_settings_fields( $form ) {

        return [
			[
				'title' => 'Configuration Custom',
				'fields' => [
					[
						'name' => 'use_custom_confirmation',
						'type' => 'toggle',
						'label' => "Utiliser cet écran de confirmation au lieu de celui de Gravity Forms.",
						'default_value' => "0",
					],
					[
						'name' => 'use_don',
						'label' => 'Bloc don',
						'type' => 'checkbox_and_select',
						'checkbox' => [
							'name'  => 'use_don_enable',
							'label' => 'Afficher le bloc de don',
							'default_value' => 1,
						],
						'select'   => [
							'name' => 'use_don_position',
							'label' => 'position',
							'choices' => [
								[
									'label' => 'En premier',
									'value' => '1'
								],
								[
									'label' => 'En deuxième',
									'value' => '2'
								],
								[
									'label' => 'En troisième',
									'value' => '3'
								]
							],
							'default_value' => "1",
						]
					],

					[
						'name' => 'use_share',
						'label' => 'Bloc partage',
						'type' => 'checkbox_and_select',
						'checkbox' => [
							'name'  => 'use_share_enable',
							'label' => 'Afficher le bloc de partage',
							'default_value' => 1,
						],
						'select'   => [
							'name' => 'use_share_position',
							'label' => 'position',
							'choices' => [
								[
									'label' => 'En premier',
									'value' => '1'
								],
								[
									'label' => 'En deuxième',
									'value' => '2'
								],
								[
									'label' => 'En troisième',
									'value' => '3'
								]
							],
							'default_value' => "2",
						]
					],

					[
						'name' => 'use_petition',
						'label' => 'Bloc pétition',
						'type' => 'checkbox_and_select',
						'checkbox' => [
							'name'  => 'use_petition_enable',
							'label' => 'Afficher le bloc de pétition',
							'default_value' => 1,
						],
						'select'   => [
							'name' => 'use_petition_position',
							'label' => 'position',
							'choices' => [
								[
									'label' => 'En premier',
									'value' => '1'
								],
								[
									'label' => 'En deuxième',
									'value' => '2'
								],
								[
									'label' => 'En troisième',
									'value' => '3'
								]
							],
							'default_value' => "3",
						]
					],
				]
			],

			// [
			// 	'title' => 'Apparence',
			// 	'fields' => [
			// 		[
			// 			'type' => 'text',
			// 			'name' => 'first_line_color',
			// 			'label' => 'Couleur de la première ligne des blocs (défaut : currentColor)',
			// 			'class' => 'large',
			// 		],

			// 		[
			// 			'type' => 'text',
			// 			'name' => 'second_line_color',
			// 			'label' => 'Couleur du reste du texte des blocs (défaut : couleur de première ligne)',
			// 			'class' => 'large',
			// 		],

			// 		[
			// 			'type' => 'text',
			// 			'name' => 'first_block_color',
			// 			'label' => 'Couleur background du premier bloc (défaut, blanc)',
			// 			'class' => 'large',
			// 		],

			// 		[
			// 			'type' => 'text',
			// 			'name' => 'first_block_first_line_color',
			// 			'label' => 'Couleur de la première ligne du premier bloc (défaut : première ligne bloc normal)',
			// 			'class' => 'large',
			// 		],

			// 		[
			// 			'type' => 'text',
			// 			'name' => 'first_block_second_line_color',
			// 			'label' => 'Couleur du reste du texte du premier bloc (défaut : seconde ligne bloc normal)',
			// 			'class' => 'large',
			// 		],


			// 		[
			// 			'type' => 'text',
			// 			'name' => 'first_block_cta_background_color',
			// 			'label' => 'Couleur bouton du premier bloc (défaut : background du bouton cta)',
			// 			'class' => 'large',
			// 		],

			// 		[
			// 			'type' => 'text',
			// 			'name' => 'first_block_cta_text_color',
			// 			'label' => 'Couleur du texte du bouton du premier bloc (défaut : texte du bouton cta)',
			// 			'class' => 'large',
			// 		],




			// 		[
			// 			'type' => 'text',
			// 			'name' => 'cta_hover_background_color',
			// 			'label' => 'Couleur hover du bouton pétition (défaut : background du bouton cta)',
			// 			'class' => 'large',
			// 		],

			// 		[
			// 			'type' => 'text',
			// 			'name' => 'cta_text_color',
			// 			'label' => 'Couleur du texte du bouton pétition (défaut : couleur première ligne)',
			// 			'class' => 'large',
			// 		],


			// 	]
			// ],

			[
				'title' => 'Message principal de remerciement',
				'fields' => [
					[
						'type' => 'textarea',
						'name' => 'thank_you_message',
						'label' => '',
						'use_editor' => true,
						// 'class' => 'large',
					],
				]
			],

			[
				'title' => 'Bloc don',
				'fields' => [
					[
						'type' => 'textarea',
						'name' => 'don_message_1',
						'label' => 'Texte 1 : Premier niveau, en gras.',
						// 'class' => 'large',
						'use_editor' => true,
					],
					[
						'type' => 'textarea',
						'name' => 'don_message_2',
						'label' => 'Texte 2 : Deuxième niveau, simple.',
						// 'class' => 'large',
						'use_editor' => true,
					],
					[
						'type' => 'text',
						'name' => 'don_button_text',
						'label' => 'Texte du bouton',
						'class' => 'large',
					],
					[
						'type' => 'text',
						'name' => 'don_button_link',
						'label' => 'Lien du bouton',
						'class' => 'large',
					],
				]
			],

			[
				'title' => 'Bloc partage',
				'fields' => [
					[
						'type' => 'textarea',
						'name' => 'share_message_1',
						'label' => 'Texte 1 : Premier niveau, en gras.',
						// 'class' => 'large',
						'use_editor' => true,
					],
					[
						'type' => 'textarea',
						'name' => 'share_message_2',
						'label' => 'Texte 2 : Deuxième niveau, simple.',
						// 'class' => 'large',
						'use_editor' => true,
					],
					[
						'type' => 'toggle',
						'name' => 'share_facebook',
						'label' => 'Facebook',
						'default_value' => "1",
					],

					[
						'type' => 'text',
						'name' => 'share_facebook_message',
						'label' => 'Lien de partage Facebook',
						'dependency' => [
							'live' => true,
							'fields' => [
								[
									'field' => 'share_facebook',
								]
							]
						]
					],

					[
						'type' => 'toggle',
						'name' => 'share_twitter',
						'label' => 'Twitter',
						'default_value' => "1",
					],

					[
						'type' => 'textarea',
						'name' => 'share_twitter_message',
						'label' => 'Message Twitter',
						'dependency' => [
							'live' => true,
							'fields' => [
								[
									'field' => 'share_twitter',
								]
							]
						]
					],

					[
						'type' => 'toggle',
						'name' => 'share_whatsapp',
						'label' => 'Whatsapp',
						'default_value' => "1",
					],

					[
						'type' => 'textarea',
						'name' => 'share_whatsapp_message',
						'label' => 'Message Whatsapp',
						'dependency' => [
							'live' => true,
							'fields' => [
								[
									'field' => 'share_whatsapp',
								]
							]
						]
					],
					[
						'type' => 'toggle',
						'name' => 'share_mailto',
						'label' => 'Mailto',
						'default_value' => "1",
					],

					[
						'type' => 'textarea',
						'name' => 'share_mailto_message',
						'label' => 'Message Mailto',
						'dependency' => [
							'live' => true,
							'fields' => [
								[
									'field' => 'share_mailto',
								]
							]
						]
					],

				]
			],

			[
				'title' => 'Bloc pétition',
				'fields' => [
					[
						'type' => 'textarea',
						'name' => 'petition_message_1',
						'label' => 'Texte 1 : Premier niveau, en gras.',
						// 'class' => 'large',
						'use_editor' => true,
					],
					[
						'type' => 'textarea',
						'name' => 'petition_message_2',
						'label' => 'Texte 2 : Deuxième niveau, simple.',
						// 'class' => 'large',
						'use_editor' => true,
					],
					[
						'type' => 'text',
						'name' => 'petition_button_text',
						'label' => 'Texte du bouton',
						'class' => 'large',
					],
					[
						'type' => 'text',
						'name' => 'petition_button_link',
						'label' => 'Lien du bouton',
						'class' => 'large',
					],
				]
			],


//TODO: Si on veut ajouter des conditions : https://docs.gravityforms.com/the-simple_condition-helper/
		];
	}



	public function render($confirmation, $form, $entry, $ajax) {

		$config = $form['greenpeace-confirmation'] ?? false;

		if ( ! $config) {
			return $confirmation;
		}

		$use_custom_confirmation = intval( $config['use_custom_confirmation'] ?? "0" );

		if ( ! $use_custom_confirmation) {
			return $confirmation;
		}


		$form_id = $form['id'];

		$blocks = [];

		$use_don = intval( $config['use_don_enable'] ?? "0" );
		$use_share = intval( $config['use_share_enable'] ?? "0" );
		$use_petition = intval( $config['use_petition_enable'] ?? "0" );




		if ($use_don) {
			$position = intval( $config['use_don_position']);

			$don_html = '<div class="confirmation-first-message">' . \GFCommon::replace_variables( trim( $config['don_message_1'] ), $form, $entry, false, false, true ) . '</div>';
			$don_html .= '<div class="confirmation-second-message">' . \GFCommon::replace_variables( trim( $config['don_message_2'] ), $form, $entry, false, false, true ) . '</div>';

			$don_bouton = '<div class="confirmation-block-button-wrapper"><a href="'.esc_url( trim( $config['don_button_link'] ) ).'" class="gform_button button don-button">'.trim( $config['don_button_text'] ).'</a></div>';

			$blocks[ $position ] = '<div class="confirmation-block-inner">'.$don_html . $don_bouton . '</div>';
		}





		if ($use_petition) {
			$position = intval( $config['use_petition_position']);

			$petition_html = '<div class="confirmation-first-message">' . \GFCommon::replace_variables( trim( $config['petition_message_1'] ), $form, $entry, false, false, true ) . '</div>';
			$petition_html .= '<div class="confirmation-second-message">' . \GFCommon::replace_variables( trim( $config['petition_message_2'] ), $form, $entry, false, false, true ) . '</div>';

			$petition_bouton = '<div class="confirmation-block-button-wrapper"><a href="'.esc_url( trim( $config['petition_button_link'] ) ).'" class="gform_button button petition-button">'.trim( $config['petition_button_text'] ).'</a></div>';


			$blocks[ $position ] = '<div class="confirmation-block-inner">'.$petition_html . $petition_bouton.'</div>';
		}




		do {
			if ($use_share) {
				$position = intval( $config['use_share_position']);

				$share_html = '<div class="confirmation-first-message">' . \GFCommon::replace_variables( trim( $config['share_message_1'] ), $form, $entry, false, false, true ) . '</div>';
				$share_html .= '<div class="confirmation-second-message">' . \GFCommon::replace_variables( trim( $config['share_message_2'] ), $form, $entry, false, false, true ) . '</div>';


				$list_share = [];

				if (intval($config['share_facebook'] ?? "0")) {
					$list_share[] = 'facebook';
				}

				if (intval($config['share_twitter'] ?? "0")) {
					$list_share[] = 'twitter';
				}

				if (intval($config['share_whatsapp'] ?? "0")) {
					$list_share[] = 'whatsapp';
				}

				if (intval($config['share_mailto'] ?? "0")) {
					$list_share[] = 'mailto';
				}

				if (empty($list_share)) {
					break;
				}

				$list_html = '';


				foreach( $list_share as $item) {

					$link = trim($config['share_'.$item.'_message'] ?? "");
					$html =<<< END
					<svg role="img" aria-labelledby="{$item}-title"
					x="0px" y="0px"
					width="24px" height="24px" viewBox="0 0 24 24"
					enable-background="new 0 0 24 24"
					xml:space="preserve"
					>
					<title id="{$item}-title">{$item}</title>
					<use href="#{$item}-logo"/>
					</svg>
					END;

					if ($link) {
						$html =<<< END
						<a href="{$link}"
							target="_blank"
							class="share-item-link"
							data-action="share-item"
							data-item="{$item}">
							{$html}
						</a>
						END;
					}

					$html =<<< END
					<div class="share-item">{$html}</div>
					END;

					$list_html .= $html;
				}


				$list_html = '<div class="share-items">' . $list_html . '</div>';

				$blocks[ $position ] = '<div class="confirmation-block-inner">'.$share_html . $list_html.'</div>';
			}
		}
		while (0);








		$html = '<div class="confirmation-block"><div class="confirmation-block-inner">' . \GFCommon::replace_variables( trim( $config['thank_you_message'] ), $form, $entry, false, false, true ) . '</div></div>';

		$i = true;
		foreach ($blocks as $block) {
			$class = "confirmation-block";
			if ($i === true) {
				$i = false;
				$class .= ' confirmation-first-block';
			}

			$html .= '<div class="'.$class.'">'.$block.'</div>';

		}

		$html =<<< END
		<div id="gf_{$form_id}" class="gform_anchor" tabindex="-1">
		{$html}
		END;

		return $html;
	}

}
