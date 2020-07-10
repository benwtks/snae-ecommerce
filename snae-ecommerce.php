<?php
/*
 * Plugin Name: SNAE Ecommerce
 * Description: A custom built ecommerce plugin for the Southern Nature Art Exhibition
 * Author: Ben Watkins
 * Author URI: http://benwtks.com/
 * Version: 0.5
 */

require_once( __FILE__  . 'vendor/autoload.php')
require_once( __DIR__ . '/templates.php');
require_once( __DIR__ . '/scripts.php');
require_once( __DIR__ . '/artist.php');
require_once( __DIR__ . '/workshop.php');
require_once( __DIR__ . '/plugin-options.php');
require_once( __DIR__ . '/checkout.php');

function snae_ecommerce_hidden_plugin_type() {
	register_post_type('pay',
		array(
			'labels' => array(
				'name' => __( 'Pay' ),
			),
			'public' => true,
			'show_ui' => false,
			'show_in_nav_menus' => false,
			'show_in_menu' => false,
			'show_in_admin_bar' => false
		)
	);
}

add_action( 'init', 'snae_ecommerce_hidden_plugin_type' );

function snae_ecommerce_create_payment_pages() {
	$cart_post = array(
		'post_title'     => 'Cart',
		'post_status'   => 'publish',
		'post_type'      => 'pay',
		'comment_status' => 'closed',
	);

	$checkout_post = array(
		'post_title'     => 'Checkout',
		'post_status'   => 'publish',
		'post_type'      => 'pay',
		'comment_status' => 'closed',
	);

	$id = wp_insert_post($cart_post);
	$id = wp_insert_post($checkout_post);
}

register_activation_hook( __FILE__, 'snae_ecommerce_create_payment_pages' );

function snae_ecommerce_delete_checkout_page() {
	$pay_posts = new WP_Query( array(
		'post_type' => 'pay',
		'posts_per_page' => -1
	));

	$ids = wp_list_pluck($pay_posts->posts, 'ID');

	foreach ($ids as $id) {
		wp_delete_post($id);
	}
}

register_deactivation_hook( __FILE__, 'snae_ecommerce_delete_checkout_page' );

function snae_ecommerce_add_image_size() {
	add_image_size( 'checkout', 150, 150, true);
	add_image_size( 'workshop-preview', 1000, 800, true);
}

add_action('init', 'snae_ecommerce_add_image_size');

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
