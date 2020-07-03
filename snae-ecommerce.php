<?php
/*
 * Plugin Name: SNAE Ecommerce
 * Description: A custom built ecommerce plugin for the Southern Nature Art Exhibition
 * Author: Ben Watkins
 * Author URI: http://benwtks.com/
 * Version: 0.1
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;
require_once( __DIR__ . '/artist.php');
require_once( __DIR__ . '/workshop.php');

function snae_ecommerce_single_template( $template ) {
	global $post;

	if ($post->post_type === 'artist') {
		$template = dirname( __FILE__ ) . '/templates/single-artist.php';
	} else if ( is_post_type_archive('workshop') ) {
		$template = dirname( __FILE__ ) . '/templates/archive-workshop.php';
	} else if ($post->post_type === 'workshop') {
		$template = dirname( __FILE__ ) . '/templates/single-workshop.php';
	}
	
	return $template;
};

add_filter( 'single_template', 'snae_ecommerce_single_template' );

function snae_ecommerce_archive_template( $template ) {
	global $post;

	if (is_archive() && get_post_type($post) == 'workshop') {
		$template = dirname( __FILE__ ) . '/templates/archive-workshop.php';
	} else if (is_archive() && get_post_type($post) == 'artist') {
		$template = dirname( __FILE__ ) . '/templates/archive-artist.php';
	}

	return $template;
}

add_filter( 'archive_template', 'snae_ecommerce_archive_template' );

function snae_ecommerce_scripts() {
	wp_enqueue_script( 'workshop_photo', plugins_url('/js/workshop_photo.js', __FILE__), array(), _S_VERSION);
}

add_action( 'wp_enqueue_scripts', 'snae_ecommerce_scripts' );

function snae_ecommerce_get_pages_array() {
	$query = new WP_Query( array(
		'post_type' => 'page',
		'posts_per_page' => -1
	));

	$array = $query->posts;
	return wp_list_pluck( $array, 'post_title', 'ID' );
}

function snae_ecommerce_plugin_options() {
	Container::make( 'theme_options', __( 'Ecommerce Options' ) )
		->add_fields( array(
			Field::make( 'checkbox', 'crb_ecommerce_raise_details', __( 'Raise the right hand side details box to top' ) )
				->set_option_value( 'yes' ),
			Field::make( 'text', 'crb_workshop_refund_title', 'Workshop Refund guarantee title'),
			Field::make( 'select', 'crb_workshop_refund_policy', 'Workshop Refund policy page')
				->add_options( 'snae_ecommerce_get_pages_array' ),
			Field::make( 'complex', 'crb_standard_workshop_guarantees', 'Fixed workshop guarantees (shown on all workshops)' )
				->add_fields( array(
					Field::make( 'text', 'crb_standard_workshop_guarantee', 'Standard guarantee'),
				))
		));
}

add_action( 'carbon_fields_register_fields', 'snae_ecommerce_plugin_options' );

function snae_ecommerce_get_workshop_preview($workshop) {
	$photo_url = snae_ecommerce_get_first_workshop_photo_url($workshop, 'workshop-preview');
	$title = get_the_title($workshop);
	$link = get_the_permalink($workshop);
	$desc = carbon_get_post_meta($workshop, 'crb_workshop_short_desc');

	return 
		'<div class="workshop-preview">
			<img class="preview-photo" src="' . $photo_url . '" alt="workshop photo">
	<div class="details">
		<h3 class="title"><a href="' . $link . '">' . $title . '</a></h3>
		<p>' . $desc . '</p>
	</div>
</div>';
}

function snae_ecommerce_get_artist_preview($artist) {
	$photo_ID = carbon_get_post_meta($artist, 'crb_artist_photo');
	$photo_url = wp_get_attachment_image_url($photo_ID);
	$title = get_the_title($artist);
	$link = get_the_permalink($artist);
	$desc = carbon_get_post_meta($artist, 'crb_short_bio');

	return 
		'<div class="artist-preview">
			<img class="preview-photo" src="' . $photo_url . '" alt="artist photo">
	<div class="details">
		<h3 class="title"><a href="' . $link . '">' . $title . '</a></h3>
		<p>' . $desc . '</p>
	</div>
</div>';
}
