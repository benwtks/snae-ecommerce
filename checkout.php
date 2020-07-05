<?php

function snae_ecommerce_create_session($secret_key, $order_name, $desc, $img, $unit_amount, $cancel_url, $success_url){
	$stripe = new \Stripe\StripeClient($secret_key);

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
	]);

	 return $session;
}

function snae_ecommerce_get_checkout_button($content, $workshop){
	$order_name = get_the_title($workshop);
	$unit_price = doubleval(carbon_get_post_meta($workshop, 'crb_workshop_price')) * 100;
	$secret_key = carbon_get_theme_option('crb_stripe_api_key_secret');
	$publishable_key = carbon_get_theme_option('crb_stripe_api_key_publishable');

	$success_url = get_page_link(carbon_get_theme_option('crb_success_page'));
	$cancel_url = get_page_link($workshop);

	$desc = carbon_get_post_meta($workshop, 'crb_workshop_short_desc');
	$img = snae_ecommerce_get_first_workshop_photo_url($workshop);

	$session_id = snae_ecommerce_create_session($secret_key, $order_name, $desc, $img, $unit_price, $cancel_url, $success_url)->id;

	if ($session_id && $publishable_key) {
		return '<button id="checkout-button" data-hi="' . $publishable_key . '" data-secret="' . $session_id . '">' . $content . '</button>';
	} else {
		return;
	}
}

function snae_ecommerce_get_buy_now_form($button_attrs, $button_content, $workshop_id) {
	$product_data = ['name' => get_the_title($workshop_id)];
	$buy_posts = new WP_Query( array(
		'post_type' => 'buy',
		'posts_per_page' => -1
	));

	$action = get_post_permalink(wp_list_pluck($buy_posts->posts, 'ID')[0]);

	return '<form method="POST" action="' . $action . '">
			<input type="hidden" name="workshop_id" value="' . $workshop_id . '">
			<button ' . $button_attrs . '>' . $button_content . '</button>
		</form>';
}
