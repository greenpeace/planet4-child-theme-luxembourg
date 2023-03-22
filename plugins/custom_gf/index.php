<?php
/*
* Plugin Name: Gravity Forms GPF
* Description: Champs spécifiques, CSS admin, etc
*/


/*
* Add-on à installer :

https://wordpress.org/plugins/smart-phone-field-for-gravity-forms/

*/



require __DIR__ . '/vendor/autoload.php';

mb_internal_encoding('UTF-8'); // IMPORTANT

new Greenpeacefrance\Gravityforms\Plugin();

define( 'GPFGF_DIR_URL', plugin_dir_url( __FILE__ ) );

function cleanCrmValue($value, $maxlength, $keep_numbers = true, $encoding = 'UTF-8') {


	// on va découper chaque caractère et les regarder individuellement.
	// Attention à bien être en utf8 (utiliser mb_internal_encoding() pour être sûr)

	$array = preg_split('//u', $value, $maxlength + 1);

	if (! $array) {
		return '';
	}

	$result = [];

	$length = min( count( $array ), $maxlength );


	for ($i = 0; $i < $length; $i++) {
		$c = $array[ $i ];
		if ($c === "") {
			continue;
		}

		$char = mb_ord($c, $encoding);

		if (
			$char === 32 // espace
			|| $char === 39 // apostrophe
			// || $char === 44 // virgule
			|| $char === 45 // tiret
			|| ( $keep_numbers && ( $char >= 48 && $char <= 57 ) ) // 0 à 9
			|| ( $char >= 65 && $char <= 90 ) // A à Z
			|| ( $char >= 97 && $char <= 122 ) // a à z
			|| ( $char >= 192 && $char <= 214 ) // majuscules avec accents
			|| ( $char >= 216 && $char <= 246 ) // majuscules et minuscules avec accents
			|| ( $char >= 248 && $char <= 255 ) // minuscules avec accents
		) {

			$result[] = mb_chr( $char, $encoding);
		}
	}

	$value = trim( implode( '', $result ) );

	return $value;

}

