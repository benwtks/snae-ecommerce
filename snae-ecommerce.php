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
	}
	
	return $template;
};

add_filter( 'single_template', 'snae_ecommerce_single_template' );
