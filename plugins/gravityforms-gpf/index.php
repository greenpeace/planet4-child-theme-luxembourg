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

/*
spl_autoload_register(function ($class_name) {
	if (substr($class_name, 0, 30) === 'Greenpeacefrance\\Gravityforms\\') {
		$class = str_replace('\\', '/', substr($class_name, 30) );
		require __DIR__ . '/src/' . $class . '.php';
	}
});
*/

mb_internal_encoding('UTF-8'); // IMPORTANT



$plugin = new Greenpeacefrance\Gravityforms\Plugin();



// chargement des plugins et champs
$hook = 'gform_loaded';
if ( defined('THIS_IS_LUXEMBOURG') ) {
	$hook = 'init';
	define( 'GPFGF_DIR_URL', '/luxembourg/wp-content/themes/planet4-child-theme-luxembourg/plugins/gravityforms-gpf/' );
}
else {
	define( 'GPFGF_DIR_URL', plugin_dir_url( __FILE__ ) );
}

add_action( $hook, [$plugin, 'loaded'], 5 );



// modif form (exemple: changer la classe CSS)
add_filter( 'gform_pre_render', [$plugin, 'pre_render'], 10, 3 );

// Ajout du groupe de champs Greenpeace
add_filter( 'gform_field_groups_form_editor', [$plugin, 'field_groups_editor'], 10, 1 );

// ajoute du toggle Enable Ajax
add_filter( 'gform_form_settings_fields', [$plugin, 'global_settings'], 10, 2);

add_action( 'gfiframe_form_footer', function() {
	// Action appelée dans le footer de la page (embed)
	// quand le formulaire est affiché via iframe.
	// On ajoute un peu de marge sur les côtés,
	// sinon les inputs débordent et sont rognés.
	echo '<style type="text/css">body {padding: 0 2px 0 1px;}</style>';
} );


// Ajax automatique sur les formulaires
// Attention, ça casse la preview. Pour que la preview fonctionne,
// Ajax doit être désactivé (dans les Forms Settings)
add_filter( 'gform_form_args', [$plugin, 'form_args'] );

add_filter( 'gform_custom_merge_tags', [ $plugin, 'custom_merge_tags' ], 10, 4 );
add_filter( 'gform_replace_merge_tags', [ $plugin, 'replace_merge_tags' ], 10, 3 );
add_filter( 'gform_input_masks', [ $plugin, 'add_input_mask' ] );

add_action( 'gform_enqueue_scripts', function($form, $is_ajax) {

	// if ( ! defined('THIS_IS_LUXEMBOURG') ) {
		wp_enqueue_style( 'gpfgf-default-styles', GPFGF_DIR_URL . 'assets/style.css' );
	// }

	wp_enqueue_script( 'gpfgf-default-script', GPFGF_DIR_URL . 'assets/script.js', ['jquery'], null, true );


	wp_localize_script( 'gpfgf-default-script', 'gpfgf', [
		'baseUrl' => GPFGF_DIR_URL,
	] );


}, 1, 2 );


add_action('wp_footer', function() {
	echo <<<END
	<div style="display:none" class="svg-catalog-portal">
	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
	<defs>


	<g id="facebook-logo">
	<path fill="#1877F2" d="M24,12.1c0-6.6-5.4-12-12-12S0,5.4,0,12.1c0,6,4.4,11,10.1,11.9v-8.4h-3v-3.5h3V9.4c0-3,1.8-4.7,4.5-4.7
	C16,4.8,17.3,5,17.3,5v3h-1.5c-1.5,0-2,0.9-2,1.9v2.3h3.3l-0.5,3.5h-2.8v8.4C19.6,23,24,18.1,24,12.1L24,12.1z"/>
	</g>


	<g id="twitter-logo">
	<path fill="#1da1f2" d="M23.954 4.569c-0.885 0.389-1.83 0.654-2.825 0.775 1.014-0.611 1.794-1.574 2.163-2.723-0.951 0.555-2.005 0.959-3.127 1.184-0.896-0.959-2.173-1.559-3.591-1.559-2.717 0-4.92 2.203-4.92 4.917 0 0.39 0.045 0.765 0.127 1.124-4.090-0.193-7.715-2.157-10.141-5.126-0.427 0.722-0.666 1.561-0.666 2.475 0 1.71 0.87 3.213 2.188 4.096-0.807-0.026-1.566-0.248-2.228-0.616v0.061c0 2.385 1.693 4.374 3.946 4.827-0.413 0.111-0.849 0.171-1.296 0.171-0.314 0-0.615-0.030-0.916-0.086 0.631 1.953 2.445 3.377 4.604 3.417-1.68 1.319-3.809 2.105-6.102 2.105-0.39 0-0.779-0.023-1.17-0.067 2.189 1.394 4.768 2.209 7.557 2.209 9.054 0 13.999-7.496 13.999-13.986 0-0.209 0-0.42-0.015-0.63 0.961-0.689 1.8-1.56 2.46-2.548z"></path>
	</g>


	<g id="whatsapp-logo">
	<path fill="#25D366" d="M20.461,3.488C18.216,1.238,15.23,0,12.047,0C5.494,0,0.16,5.334,0.16,11.892
	c0,2.094,0.548,4.144,1.589,5.944L0.061,24l6.305-1.654c1.739,0.946,3.694,1.447,5.681,1.447h0.005l0,0
	c6.554,0,11.892-5.334,11.892-11.892C23.943,8.723,22.706,5.737,20.461,3.488L20.461,3.488z"/>
	<path fill="#FFFFFF" d="M17.63,14.461c-0.307-0.156-1.824-0.899-2.106-1.001c-0.282-0.103-0.486-0.156-0.694,0.154
	c-0.204,0.306-0.797,1.003-0.977,1.212c-0.181,0.204-0.36,0.232-0.667,0.077c-0.306-0.157-1.302-0.481-2.479-1.532
	c-0.914-0.816-1.535-1.827-1.715-2.135c-0.181-0.306-0.021-0.476,0.136-0.627c0.141-0.135,0.306-0.359,0.46-0.54
	c0.156-0.178,0.205-0.306,0.307-0.515C9.996,9.351,9.947,9.17,9.87,9.016C9.792,8.86,9.175,7.343,8.922,6.726
	C8.674,6.123,8.416,6.205,8.227,6.196c-0.178-0.01-0.383-0.01-0.587-0.01S7.101,6.265,6.818,6.57
	c-0.282,0.306-1.08,1.055-1.08,2.572s1.103,2.979,1.259,3.189c0.156,0.204,2.172,3.321,5.265,4.651
	c0.734,0.316,1.308,0.507,1.754,0.652c0.739,0.233,1.409,0.198,1.94,0.121c0.593-0.087,1.823-0.744,2.079-1.464
	c0.258-0.719,0.258-1.337,0.18-1.462c-0.072-0.136-0.276-0.215-0.588-0.37L17.63,14.461z"/>
	</g>


	<g id="mailto-logo">
	<circle fill="#73be31" cx="12" cy="12" r="12"/>
	<path fill="#FFFFFF" d="M10.032,13.124c0.697,0.932,1.708,1.493,2.774,1.646c1.067,0.155,2.194-0.099,3.128-0.796
		c0.167-0.126,0.326-0.264,0.46-0.399l2.104-2.104c0.817-0.846,1.21-1.933,1.191-3.011c-0.019-1.078-0.448-2.151-1.287-2.959
		c-0.823-0.795-1.889-1.189-2.95-1.183c-1.048,0.006-2.097,0.401-2.907,1.183L11.329,6.71c-0.275,0.273-0.277,0.719-0.002,0.994
		C11.6,7.978,12.045,7.98,12.32,7.707l1.201-1.194c0.541-0.522,1.239-0.785,1.939-0.79c0.71-0.004,1.416,0.258,1.967,0.789
		c0.559,0.54,0.845,1.253,0.857,1.973s-0.249,1.444-0.78,1.993l-2.111,2.112c-0.08,0.081-0.186,0.173-0.301,0.26
		c-0.621,0.465-1.371,0.634-2.084,0.531c-0.714-0.104-1.386-0.477-1.851-1.099c-0.232-0.311-0.672-0.374-0.984-0.142
		C9.862,12.373,9.799,12.813,10.032,13.124L10.032,13.124z M13.968,10.876c-0.697-0.932-1.707-1.494-2.774-1.647
		s-2.195,0.1-3.128,0.797c-0.167,0.126-0.326,0.264-0.46,0.399l-2.104,2.105c-0.817,0.846-1.21,1.933-1.192,3.011
		c0.018,1.078,0.449,2.151,1.287,2.959c0.824,0.796,1.889,1.189,2.951,1.183c1.048-0.006,2.097-0.4,2.907-1.183l1.21-1.211
		c0.275-0.274,0.275-0.719,0-0.994c-0.274-0.272-0.719-0.274-0.993,0l-1.192,1.194c-0.541,0.521-1.238,0.785-1.939,0.789
		c-0.709,0.005-1.417-0.258-1.967-0.789c-0.559-0.539-0.845-1.253-0.857-1.973s0.25-1.443,0.78-1.993l2.111-2.111
		c0.081-0.082,0.185-0.174,0.301-0.26c0.622-0.465,1.371-0.633,2.084-0.531s1.385,0.476,1.85,1.098
		c0.231,0.311,0.673,0.375,0.984,0.142C14.138,11.627,14.201,11.188,13.968,10.876z"/>
	</g>

	</defs>
	</svg>
	</div>
	END;
});

add_filter('script_loader_tag', function($tag, $handle) {

	if ( $handle === 'gpfgf-default-script' ) {
		return str_replace( ' src', ' async="async" src', $tag );
	}

	return $tag;
}, 10, 2);
/*
add_action( 'gform_field_standard_settings', function($position, $form_id) {


	if ($position === 5) {

		$form = GFAPI::get_form($form_id);

		$button = $form['button'];

		echo '
		<li class="after_submit_text_setting field_setting">
			<label for="after_submit_text" class="section_label">
			Texte du bouton lors du Submit
			</label>
			<input type="text" id="after_submit_text" oninput="form.button.after_submit_text=this.value" value="'.($button['after_submit_text'] ?? "").'"/>
		</li>';
	}

}, 10, 2);
*/

// 		add_action( 'gform_submit_button', function($input, $form) {

// 			// on peut éventuellement changer le bouton
// 			// pour par exemple ajouter du JS pour gérer un état "en cours d'envoi"


// //			wp_mail( 'hugo.poncedeleon@greenpeace.org', 'submit', $input . PHP_EOL . print_r($form, true) );

// 			return $input;

// 		}, 10, 2 );



// Les pictos pour les boutons GP
add_action( 'admin_head', function( ) {
	echo '<link rel="stylesheet" type="text/css" href="'. GPFGF_DIR_URL .'assets/admin.css"/>
<link rel="stylesheet" type="text/css" href="'. GPFGF_DIR_URL .'assets/pictos.css" />
	';
});


/*
// Utilisé pour la modification du Quiz, mais on désactive (ça marche pas)
add_action( 'admin_footer', function( ) {
	// On ne peut pas utiliser admin_enqueue_scripts parce que
	// GF est configuré en mode No Conflict et donc
	// ne charge pas les autres JS.
	$screen = get_current_screen();

	if ($screen->id === 'toplevel_page_gf_edit_forms') {
		echo '<script type="text/javascript" src="'.GPFGF_DIR_URL . 'assets/admin.js" id="gpfgf-admin-js"></script>';
	}

});
*/

// modification des messages de validation de téléphone
// suppression des "X caractères maximum"
add_action( 'gform_register_init_scripts', [$plugin, 'form_javascript'], 1 );

add_filter( 'gform_field_validation', [ $plugin, 'field_validation' ], 10, 4 );
add_filter( 'gform_field_css_class', [ $plugin, 'field_class' ], 10, 3 );

// add_filter( 'gform_field_content', [ $plugin, 'field_content'], 10, 5 );

if ( ! is_admin() ) {
	add_filter( 'gform_field_content', [ $plugin, 'field_hotjar_masked'], 10, 5 );
	add_filter( 'gform_field_content', [ $plugin, 'field_maxlength'], 10, 5 );
	// add_filter( 'gform_field_content', [ $plugin, 'field_quiz_correct_answers'], 10, 5 );

}



add_filter( 'gform_field_choice_markup_pre_render', [$plugin, 'add_optgroup_to_select'], 10, 3 );


add_filter( 'gform_submit_button', [$plugin, 'submit_button'], 10, 2 );

// On peut modifier le placeholder du masque sur certains champs (pas le téléphone...)
// C'est bourrin et il faut une priorité basse (100) pour passer après les plugins
// qui ajoutent leurs masques.
// Bien sûr, on peut aussi modifer de toute autre manière le plugin MaskedInput
// add_filter('gform_input_mask_script', function($script, $form_id, $field_id, $mask) {
// 	return preg_replace("/\.mask\('([^']+)'\)/", ".mask('$1', {placeholder: '░'})", $script);

// }, 100, 4);




add_action( 'gform_field_standard_settings', [$plugin, 'field_settings'], 10, 2 );
add_action( 'gform_editor_js', [$plugin, 'field_settings_js']);


add_filter( 'gform_ajax_spinner_url', function($image_src, $form) {
	return GPFGF_DIR_URL . 'assets/spinner.svg';
}, 10, 2 );

// pour debug :
// add_action( 'gform_after_save_form', function($form_meta, $is_new, $deleted_fields ) {
// 	ob_end_clean();
// print_r($form_meta);
// print_r($is_new);
// print_r( $deleted_fields);
// }, 10, 3);


// add_action( 'gform_field_standard_settings', [$plugin, 'div_field_options'], 10, 2 );
// add_action( 'gform_editor_js', [$plugin, 'editor_script'] );


// add_filter( 'gform_field_content', function( $content, $field, $value, $lead_id, $form_id ) {
// 	if ($field->type === 'gp_html') {
// 		return '[aa]' . $content . '[/aa]';
// 	}

// 	return $content;
// }, 10, 5 );












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




// Log de debug
add_filter( 'gform_currency_pre_save_entry', function($currency, $form) {

	$form_id = $form['id'];

	foreach ( $form['fields'] as $field ) {
		$type = $field->type;
		$display_only = $field->displayOnly;
		$has_calculation = $field->has_calculation();
		$name = $field->label;
		$id = $field->id;

		\GFCommon::log_debug( "Save du Field {$name}(#{$id} - {$type})"
		. " pour Form " . $form_id . " :"
		. " DisplayOnly: " . ($display_only ? 'true' : 'false')
		. " | HasCalculation : " . ($has_calculation ? 'true' : 'false')
		);
	}




	return $read_value_from_post;
}, 10, 2 );