<?php
/*
 * Plugin Name: SNAE Ecommerce
 * Description: A custom built ecommerce plugin for the Southern Nature Art Exhibition
 * Author: Ben Watkins
 * Author URI: http://benwtks.com/
 * Version: 0.1
 */

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
