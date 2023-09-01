<?php

namespace Greenpeacefrance\Gravityforms;

use \GFForms;
use \GFAddOn;
use \GFFeedAddOn;
use \GF_Fields;
use \GFAPI;
use \GFFormDisplay;
use \GFFormsModel;


class Plugin {


	// TODO: IBAN / BIC pour RA
	// TODO: texte de l'opt-out






	public function loaded() {

		GFForms::include_feed_addon_framework();
		GFForms::include_addon_framework();


		GFAddOn::register( __NAMESPACE__ . '\\Crm' );
		GFAddOn::register( __NAMESPACE__ . '\\Apparence' );
		GFAddOn::register( __NAMESPACE__ . '\\ConfirmationScreen' );
		GFAddOn::register( __NAMESPACE__ . '\\Tracking' );
		GFAddOn::register( __NAMESPACE__ . '\\CssJs' );


		if ( is_admin() ) {
			GFAddOn::register( __NAMESPACE__ . '\\Folders' );
		}

		// mettre les Feeds après les Addons
		GFFeedAddOn::register( __NAMESPACE__ . '\\Feed\\SfmcFeed' );
		// GFAddOn::register( __NAMESPACE__ . '\\EngagingNetworks' );
		GFFeedAddOn::register( __NAMESPACE__ . '\\Feed\\Web2caseFeed' );
		// GFFeedAddOn::register( __NAMESPACE__ . '\\Feed\\DoubleOptInFeed' );
		// GFFeedAddOn::register( __NAMESPACE__ . '\\Feed\\TestFeed' );

		// GFFeedAddOn::register( __NAMESPACE__ . '\\Feed\\FacebookFeed' );


		GF_Fields::register( new Field\FirstNameField() );
		GF_Fields::register( new Field\LastNameField() );
		// GF_Fields::register( new Field\OptOutField() );
		GF_Fields::register( new Field\AddressField() );
		GF_Fields::register( new Field\CityField() );
		GF_Fields::register( new Field\PostcodeField() );
		// GF_Fields::register( new Field\PostcodeLuxField() );
		// GF_Fields::register( new Field\FakeCountryField() );
		GF_Fields::register( new Field\CountryField() );
		GF_Fields::register( new Field\DirtyHtmlField() );
		GF_Fields::register( new Field\SousAccrocheField() );
		GF_Fields::register( new Field\StateButtonField() );
		// GF_Fields::register( new Field\MentionsLegalesField() );
		//GF_Fields::register( new Field\IbanField() );
		GF_Fields::register( new Field\JaugeField() );


		if ( defined('THIS_IS_LUXEMBOURG') ) {
			GFAddOn::init_addons();
		}
		else {
			// Pas besoin d'activer ça pour le Luxembourg
			GFAddOn::register( __NAMESPACE__ . '\\DataFromDb' );
		}
	}



	public function submit_button( $button, $form ) {
		$required = 0;
		$obligatoire = "";
		$hide_required = false;

		foreach ($form['fields'] as $field) {
			if ($field->isRequired) {
				$required ++;
			}
		}


		if ($required > 0) {
			$type_indicateur = $form['requiredIndicator'];
// text
// asterisk
// custom
//                      $custom_indicator = $form['customRequiredIndicator'];

			if ($type_indicateur === 'asterisk') {

				$hide_required = boolval($form['hide_required'] ?? 0);
				$obligatoire = '<div class="champs-obligatoires-label">* : ' . ($required > 1 ? 'champs obligatoires' : 'champ obligatoire') . '</div>';


			}

			// on ne gère pas les autres cas. C'est censé être indiqué dans les champs ...
			// Au cas où, on peut simplement mettre le texte "MErci de vérifier les champs obligatoires"
		}




		// mentions légales
		$display_mentions_legales = false;
		$mentions = "";
		$position_mentions = 'below';




		if ( isset( $form['display_mentions_legales'] )
			&& intval( $form['display_mentions_legales'] ) ) {
				$display_mentions_legales = true;

				$position_mentions = intval($form['display_mentions_legales_position'] ?? "0") ? 'above' : 'below';

		}

		if ( $display_mentions_legales ) {
			$button_label = $form['button']['text'];
			$mentions_legales = $form['mentions_legales'];

			$mentions = '<div class="form-disclaimer">'. wpautop( sprintf( $mentions_legales, $button_label ) ) . '</div>';

		}


		$apparence = $form['greenpeace-design'];

		$cta_picto = $apparence['cta_picto'] ?? "";
		$picto = "";

		if ( ! empty( $cta_picto ) ) {
			$picto = '<span class="cta-picto">
			<svg x="0px" y="0px" viewBox="0 0 24 32"
			enable-background="new 0 0 24 32"
			xml:space="preserve"
			>
			<use href="#picto-'.$cta_picto.'"/>
			</svg>
			</span>';
		}

		// dernière chose, on change le type Input en Button
		$button = preg_replace("/<input (.*)value='([^']*)'(.*)>/", '<button $1 $3><span class="base-button-content">'.$picto.' <span>$2</span></span></button>', $button);

		// on récupère l'ID pour le mettre un cran au dessus ...
		preg_match("/id='([^']+)'/", $button, $match);
		// ... qu'on enlève du bouton
		$button = preg_replace("/id='([^']+)'/", "", $button);

		// on rajouye la classe de base CSS
		$button = preg_replace("/class='([^']+)'/", "class='base-button $1'", $button);



		$str = '<div class="gpfgf-submit-button" id="'.$match[1].'">';

		if ( ! $hide_required) {
			$str .= $obligatoire;
		}

		if ($position_mentions === 'above') {
			$str .= $mentions;
		}

		$str .= '<div class="submit-button-area">'
			. $button
			. '</div>';

		if ($position_mentions === 'below') {
				$str .= $mentions;
			}

		$str .= '</div>';
/*
		$str .= '<div style="display:none">
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
		<defs>
			<g id="picto-pen">
				<path fill="currentColor" d="M22.176 7.547l-13.245-7.013-1.598 7.207 9.778 5.174zM22.936 27.8c-1.071-0.29-2.178 0.222-3.072 0.639-0.101 0.047-0.202 0.093-0.295 0.14-0.202 0.088-0.403 0.191-0.605 0.295-0.532 0.268-1.086 0.552-1.552 0.543-0.403-0.006-0.915-0.14-1.578-0.397-0.183-0.073-0.372-0.189-0.569-0.313-0.388-0.233-0.838-0.543-1.505-0.617-2.048-0.23-4.003 0.042-5.911 0.307-0.636 0.085-1.303 0.18-1.955 0.251-0.233 0.026-0.481 0.062-0.714 0.102-0.559 0.090-0.946 0.135-1.21 0.088-0.186-0.031-0.326-0.104-0.497-0.372-0.015-0.034-0.062-0.059-0.078-0.093 1.35-1.396 4.748-4.309 10.457-5.191 0 0-1.722-4.903 1.365-8.898l-4.222-2.222-0.962-0.512-2.757-1.458c-1.572 4.794-6.599 6.122-6.599 6.122 2.79 5.877 1.732 10.764 1.342 12.124-0.251 0.285-0.315 0.704-0.109 1.047 0.403 0.687 0.993 1.102 1.741 1.232 0.636 0.109 1.288 0 1.871-0.093l0.559-0.093c0.683-0.078 1.359-0.171 2.014-0.264 1.877-0.264 3.631-0.512 5.492-0.295 0.217 0.031 0.465 0.171 0.729 0.357 0.264 0.155 0.527 0.326 0.869 0.45 0.884 0.341 1.583 0.512 2.203 0.527 0.915 0.015 1.707-0.388 2.451-0.76l0.497-0.248c0.109-0.047 0.217-0.109 0.357-0.171 0.574-0.279 1.288-0.59 1.8-0.527l0.078 0.015c0.465 0.078 0.915-0.202 1.040-0.652 0.14-0.481-0.155-0.977-0.636-1.117zM2.185 28.191l4.841-9.145c-0.301-0.295-0.388-0.76-0.183-1.151 0.251-0.476 0.951-0.585 1.427-0.332 0.475 0.253 0.88 0.919 0.63 1.396-0.209 0.388-0.645 0.58-1.055 0.493l-4.636 8.747c-0.284-0.194-0.675-0.23-0.977-0.051-0.020 0-0.031 0.015-0.047 0.031z"/>
			</g>

			<g id="picto-quote">
				<path fill="currentColor" d="M21.793 2.345c-3.31 1.241-6.207 3.172-8.69 5.793-1.931 2.207-2.897 4.23-2.897 6.069 0 0.829 0.138 1.655 0.414 2.483 0.552 0.828 1.241 1.655 1.931 2.345 0.828 0.69 1.517 1.519 2.069 2.345 0.414 0.964 0.552 1.931 0.552 2.897 0 2.069-0.828 4-2.207 5.517-1.241 1.379-3.172 2.207-5.103 2.207-2.069 0-4-0.828-5.241-2.481-1.517-1.519-2.345-3.726-2.345-6.070 0-4.321 1.701-8.735 5.103-13.241 3.862-4.69 9.103-8.276 14.897-10.207l1.517 2.345zM41.655 2.345c-3.172 1.241-6.207 3.172-8.552 5.793-2.023 2.299-3.034 4.321-3.034 6.069 0 0.829 0.139 1.655 0.552 2.483s1.103 1.655 1.93 2.345c0.829 0.69 1.519 1.519 2.069 2.345 0.276 0.964 0.552 1.931 0.415 2.897 0.138 2.069-0.69 4-2.069 5.379-1.379 1.517-3.174 2.343-5.103 2.343-2.069 0-4-0.964-5.241-2.481-1.655-1.517-2.483-3.724-2.345-6.069 0-4.321 1.701-8.735 5.103-13.241 3.862-4.69 8.966-8.276 14.759-10.207l1.517 2.345z"></path>

			</g>
		</defs>
		</svg>
		</div>';
		*/
		return $str;
	}


	public function field_validation($result, $value, $form, $field) {

		switch ($field->type) {
			case 'email':
				if ( strlen($value) > 80) {
					$result = [
						'is_valid' => false,
						'message' => 'Votre adresse e-mail est trop longue.',
					];
				}
				else if ( ! preg_match('/^[a-z0-9]([a-z0-9_\.\+-]*[^_\.\+-]+)?@([a-z0-9-]+\.)+[a-z0-9]+$/', $value) ) {
					$result = [
						'is_valid' => false,
						'message' => 'Votre adresse e-mail semble incorrecte.',
					];
				}
				break;

			case 'gp_last_name':
				if ( strlen( trim($value) ) === 0) {
					$result = [
						'is_valid' => false,
						'message' => 'Votre nom est obligatoire.'
					];
				}
				break;

			case 'gp_first_name':
				if ( strlen( trim($value) ) === 0) {
					$result = [
						'is_valid' => false,
						'message' => 'Votre prénom est obligatoire.'
					];
				}
				break;

			case 'phone':
				if ( ! empty($value) ) {
					try {
						$telephone = \Brick\PhoneNumber\PhoneNumber::parse($value, 'FR');
						if ( ! $telephone->isValidNumber()) {
							throw new \Exception('Téléphone invalide');
						}
					}
					catch(\Exception $e) {
						$result = [
							'is_valid' => false,
							'message' => 'Votre numéro de téléphone semble incorrect.'
						];
					}

				}
				break;

		}

		return $result;
	}

	public function pre_render($form, $ajax, $field_values) {
// wp_mail('hugo.poncedeleon@greenpeace.org', 'form', print_r($form, true));
		if (isset($form['has_floating_labels']) && $form['has_floating_labels']) {
			$form['cssClass'] .= ' with-floating-label';
		}

		return $form;
	}

	public function field_class( $classes, $field, $form ) {
		$classes .= ' gfgpf-elements-' . $field->type . ' ';
		if ($field instanceof \GF_Field_Select) {
			$classes .= 'field-activated ';
		}
		return $classes;
	}


	public function custom_merge_tags( $merge_tags, $form_id, $fields, $element_id ) {
		$merge_tags[] = [
			'label' => 'Langue de la page Luxembourg',
			'tag' => '{luxembourg_page_lang}'
		];

		return $merge_tags;
	}


	public function replace_merge_tags( $text, $form, $entry ) {
		if ( strpos( $text, '{luxembourg_page_lang}' ) !== false ) {

			$url = $entry['source_url'];

			if ( preg_match( '#/luxembourg/([a-z]{2})/#', $url, $matches ) ) {
				$lang = $matches[1];
				$text = str_replace( '{luxembourg_page_lang}', $lang, $text );
			}
		}

		return $text;
	}


	public function add_input_mask( $masks ) {
		$masks['Code postal FR'] = "99999";
		$masks['Code postal LU'] = "L9999";
		$masks['Code postal BE'] = "B9999";
		$masks['Code postal CH'] = "9999";
		$masks['Code postal DE'] = "99999";

		return $masks;
	}


	public function field_hotjar_masked( $content, $field, $value, $entry_id, $form_id ) {

		switch ( $field->type ) {
			case 'email':
			case 'phone':
			case 'gp_first_name':
			case 'gp_last_name':
			case 'gp_address':
			case 'gp_postcode':
			case 'gp_city':
			case 'text':
			case 'number':
			case 'password':
			case 'website':
				$content = preg_replace( '/<input /', '<input data-hj-masked ', $content);
				break;

			case 'textarea':
				$content = preg_replace( '/<textarea /', '<textarea data-hj-masked ', $content);
				break;
		}

		return $content;
	}




	public function field_maxlength( $content, $field, $value, $entry_id, $form_id ) {

		switch ( $field->type ) {
			case 'email':
				$content = preg_replace( '/<input /', '<input maxlength="80" ', $content);
				break;
		}

		return $content;
	}




	public function field_quiz_correct_answers($content, $field, $value, $entry_id, $form_id) {

		if ( $field->type === 'quiz' && ! empty($field->gpfQuizAllChoicesCorrect) ) {
			$content = preg_replace('/<input /', '<input data-all-correct ', $content);
		}

		return $content;
	}

	public function add_optgroup_to_select( $choice_markup, $choice, $field ) {
		if ( $field->get_input_type() == 'select' ) {
			$choice_value = rgar( $choice, 'value' );
			if ( $choice_value === 'optgroup' ) {
				return sprintf( '<optgroup label="%s">', esc_html( $choice['text'] ) );
			} elseif ( $choice_value === '/optgroup' ) {
				return '</optgroup>';
			}
		}

		return $choice_markup;
	}

	public function form_javascript($form) {
		// Ce script s'execute après l'affichage du form.
		// On a donc accès aux éléments qu'on cible.
		$script = <<<END
(function() {

var valid = document.querySelector('.gfield .spf-phone.valid-msg');
var error = document.querySelector('.gfield .spf-phone.error-msg');

if (valid) {
	valid.innerHTML = '&#10004; numéro de téléphone valide';
}

if (error) {
	error.innerHTML = '&#10006; numéro de téléphone invalide';
}

var charleft = document.querySelectorAll('.gfield .charleft')
if (charleft) {
	charleft.forEach( function(item) {
		item.style.display = 'none'
	})
}
})();
END;

		GFFormDisplay::add_init_script( $form['id'], 'remove-valid-msg', GFFormDisplay::ON_PAGE_RENDER, $script );

	}



	public function form_args($args) {


		$args['display_title'] = false;
		$args['display_description'] = false;

		// gestion Ajax

		if ($args['ajax']) {
			return $args;
		}

		$form = GFAPI::get_form( $args[ 'form_id' ] ) ;

		if ($form ) {

			// Si il n'est pas présent, c'est qu'il n'a pas été configuré pour le formulaire.
			// On l'active par défaut.
			if ( ! isset($form['enable_ajax'])) {
				$args['ajax'] = true;
			}
			else {
				$args['ajax'] = boolval($form['enable_ajax']);
			}
		}

		return $args;
	}


	public function global_settings($fields, $form) {

		$fields['form_basics']['fields'][] = [
			'name' => 'enable_ajax',
			'type' => 'toggle',
			'label' => 'Envoi du formulaire en AJAX',
			'default_value' => 0,
		];


		$fields['form_layout']['fields'][] = [
			'name' => 'hide_required',
			'type' => 'toggle',
			'label' => "Cacher l'information \"champs obligatoires\"",
			'default_value' => "0",
			"description" => "Cette ligne de texte est affichée automatiquement. Cochez pour la cacher et la gérer à la main avec un champ HTML dans le formulaire. Ne fonctionne que pour le type \"astérisque *\"",
		];


		$fields['form_layout']['fields'][] = [
			'name' => 'has_floating_labels',
			'type' => 'toggle',
			'label' => 'Label flottant (ils sont placés en placeholder puis se placent au dessus des champs lors du focus)',
			'default_value' => "0",
			"description" => "",
		];

		$fields['form_basics']['fields'][] = [
			'name' => 'display_mentions_legales',
			'type' => 'toggle',
			'label' => 'Affichage automatique des mentions légales ci-dessous. Un %s dans le texte sera remplacé par le label du bouton.',
			'default_value' => "0",
		];



		$fields['form_basics']['fields'][] = [
			'name' => 'mentions_legales',
			'type' => 'textarea',
			'use_editor' => true,
			'label' => '',
			'default_value' => '',
		];

		$fields['form_basics']['fields'][] = [
			'name' => 'display_mentions_legales_position',
			'type' => 'toggle',
			'label' => 'Coché : mentions affichées sous le bouton Valider. Non coché : mentions affichées au dessus du bouton.',
			'default_value' => "0",
			"description" => "",
		];

		return $fields;
	}


	public function field_groups_editor($field_groups) {

		// on crée un nouveau tableau pour avoir GP en premier
		$new_groups = [];

		$new_groups['greenpeace_fields'] = [
			'name' => 'greenpeace_fields',
			'label' => 'Greenpeace',
			'fields' => [],
		];

		foreach ($field_groups as $name => $data) {
			$new_groups[$name] = $data;
		}

		return $new_groups;
	}



	public function field_settings_js() {
		?>
		<script type='text/javascript'>

			fieldSettings.gp_jauge += ', .gp_jauge_start_setting, .gp_jauge_target_setting, .gp_jauge_min_setting, .gp_jauge_text_setting'


			jQuery(document).bind('gform_load_field_settings', function(event, field, form){

				if (field.type === 'gp_jauge') {
					jQuery('#jauge_start').val( rgar(field, 'jauge_start') )
					jQuery('#jauge_objectif').val( rgar(field, 'jauge_objectif') )
					jQuery('#jauge_text').val( rgar(field, 'jauge_text') )
					jQuery('#jauge_min').val( rgar(field, 'jauge_min') )
				}
			});

		</script>
		<?php
	}


	public function field_settings( $position, $form_id ) {

		$form = GFAPI::get_form($form_id);


		if ( $position == 25 ) {

			?>

			<li class="gp_jauge_start_setting field_setting">
				<label for="jauge_start" style="display:inline;">
					Valeur de départ (sera additionné au nombre de signataires de ce formulaires)
				</label>
				<input type="text" id="jauge_start" oninput="SetFieldProperty('jauge_start', this.value);"/>
			</li>

			<li class="gp_jauge_target_setting field_setting">
				<label for="jauge_objectif" style="display:inline;">
					Objectif
				</label>
				<input type="text" id="jauge_objectif" oninput="SetFieldProperty('jauge_objectif', this.value);"/>
			</li>

			<li class="gp_jauge_min_setting field_setting">
				<label for="gp_jauge_min_value" style="display:inline;">
					Afficher à partir de&nbsp;...
				</label>
				<input type="text" id="jauge_minimum" oninput="SetFieldProperty('jauge_minimum', this.value);"/>
			</li>


			<li class="gp_jauge_text_setting field_setting">
				<label for="gp_jauge_text_value" style="display:inline;">
					Texte affiché. Un %s sera remplacé par le nombre de signataires. Peut contenir du HTML.
				</label>
				<textarea id="jauge_text" oninput="SetFieldProperty('jauge_text', this.value);"></textarea>
			</li>



			<?php
		}

/*
		if ( $position === 1368) { //parmi l'Edit Choices, pour le field Quiz
			?>
			<li class="gp_quiz_all_correct field_setting" data-js="choices-ui-setting" data-type="option">
				<input type="checkbox" id="gpf-quiz-all-choices-correct" onclick="var value = jQuery(this).is(':checked'); SetFieldProperty('gpfQuizAllChoicesCorrect', value);">
				<label for="gpf-quiz-all-choices-correct" class="inline">
					Toutes les réponses sont correctes.
				</label>
			</li>
			<?php

		}
		*/

	}


/*

	public function editor_script(){
		?>
		<script type='text/javascript'>

			fieldSettings.gp_html += ', .gp_html_content_setting';

			document.addEventListener('gform_load_field_settings', function(event, field, form){

				document.querySelector( '#gp_html_content_value' ).value = rgar( field, 'content' ) || 'div';
			});
		</script>
		<?php
	}


	public function div_field_options( $position, $form_id ) {
		if ( $position == 25 ) {
			?>

			<li class="gp_html_content_setting field_setting">
				<label for="gp_html_content_value" style="display:inline;">
					Contenu HTML
				</label>
				<textarea id="gp_html_content_value" oninput="SetFieldProperty('content', this.value);"></textarea>

			</li>
			<?php
		}
	}
*/

}