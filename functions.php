<?php

/**
 * Additional code for the child theme goes in here.
 */

add_action( 'wp_enqueue_scripts', 'enqueue_child_styles', 99);
function enqueue_child_styles() {
	$css_creation = filectime(get_stylesheet_directory() . '/style.css');

    wp_enqueue_script( 'validator', get_stylesheet_directory_uri() . '/vendor/validator.js', array('jquery'), $css_creation, true );
    wp_enqueue_script( 'mask', get_stylesheet_directory_uri() . '/vendor/jquery.mask.min.js', array('jquery'), $css_creation, true );
    wp_enqueue_script( 'bundle', get_stylesheet_directory_uri() . '/dist/bundle.js', array('jquery','validator'), $css_creation, true );
    wp_localize_script( 'bundle', 'adminAjaxUrl', admin_url( 'admin-ajax.php' ) );
    wp_localize_script( 'bundle', 'lang', ICL_LANGUAGE_CODE);

}

add_action( 'after_setup_theme', 'set_locale' );
function set_locale() {
    load_child_theme_textdomain( 'planet4-child-theme-luxembourg', get_stylesheet_directory() . '/languages' );
}


// submit du formulaire
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


                $to = ["renaud@qodop.com"];
                //$to[] = ["anais.hector@greenpeace.org",""];
                $subject = $request['selection'];

                if(in_array($subject, ["Agriculture / OGM", "Climat", "Forêt", "Océan", "Toxique", "HS Autre", "Poser une question sur Greenpeace" ,"Poser une question aux Relations Presse"] ) ) {
                    $to[] = "renaud+contact@qodop.com";
                    //$to[] = "contact.luxembourg@greenpeace.org";
                } else {
                    $to[] = "renaud+membre@qodop.com";
                    //$to[] = "membres.lu@greenpeace.org";
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

                if (!empty(trim($request['email'])) && preg_match("/^(.*<)?(?<email>[a-zA-Z0-9_\.\+-]+[^\.]@([a-zA-Z0-9-]+\.)+[a-zA-Z0-9]+)>?$/", trim($request['email']), $match)) {

                    $thankyouMsg = ($request['lang']) == 'fr' ? 'Merci %00N7E000000nl0L% pour votre message, nous le traitons dans les meilleurs délais.' :'Danke  %00N7E000000nl0L% für deine Nachricht, wir behandeln sie so schnell wie möglich.';
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


