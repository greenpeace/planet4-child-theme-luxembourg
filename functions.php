<?php

// PLUGIN GRAVITYFORM 


/**
 * Additional code for the child theme goes in here.
 */

// Adding a hook to allow to modify data before it is sent to EN
add_filter(
    'planet4_enform_data',
    function ( $data ) {
        if ( isset( $data['supporter']['questions']['question.3805'] )
            && $data['supporter']['questions']['question.3805'] === 'N'
        ) {
            unset( $data['supporter']['questions']['question.3805'] );
        }
        if ( empty( $data['supporter']['questions'] ) ) {
            unset( $data['supporter']['questions'] );
        }
        return $data;
    }
);

add_action( 'wp_enqueue_scripts', 'enqueue_child_styles', 99);
function enqueue_child_styles() {
    $css_creation = filectime(get_stylesheet_directory() . '/dist/bundle.js');

    wp_enqueue_script( 'validator', get_stylesheet_directory_uri() . '/vendor/validator.js', array('jquery'), $css_creation, true );
    wp_enqueue_script( 'mask', get_stylesheet_directory_uri() . '/vendor/jquery.mask.min.js', array('jquery'), $css_creation, true );
    wp_enqueue_script( 'bundle', get_stylesheet_directory_uri() . '/dist/bundle.js', array('jquery','validator'), $css_creation, true );
    wp_localize_script( 'bundle', 'p4lux', [
      'adminAjaxUrl' => admin_url( 'admin-ajax.php' ),
      'lang' => ICL_LANGUAGE_CODE,
    ] );
}

add_action( 'after_setup_theme', 'set_locale' );
function set_locale() {
    load_child_theme_textdomain( 'planet4-child-theme-luxembourg', get_stylesheet_directory() . '/languages' );
}


// contact form submit
add_action('wp_ajax_nopriv_form_submit', 'gpf_form_submit');
add_action('wp_ajax_form_submit', 'gpf_form_submit');
function gpf_form_submit() {




    $data = [
        'success' => true
    ];

    if (!empty($_POST['form_id'])) {
        $post_id = absint($_POST['form_id']);

        if ($post_id > 0) {

            $request = $_POST;

            foreach ($request as $k => $v) {
                $request[$k] = stripslashes($v);
            }

            foreach ($request as $k => $v) {
                $request[$k] = strip_tags($v);
            }


            // on envoie les emails à l'administrateur
            register_shutdown_function(function($request, $form, $app) {

                $dev = 0;
                if(strpos(get_site_url(), 'dev.gp.lu') !== false
                    || strpos(get_site_url(), 'k8s.p4.greenpeace.org') !== false)
                    $dev = '1';

                $form_target_text = "
                Prénom : %00N7E000000nl0L%
                Nom : %00N7E000000nl0Q%
                Email : %email%
                Téléphone : %phone%
                Langue : %lang%
                Adhérent : %is_adherent%
                Numéro adhérent : %numadherent%
                Sujet : %selection%
                Bénéficiaire : %beneficiaire%
                IBAN : %iban%
                BIC : %bic%
                Ancienne adresse : %old_adresse%
                Nouvelle adresse : %new_adresse%
                Ancien tél : %old_phone%
                Nouveau tél : %new_phone%
                Ancien mobile : %old_phone_mobile%
                Nouveau mobile : %new_phone_mobile%
                Ancien mail : %old_email%
                Nouveau mail : %new_email%
                Message : %message%";

                $body = preg_replace_callback('/%([0-9a-zA-Z_]+)%/', function($match) use ($request) {
                    return isset($request[$match[1]]) ? $request[$match[1]] : '';
                }, $form_target_text);

                $to = $dev ? ["renaud@qodop.com"] : ["anais.hector@greenpeace.org"];
                $subject = 'Un message du formulaire de contact greenpeace.lu - ' . $request['selection'];

                if(in_array($subject, ["Agriculture / OGM", "Climat", "Forêt", "Océan", "Toxique", "HS Autre", "Poser une question sur Greenpeace" ,"Poser une question aux Relations Presse"] ) ) {
                    $to[] = $dev ? "renaud+contact@qodop.com" : "contact.luxembourg@greenpeace.org";
                } else {
                    $to[] =  $dev ? "renaud+membre@qodop.com" : "membres.lu@greenpeace.org";
                }

                $body = preg_replace_callback('/%([0-9a-zA-Z_]+)%/', function($match) use ($request) {
                    $value = '';
                    if (isset($request[$match[1]])) {
                        $value = $request[$match[1]];

                        if ($value == "0" || $value == "1") {
                            $value = !!$value ? 'oui' : 'non';
                        }
                    }
                    return $value;
                }, $body);
                $headers = 'From: Greenpeace Luxembourg <noreply@greenpeace.lu>' . "\r\n";

                $res = wp_mail( $to, $subject, $body, $headers );

            }, $request, $post, $app);




            // on envoie les emails à l'internaute
            register_shutdown_function(function($request, $form, $app) {

                $dev = 0;
                if(strpos(get_site_url(), 'dev.gp.lu') !== false
                    || strpos(get_site_url(), 'k8s.p4.greenpeace.org') !== false)
                    $dev = '1';

                if (!empty(trim($request['email'])) && preg_match("/^(.*<)?(?<email>[a-zA-Z0-9_\.\+-]+[^\.]@([a-zA-Z0-9-]+\.)+[a-zA-Z0-9]+)>?$/", trim($request['email']), $match)) {

                    $thankyouMsg = ($request['lang']) == 'fr' ? 'Bonjour %00N7E000000nl0L% %00N7E000000nl0Q%, <br><br> Nous accusons bonne réception de votre message et y répondrons dans les meilleurs délais.<br><br> Bien à vous,<br>L\'équipe Greenpeace Luxembourg.' :'Hallo  %00N7E000000nl0L% %00N7E000000nl0Q%, <br><br> Wir bestätigen den Erhalt Ihrer Nachricht und  werden uns schnellstmöglich um Ihr Anliegen kümmern.<br><br>Mit freundlichen Grüßen,<br>Das Greenpeace Luxemburg-Team';
                    $to = $match['email'];
                    $subject = ($request['lang'] == 'fr') ? 'Votre message à Greenpeace Luxembourg ' : 'Ihre Nachricht an Greenpeace Luxemburg';
                    $subject = ( ($request['selection'] != '') ? $subject  . ' - ' . $request['selection'] : $subject );
                    $body = preg_replace_callback('/%([0-9a-zA-Z_]+)%/', function($match) use ($request) {
                    $value = '';
                    if (isset($request[$match[1]])) {
                        $value = $request[$match[1]];

                        if ($value == "0" || $value == "1") {
                            $value = !!$value ? 'oui' : 'non';
                        }
                    }
                    return $value;
                    }, $thankyouMsg);
                    $headers = 'From: Greenpeace Luxembourg <noreply@greenpeace.lu>' . "\r\n";
                    $headers .= "Content-type: text/html; charset=\"UTF-8\"; format=flowed \r\n";
                    $headers .= "Mime-Version: 1.0 \r\n";
                    $headers .= "Content-Transfer-Encoding: quoted-printable \r\n";


                    $res = wp_mail( $to, $subject, $body, $headers );

                }
            }, $request, $post, $app);

        }
    }


    $data = json_encode($data);

    if (!empty($_GET['callback'])) {
        $data = $_GET['callback'] . '(' . $data . ')';
    }

    die($data);
}


// shortcode for CTA button
// Filter Functions with Hooks
function gplux_cta_mce_button() {
  // Check if user have permission
  if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
    return;
  }
  // Check if WYSIWYG is enabled
  if ( 'true' == get_user_option( 'rich_editing' ) ) {
    add_filter( 'mce_external_plugins', 'gplux_cta_tinymce_plugin' );
    add_filter( 'mce_buttons', 'gplux_cta_register_mce_button' );
  }
}
add_action('admin_head', 'gplux_cta_mce_button');

// Function for new button
function gplux_cta_tinymce_plugin( $plugin_array ) {
  $plugin_array['gplux_cta_mce_button'] = get_stylesheet_directory_uri() .'/mce_plugins/cta_plugin.js';
  return $plugin_array;
}

// Register new button in the editor
function gplux_cta_register_mce_button( $buttons ) {
  array_push( $buttons, 'gplux_cta_mce_button' );
  return $buttons;
}

function gplux_cta_shortcode( $atts, $content ) {

    // get the options defined for this shortcode
    extract( shortcode_atts( array(
        'text'     => '',
        'link'     => '',
        'type'        => '',
        'height'        => '',
        'width'        => '',
        'target'     => '',
        'align'     => '',
    ), $atts ) );
    $btn_type = ($type == 'action') ? 'btn-primary' : (($type == 'donate') ? 'btn-donate' : 'btn-secondary' );

    return '<p style="text-align:'. $align .'"><a href="' . $link . '" target="' . $target . '" class="btn ' . $btn_type . ' ' . $height . ' page-header-btn ' . $width . '">' . $text . '</a></p><p>&nbsp;</p>';

}
add_shortcode( 'cta', 'gplux_cta_shortcode' );


// shortcode for Responsively button
// Filter Functions with Hooks
function gplux_responsively_mce_button() {
  // Check if user have permission
  if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
    return;
  }
  // Check if WYSIWYG is enabled
  if ( 'true' == get_user_option( 'rich_editing' ) ) {
    add_filter( 'mce_external_plugins', 'gplux_responsively_tinymce_plugin' );
    add_filter( 'mce_buttons', 'gplux_responsively_register_mce_button' );
  }
}
add_action('admin_head', 'gplux_responsively_mce_button');

// Function for responsively iframe editor button
function gplux_responsively_tinymce_plugin( $plugin_array ) {
  $plugin_array['gplux_responsively_mce_button'] = get_stylesheet_directory_uri() .'/mce_plugins/responsively_plugin.js';
  return $plugin_array;
}

// Register new button in the editor
function gplux_responsively_register_mce_button( $buttons ) {
  array_push( $buttons, 'gplux_responsively_mce_button' );
  return $buttons;
}

function gplux_responsively_shortcode( $atts, $content ) {

    // get the options defined for this shortcode
    extract( shortcode_atts( array(
        'src'     => ''
    ), $atts ) );

    return '<div class="responsively-container"><iframe src="'.$src.'" frameborder="0" allowfullscreen scrolling="no"></iframe></div>';

}
add_shortcode( 'responsively', 'gplux_responsively_shortcode' );

define('THIS_IS_LUXEMBOURG', 1);

// APPELER LES ASSETS FR POUR RECUP ICONS define('GPFGF_DIR_URL', 'wp-content/themes/planet4-child-theme-luxembourg/plugins/gravityforms-gpf/'):

if (defined('GF_PLUGIN_DIR_PATH')) {
    require_once __DIR__ . "/plugins/gravityforms-gpf/index.php";
};
