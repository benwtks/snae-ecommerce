<?php

function snae_ecommerce_scripts() {
	wp_enqueue_script('workshop_photo', plugins_url('/js/workshop_photo.js', __FILE__), array(), _S_VERSION);
	wp_enqueue_script('snae_ecommerce_cart_button', plugins_url('/js/addToCart.js', __FILE__), array(), _S_VERSION);

	if ( get_post_type() === 'pay' ) {
		if ( get_the_title() === 'Cart' ) {
			wp_enqueue_script('snae_ecommerce_cart', plugins_url('/js/cart.js', __FILE__), array(), _S_VERSION);
		}

		if ( get_the_title() === 'Checkout' ) {
			wp_enqueue_script('snae_ecommerce_checkout', plugins_url('/js/checkout.js', __FILE__), array(), _S_VERSION);
			wp_enqueue_script('stripe_elements', "https://js.stripe.com/v3/", array(), _S_VERSION);
		}
	}
}

add_action( 'wp_enqueue_scripts', 'snae_ecommerce_scripts' );


