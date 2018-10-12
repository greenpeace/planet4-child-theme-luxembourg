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
$context['social_accounts']     = $post->get_social_accounts( $context['footer_social_menu'] );

$background_image_id                = get_post_meta( get_the_ID(), 'background_image_id', 1 );
$context['background_image']        = wp_get_attachment_url( $background_image_id );
$context['background_image_srcset'] = wp_get_attachment_image_srcset( $background_image_id, 'full' );
$context['post_image_id']           = $page_meta_data['background_image_id'][0] ?? ( $page_meta_data['_thumbnail_id'][0] ?? '' );

Timber::render( array( 'contact.twig' ), $context );

