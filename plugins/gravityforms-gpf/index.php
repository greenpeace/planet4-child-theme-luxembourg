<?php
/*
* Plugin Name: Gravity Forms GPF
* Description: Champs spécifiques, CSS admin, etc
*/


/*
* Add-on à installer :

https://wordpress.org/plugins/smart-phone-field-for-gravity-forms/

*/


spl_autoload_register(function ($class_name) {
	if (substr($class_name, 0, 30) === 'Greenpeacefrance\\Gravityforms\\') {
		$class = str_replace('\\', '/', substr($class_name, 30) );
		require __DIR__ . '/src/' . $class . '.php';
	}
});


mb_internal_encoding('UTF-8'); // IMPORTANT

$plugin = new Greenpeacefrance\Gravityforms\Plugin();



// chargement des plugins et champs
$hook = 'gform_loaded';
if ( defined('THIS_IS_LUXEMBOURG') ) {
	$hook = 'init';
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
	wp_enqueue_style( 'gpfgf-default-styles', GPFGF_DIR_URL . 'assets/style.css' );
	wp_enqueue_script( 'gpfgf-default-script', GPFGF_DIR_URL . 'assets/script.js', ['jquery'], null, true );
}, 10, 2 );


add_filter('script_loader_tag', function($tag, $handle) {

	if ( $handle === 'gpfgf-default-script' ) {
		return str_replace( ' src', ' async="async" src', $tag );
	}

	return $tag;
}, 10, 2);

add_action( 'gform_field_standard_settings', function($position, $form_id) {

	$form = GFAPI::get_form($form_id);


	if ($position === 5) {

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


// 		add_action( 'gform_submit_button', function($input, $form) {

// 			// on peut éventuellement changer le bouton
// 			// pour par exemple ajouter du JS pour gérer un état "en cours d'envoi"


// //			wp_mail( 'hugo.poncedeleon@greenpeace.org', 'submit', $input . PHP_EOL . print_r($form, true) );

// 			return $input;

// 		}, 10, 2 );


add_action( 'gform_editor_js', function() {
	?>
	<script type='text/javascript'>

		fieldSettings.submit += ', .after_submit_text_setting';

		document.addEventListener('gform_load_field_settings', function(event, field, form){

			document.querySelector( '#after_submit_text' ).value = rgar( field, 'after_submit_text' ) || '';
		});
	</script>
	<?php
});



// Les pictos pour les boutons GP
add_action( 'admin_head', function( ) {
	echo '<link rel="stylesheet" type="text/css" href="'. GPFGF_DIR_URL .'assets/pictos.css" />';
});


// modification des messages de validation de téléphone
// suppression des "X caractères maximum"
add_action( 'gform_register_init_scripts', [$plugin, 'form_javascript'] );


add_filter( 'gform_field_css_class', [ $plugin, 'field_class' ], 10, 3 );

// add_filter( 'gform_field_content', [ $plugin, 'field_content'], 10, 5 );
add_filter( 'gform_field_content', [ $plugin, 'field_content'], 10, 5 );

add_filter( 'gform_field_choice_markup_pre_render', [$plugin, 'add_optgroup_to_select'], 10, 3 );


add_filter( 'gform_submit_button', [$plugin, 'submit_button'], 10, 2 );

// On peut modifier le placeholder du masque sur certains champs (pas le téléphone...)
// C'est bourrin et il faut une priorité basse (100) pour passer après les plugins
// qui ajoutent leurs masques.
// Bien sûr, on peut aussi modifer de toute autre manière le plugin MaskedInput
// add_filter('gform_input_mask_script', function($script, $form_id, $field_id, $mask) {
// 	return preg_replace("/\.mask\('([^']+)'\)/", ".mask('$1', {placeholder: '░'})", $script);

// }, 100, 4);




add_action( 'gform_field_standard_settings', [$plugin, 'jauge_settings'], 10, 2 );
// add_action( 'gform_field_standard_settings', [$plugin, 'div_field_options'], 10, 2 );
// add_action( 'gform_editor_js', [$plugin, 'editor_script'] );


// add_filter( 'gform_field_content', function( $content, $field, $value, $lead_id, $form_id ) {
// 	if ($field->type === 'gp_html') {
// 		return '[aa]' . $content . '[/aa]';
// 	}

// 	return $content;
// }, 10, 5 );










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

