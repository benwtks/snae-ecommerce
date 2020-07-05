<?php

function snae_ecommerce_decrease_stock() {
	$success_page_id = carbon_get_theme_option('crb_success_page');

	if ( is_page($success_page_id) ) {
		$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
		$id = $_GET['checkout_session_id'];

		if ($id) {
			$stripe_secret_key = carbon_get_theme_option('crb_stripe_api_key_secret');
			$stripe = new \Stripe\StripeClient($stripe_secret_key);
			$session = $stripe->checkout->sessions->retrieve($id);

			$workshop = $session->metadata->workshop_id;

			$old_stock = intval(carbon_get_post_meta($workshop, 'crb_workshop_places'));
			carbon_set_post_meta($workshop, 'crb_workshop_places', $old_stock - 1);

			if ($old_stock == 1) {
				carbon_set_post_meta($workshop, 'crb_workshop_bookable', 0);
			}

			wp_redirect(get_page_link($success_page_id));
			die();
		}
	}
}

add_action( 'template_redirect', 'snae_ecommerce_decrease_stock' );

function snae_ecommerce_create_session($secret_key,  $workshop_id){
	$stripe = new \Stripe\StripeClient($secret_key);
	$success_url = get_page_link(carbon_get_theme_option('crb_success_page')) . '?checkout_session_id={CHECKOUT_SESSION_ID}';
	$cancel_url = get_page_link($workshop_id);
	$order_name = get_the_title($workshop_id);
	$unit_amount = intval(carbon_get_post_meta($workshop_id, 'crb_workshop_price')) * 100;

	$desc = carbon_get_post_meta($workshop_id, 'crb_workshop_short_desc');
	$img = snae_ecommerce_get_first_workshop_photo_url($workshop_id);

	$session = $stripe->checkout->sessions->create([
		'payment_method_types' => ['card'],
		'line_items' => [[
			'price_data' => [
				'currency' => 'gbp',
				'product_data' => [
					'name' => $order_name,
					'description' => $desc,
					'images' => [$img]
				],
				'unit_amount' => $unit_amount,
			],
			'quantity' => 1,
		]],
		'mode' => 'payment',
		'success_url' => $success_url,
		'cancel_url' => $cancel_url,
		'metadata' => ['workshop_id' => $workshop_id],
	]);

	 return $session;
}

function snae_ecommerce_get_checkout_button($content, $workshop){
	$secret_key = carbon_get_theme_option('crb_stripe_api_key_secret');
	$publishable_key = carbon_get_theme_option('crb_stripe_api_key_publishable');

	$session_id = snae_ecommerce_create_session($secret_key, $workshop)->id;

	if ($session_id && $publishable_key) {
		return '<button id="checkout-button" data-stripe="' . $publishable_key . '" data-secret="' . $session_id . '">' . $content . '</button>';
	} else {
		return;
	}
}

function snae_ecommerce_get_add_to_cart_form($button_attrs, $button_content, $workshop_id) {
	$pay_posts = new WP_Query( array(
		'post_type' => 'pay',
		'name' => 'cart',
		'posts_per_page' => -1
	));
	$action = get_post_permalink(wp_list_pluck($pay_posts->posts, 'ID')[0]);

	$places = carbon_get_post_meta($workshop_id, 'crb_workshop_places');
	$bookable = $places > 0 ? carbon_get_post_meta($workshop_id, 'crb_workshop_bookable') : 0;

	$disabled = $bookable ? '' : 'disabled';

	return '<form method="POST" action="' . $action . '">
			<input type="hidden" name="workshop_id" value="' . $workshop_id . '">
			<button ' . $button_attrs . ' ' . $disabled . '>' . $button_content . '</button>
		</form>';
}
