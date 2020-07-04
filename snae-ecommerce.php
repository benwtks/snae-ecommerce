<?php
/*
 * Plugin Name: SNAE Ecommerce
 * Description: A custom built ecommerce plugin for the Southern Nature Art Exhibition
 * Author: Ben Watkins
 * Author URI: http://benwtks.com/
 * Version: 0.1
 */
require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
require_once( __DIR__ . '/templates.php');
require_once( __DIR__ . '/scripts.php');
require_once( __DIR__ . '/artist.php');
require_once( __DIR__ . '/workshop.php');
require_once( __DIR__ . '/plugin-options.php');
require_once( __DIR__ . '/checkout.php');

function snae_ecommerce_get_pages_array() {
	$query = new WP_Query( array(
		'post_type' => 'page',
		'posts_per_page' => -1
	));

	$array = $query->posts;
	return wp_list_pluck( $array, 'post_title', 'ID' );
}

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
