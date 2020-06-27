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
	} else if ($post->post_type === 'workshop') {
		$template = dirname( __FILE__ ) . '/templates/single-workshop.php';
	}
	
	return $template;
};

add_filter( 'single_template', 'snae_ecommerce_single_template' );

function snae_ecommerce_scripts() {
	wp_enqueue_script( 'workshop_photo', plugins_url('/js/workshop_photo.js', __FILE__), array(), _S_VERSION);
}

add_action( 'wp_enqueue_scripts', 'snae_ecommerce_scripts' );

function snae_ecommerce_plugin_options() {
	Container::make( 'theme_options', __( 'Ecommerce Options' ) )
		->add_fields( array(
			Field::make( 'checkbox', 'crb_ecommerce_raise_details', __( 'Raise the right hand side details box to top' ) )
			    ->set_option_value( 'yes' )
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
