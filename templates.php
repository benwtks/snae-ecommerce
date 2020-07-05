<?php

function snae_ecommerce_single_template( $template ) {
	global $post;

	if ($post->post_type === 'artist') {
		$template = dirname( __FILE__ ) . '/templates/single-artist.php';
	} else if ( is_post_type_archive('workshop') ) {
		$template = dirname( __FILE__ ) . '/templates/archive-workshop.php';
	} else if ($post->post_type === 'workshop') {
		$template = dirname( __FILE__ ) . '/templates/single-workshop.php';
	} else if ($post->post_type === 'buy') {
		$template = dirname( __FILE__ ) . '/templates/checkout-page.php';
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
