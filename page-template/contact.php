<?php /* Template Name: Contact Page */
/**
 * The template for displaying contact page.
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/views/page-mypage.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

use Timber\Timber;


$context        = Timber::get_context();
$post           = new P4_Post();
$page_meta_data = get_post_meta( $post->ID );

// Get Faqs

// $faqs = [1,2,3];

// if ( is_array( $faqs ) && $faqs ) {
//  foreach ( $faqs as $faq ) {
//      $faq_items[] = wp_get_post($faq);
//  }
//  $context['faqs'] = $faq_items;
// }


$context['post']                = $post;
$context['header_title']        = is_front_page() ? ( $page_meta_data['p4_title'][0] ?? '' ) : ( $page_meta_data['p4_title'][0] ?? $post->title );
$context['header_subtitle']     = $page_meta_data['p4_subtitle'][0] ?? '';
$context['header_description']  = wpautop( $page_meta_data['p4_description'][0] ?? '' ) ;
$context['header_button_title'] = $page_meta_data['p4_button_title'][0] ?? '';
$context['header_button_link']  = $page_meta_data['p4_button_link'][0] ?? '';
$context['page_category']       = is_front_page() ? 'Front Page' : ( $category->name ?? 'Unknown page' );
$context['social_accounts']     = $post->get_social_accounts( $context['footer_social_menu'] );

$background_image_id                = get_post_meta( get_the_ID(), 'background_image_id', 1 );
$context['background_image']        = wp_get_attachment_url( $background_image_id );
$context['background_image_srcset'] = wp_get_attachment_image_srcset( $background_image_id, 'full' );
$context['post_image_id']           = $page_meta_data['background_image_id'][0] ?? ( $page_meta_data['_thumbnail_id'][0] ?? '' );



if(isset($_GET['postmail'])){



    $mg_api = 'key-7ac56eafa099277910a553e92d4442dd';
    $mg_version = 'api.mailgun.net/v3/';
    $mg_domain = "mg.qodop.com";
    $mg_from_email = "contact@qodop.com";
    $mg_reply_to_email = "renaud@qodop.com";

    $mg_message_url = "https://".$mg_version.$mg_domain."/messages";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

    curl_setopt ($ch, CURLOPT_MAXREDIRS, 3);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_VERBOSE, 0);
    curl_setopt ($ch, CURLOPT_HEADER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);

    curl_setopt($ch, CURLOPT_USERPWD, 'api:' . $mg_api);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_POST, true);
    //curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_HEADER, false);

    //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_URL, $mg_message_url);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
            array(  'from'      => 'Renaud <' . 'renaud@qodop.com' . '>',
                    'to'        => ' <' . $mg_reply_to_email . '>',
                    'h:Reply-To'=> ' <' . $mg_reply_to_email . '>',
                    'subject'   => 'from Mailgun'.time(),
                    'html'      => 'Hello <b>Renaud !</b>',
                ));
    $result = curl_exec($ch);
    curl_close($ch);
    $res = json_decode($result,TRUE);
    print_r($res);

    $to = 'renaud@qodop.com';
    $subject = 'from sendmail'.time();
    $body = 'The email body content <b>From sendmail</b>';
    $headers = array('Content-Type: text/html; charset=UTF-8');

    $res = wp_mail( $to, $subject, $body, $headers );
    print_r($res);
    exit;

}

Timber::render( array( 'contact.twig' ), $context );

