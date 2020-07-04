<?php

function snae_ecommerce_scripts() {
	wp_enqueue_script( 'workshop_photo', plugins_url('/js/workshop_photo.js', __FILE__), array(), _S_VERSION);
}

add_action( 'wp_enqueue_scripts', 'snae_ecommerce_scripts' );


