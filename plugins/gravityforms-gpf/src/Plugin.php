<?php

namespace Greenpeacefrance\Gravityforms;

use \GFForms;
use \GFAddOn;
use \GFFeedAddOn;
use \GF_Fields;
use \GFAPI;
use \GFFormDisplay;

class Plugin {


	// TODO: IBAN / BIC pour RA
	// TODO: texte de l'opt-out






	public function loaded() {

		GFForms::include_feed_addon_framework();
		GFForms::include_addon_framework();

		GFAddOn::register( __NAMESPACE__ . '\\Crm' );
		GFAddOn::register( __NAMESPACE__ . '\\Tracking' );
		// GFAddOn::register( __NAMESPACE__ . '\\EngagingNetworks' );


		GFFeedAddOn::register( __NAMESPACE__ . '\\Feed\\SfmcFeed' );
		GFFeedAddOn::register( __NAMESPACE__ . '\\Feed\\Web2caseFeed' );
		// GFFeedAddOn::register( __NAMESPACE__ . '\\Feed\\DoubleOptInFeed' );
		GFAddOn::register( __NAMESPACE__ . '\\CssJs' );
		GFFeedAddOn::register( __NAMESPACE__ . '\\Feed\\TestFeed' );

		GF_Fields::register( new Field\FirstNameField() );
		GF_Fields::register( new Field\LastNameField() );

		GF_Fields::register( new Field\OptOutField() );

		GF_Fields::register( new Field\AddressField() );
		GF_Fields::register( new Field\CityField() );
		GF_Fields::register( new Field\PostcodeField() );
		GF_Fields::register( new Field\FakeCountryField() );
		GF_Fields::register( new Field\CountryField() );

		GF_Fields::register( new Field\DirtyHtmlField() );
		GF_Fields::register( new Field\StateButtonField() );
		// GF_Fields::register( new Field\MentionsLegalesField() );

		//GF_Fields::register( new Field\IbanField() );

		GF_Fields::register( new Field\JaugeField() );


		if ( defined('THIS_IS_LUXEMBOURG') ) {
			GFAddOn::init_addons();
		}
	}



	public function add_mentions_legales( $button, $form ) {
		$display_mentions_legales = false;

		if ( isset( $form['display_mentions_legales'] )
			&& intval( $form['display_mentions_legales'] ) === 1 ) {
				$display_mentions_legales = true;
		}

		if ( $display_mentions_legales ) {
			$button_label = $form['button']['text'];
			$mentions_legales = $form['mentions_legales'];

			$mentions = '<div class="form-disclaimer">'. wpautop( sprintf( $mentions_legales, $button_label ) ) . '</div>';

			// $html = preg_replace( '#</div>$#', $mentions . '</div>', $html );
			$button = '<div class="gpfgf-submit-button">' . $button . $mentions . '</div>';
		}


		return $button;
	}


	public function pre_render($form, $ajax, $field_values) {
// wp_mail('hugo.poncedeleon@greenpeace.org', 'form', print_r($form, true));
		if ($form['has_floating_labels']) {
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


	public function field_content( $content, $field, $value, $entry_id, $form_id ) {

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

			case 'textarea':
				$content = preg_replace( '/<textarea /', '<textarea data-hj-masked ', $content);
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
		$script = <<<END
(function() {
// copied from remove-accents
/*
The MIT License (MIT)

Copyright (c) 2015 Marin Atanasov

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
*/
var characterMap = {
	"À": "A",
	"Á": "A",
	"Â": "A",
	"Ã": "A",
	"Ä": "A",
	"Å": "A",
	"Ấ": "A",
	"Ắ": "A",
	"Ẳ": "A",
	"Ẵ": "A",
	"Ặ": "A",
	"Æ": "AE",
	"Ầ": "A",
	"Ằ": "A",
	"Ȃ": "A",
	"Ç": "C",
	"Ḉ": "C",
	"È": "E",
	"É": "E",
	"Ê": "E",
	"Ë": "E",
	"Ế": "E",
	"Ḗ": "E",
	"Ề": "E",
	"Ḕ": "E",
	"Ḝ": "E",
	"Ȇ": "E",
	"Ì": "I",
	"Í": "I",
	"Î": "I",
	"Ï": "I",
	"Ḯ": "I",
	"Ȋ": "I",
	"Ð": "D",
	"Ñ": "N",
	"Ò": "O",
	"Ó": "O",
	"Ô": "O",
	"Õ": "O",
	"Ö": "O",
	"Ø": "O",
	"Ố": "O",
	"Ṍ": "O",
	"Ṓ": "O",
	"Ȏ": "O",
	"Ù": "U",
	"Ú": "U",
	"Û": "U",
	"Ü": "U",
	"Ý": "Y",
	"à": "a",
	"á": "a",
	"â": "a",
	"ã": "a",
	"ä": "a",
	"å": "a",
	"ấ": "a",
	"ắ": "a",
	"ẳ": "a",
	"ẵ": "a",
	"ặ": "a",
	"æ": "ae",
	"ầ": "a",
	"ằ": "a",
	"ȃ": "a",
	"ç": "c",
	"ḉ": "c",
	"è": "e",
	"é": "e",
	"ê": "e",
	"ë": "e",
	"ế": "e",
	"ḗ": "e",
	"ề": "e",
	"ḕ": "e",
	"ḝ": "e",
	"ȇ": "e",
	"ì": "i",
	"í": "i",
	"î": "i",
	"ï": "i",
	"ḯ": "i",
	"ȋ": "i",
	"ð": "d",
	"ñ": "n",
	"ò": "o",
	"ó": "o",
	"ô": "o",
	"õ": "o",
	"ö": "o",
	"ø": "o",
	"ố": "o",
	"ṍ": "o",
	"ṓ": "o",
	"ȏ": "o",
	"ù": "u",
	"ú": "u",
	"û": "u",
	"ü": "u",
	"ý": "y",
	"ÿ": "y",
	"Ā": "A",
	"ā": "a",
	"Ă": "A",
	"ă": "a",
	"Ą": "A",
	"ą": "a",
	"Ć": "C",
	"ć": "c",
	"Ĉ": "C",
	"ĉ": "c",
	"Ċ": "C",
	"ċ": "c",
	"Č": "C",
	"č": "c",
	"C̆": "C",
	"c̆": "c",
	"Ď": "D",
	"ď": "d",
	"Đ": "D",
	"đ": "d",
	"Ē": "E",
	"ē": "e",
	"Ĕ": "E",
	"ĕ": "e",
	"Ė": "E",
	"ė": "e",
	"Ę": "E",
	"ę": "e",
	"Ě": "E",
	"ě": "e",
	"Ĝ": "G",
	"Ǵ": "G",
	"ĝ": "g",
	"ǵ": "g",
	"Ğ": "G",
	"ğ": "g",
	"Ġ": "G",
	"ġ": "g",
	"Ģ": "G",
	"ģ": "g",
	"Ĥ": "H",
	"ĥ": "h",
	"Ħ": "H",
	"ħ": "h",
	"Ḫ": "H",
	"ḫ": "h",
	"Ĩ": "I",
	"ĩ": "i",
	"Ī": "I",
	"ī": "i",
	"Ĭ": "I",
	"ĭ": "i",
	"Į": "I",
	"į": "i",
	"İ": "I",
	"ı": "i",
	"Ĳ": "IJ",
	"ĳ": "ij",
	"Ĵ": "J",
	"ĵ": "j",
	"Ķ": "K",
	"ķ": "k",
	"Ḱ": "K",
	"ḱ": "k",
	"K̆": "K",
	"k̆": "k",
	"Ĺ": "L",
	"ĺ": "l",
	"Ļ": "L",
	"ļ": "l",
	"Ľ": "L",
	"ľ": "l",
	"Ŀ": "L",
	"ŀ": "l",
	"Ł": "l",
	"ł": "l",
	"Ḿ": "M",
	"ḿ": "m",
	"M̆": "M",
	"m̆": "m",
	"Ń": "N",
	"ń": "n",
	"Ņ": "N",
	"ņ": "n",
	"Ň": "N",
	"ň": "n",
	"ŉ": "n",
	"N̆": "N",
	"n̆": "n",
	"Ō": "O",
	"ō": "o",
	"Ŏ": "O",
	"ŏ": "o",
	"Ő": "O",
	"ő": "o",
	"Œ": "OE",
	"œ": "oe",
	"P̆": "P",
	"p̆": "p",
	"Ŕ": "R",
	"ŕ": "r",
	"Ŗ": "R",
	"ŗ": "r",
	"Ř": "R",
	"ř": "r",
	"R̆": "R",
	"r̆": "r",
	"Ȓ": "R",
	"ȓ": "r",
	"Ś": "S",
	"ś": "s",
	"Ŝ": "S",
	"ŝ": "s",
	"Ş": "S",
	"Ș": "S",
	"ș": "s",
	"ş": "s",
	"Š": "S",
	"š": "s",
	"Ţ": "T",
	"ţ": "t",
	"ț": "t",
	"Ț": "T",
	"Ť": "T",
	"ť": "t",
	"Ŧ": "T",
	"ŧ": "t",
	"T̆": "T",
	"t̆": "t",
	"Ũ": "U",
	"ũ": "u",
	"Ū": "U",
	"ū": "u",
	"Ŭ": "U",
	"ŭ": "u",
	"Ů": "U",
	"ů": "u",
	"Ű": "U",
	"ű": "u",
	"Ų": "U",
	"ų": "u",
	"Ȗ": "U",
	"ȗ": "u",
	"V̆": "V",
	"v̆": "v",
	"Ŵ": "W",
	"ŵ": "w",
	"Ẃ": "W",
	"ẃ": "w",
	"X̆": "X",
	"x̆": "x",
	"Ŷ": "Y",
	"ŷ": "y",
	"Ÿ": "Y",
	"Y̆": "Y",
	"y̆": "y",
	"Ź": "Z",
	"ź": "z",
	"Ż": "Z",
	"ż": "z",
	"Ž": "Z",
	"ž": "z",
	"ſ": "s",
	"ƒ": "f",
	"Ơ": "O",
	"ơ": "o",
	"Ư": "U",
	"ư": "u",
	"Ǎ": "A",
	"ǎ": "a",
	"Ǐ": "I",
	"ǐ": "i",
	"Ǒ": "O",
	"ǒ": "o",
	"Ǔ": "U",
	"ǔ": "u",
	"Ǖ": "U",
	"ǖ": "u",
	"Ǘ": "U",
	"ǘ": "u",
	"Ǚ": "U",
	"ǚ": "u",
	"Ǜ": "U",
	"ǜ": "u",
	"Ứ": "U",
	"ứ": "u",
	"Ṹ": "U",
	"ṹ": "u",
	"Ǻ": "A",
	"ǻ": "a",
	"Ǽ": "AE",
	"ǽ": "ae",
	"Ǿ": "O",
	"ǿ": "o",
	"Þ": "TH",
	"þ": "th",
	"Ṕ": "P",
	"ṕ": "p",
	"Ṥ": "S",
	"ṥ": "s",
	"X́": "X",
	"x́": "x",
	"Ѓ": "Г",
	"ѓ": "г",
	"Ќ": "К",
	"ќ": "к",
	"A̋": "A",
	"a̋": "a",
	"E̋": "E",
	"e̋": "e",
	"I̋": "I",
	"i̋": "i",
	"Ǹ": "N",
	"ǹ": "n",
	"Ồ": "O",
	"ồ": "o",
	"Ṑ": "O",
	"ṑ": "o",
	"Ừ": "U",
	"ừ": "u",
	"Ẁ": "W",
	"ẁ": "w",
	"Ỳ": "Y",
	"ỳ": "y",
	"Ȁ": "A",
	"ȁ": "a",
	"Ȅ": "E",
	"ȅ": "e",
	"Ȉ": "I",
	"ȉ": "i",
	"Ȍ": "O",
	"ȍ": "o",
	"Ȑ": "R",
	"ȑ": "r",
	"Ȕ": "U",
	"ȕ": "u",
	"B̌": "B",
	"b̌": "b",
	"Č̣": "C",
	"č̣": "c",
	"Ê̌": "E",
	"ê̌": "e",
	"F̌": "F",
	"f̌": "f",
	"Ǧ": "G",
	"ǧ": "g",
	"Ȟ": "H",
	"ȟ": "h",
	"J̌": "J",
	"ǰ": "j",
	"Ǩ": "K",
	"ǩ": "k",
	"M̌": "M",
	"m̌": "m",
	"P̌": "P",
	"p̌": "p",
	"Q̌": "Q",
	"q̌": "q",
	"Ř̩": "R",
	"ř̩": "r",
	"Ṧ": "S",
	"ṧ": "s",
	"V̌": "V",
	"v̌": "v",
	"W̌": "W",
	"w̌": "w",
	"X̌": "X",
	"x̌": "x",
	"Y̌": "Y",
	"y̌": "y",
	"A̧": "A",
	"a̧": "a",
	"B̧": "B",
	"b̧": "b",
	"Ḑ": "D",
	"ḑ": "d",
	"Ȩ": "E",
	"ȩ": "e",
	"Ɛ̧": "E",
	"ɛ̧": "e",
	"Ḩ": "H",
	"ḩ": "h",
	"I̧": "I",
	"i̧": "i",
	"Ɨ̧": "I",
	"ɨ̧": "i",
	"M̧": "M",
	"m̧": "m",
	"O̧": "O",
	"o̧": "o",
	"Q̧": "Q",
	"q̧": "q",
	"U̧": "U",
	"u̧": "u",
	"X̧": "X",
	"x̧": "x",
	"Z̧": "Z",
	"z̧": "z",
};

var chars = Object.keys(characterMap).join('|');
var allAccents = new RegExp(chars, 'g');

window.gpfRemoveAccents = function(string) {
	return string.replace(allAccents, function(match) {
		return characterMap[match];
	});
}


window.gpfCleanInput = function(charCode, target, maxLength, keepNumbers ) {
	var cursorStart = target.selectionStart;
	var cursorEnd = target.selectionEnd;

	if (
		charCode === 32
		|| charCode === 39
		|| charCode === 45
		|| ( keepNumbers && ( charCode >= 48 && charCode <= 57 ) )
		|| ( charCode >= 65 && charCode <= 90 )
		|| ( charCode >= 97 && charCode <= 122 )
		|| ( charCode >= 192 && charCode <= 214 )
		|| ( charCode >= 216 && charCode <= 246 )
		|| ( charCode >= 248 && charCode <= 255 )
	) {
		var value = target.value
		var f1 = value.substring(0, cursorStart);
		var f2 = value.substring(cursorStart, cursorEnd);
		var f3 = value.substring(cursorEnd);
		var v = f1 + String.fromCharCode( charCode ) + f3;
		v = v.slice( 0, maxLength )
			.trimStart()
			.replace(/ {2,}/g, ' ');

		target.value = v;
		var c = Math.min( maxLength, cursorStart + 1 );

		target.setSelectionRange( c, c );
	}

}





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
			'name' => 'has_floating_labels',
			'type' => 'toggle',
			'label' => 'Label flottant (ils sont placés en placeholder puis se placent au dessus des champs lors du focus)',
			'default_value' => "0",
			"description" => "",
		];

		$fields['form_basics']['fields'][] = [
			'name' => 'display_mentions_legales',
			'type' => 'toggle',
			'label' => 'Affichage des mentions légales',
			'default_value' => "0",
			"description" => "Ces mentions légales seront affichées en bas du formulaire, sous le bouton. Un %s dans le texte sera remplacé par le label du bouton.",
		];


		$fields['form_basics']['fields'][] = [
			'name' => 'mentions_legales',
			'type' => 'textarea',
			'use_editor' => true,
			'label' => 'Texte des mentions légales',
			'default_value' => 'En cliquant sur &laquo;&nbsp;%s&nbsp;&raquo; bla bla',
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




	public function jauge_settings( $position, $form_id ) {
		if ( $position == 25 ) {
			?>

			<li class="gp_jauge_start_setting field_setting">
				<label for="jauge_start" style="display:inline;">
					Valeur de départ
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
					HTML affiché. Un %s sera remplacé par le nombre de signataires.
				</label>
				<textarea id="jauge_text" oninput="SetFieldProperty('jauge_text', this.value);"></textarea>
			</li>


			<li class="gp_jauge_fgcolor_setting field_setting">
				<label for="gp_jauge_fgcolor_value" style="display:inline;">
					Couleur de la jauge (code CSS)
				</label>
				<input type="text" id="jauge_fgcolor" oninput="SetFieldProperty('jauge_fgcolor', this.value);"/>
			</li>


			<li class="gp_jauge_bgcolor_setting field_setting">
				<label for="gp_jauge_bgcolor_value" style="display:inline;">
					Couleur du fond de la jauge (code CSS)
				</label>
				<input type="text" id="jauge_bgcolor" oninput="SetFieldProperty('jauge_bgcolor', this.value);"/>
			</li>

			<?php
		}
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