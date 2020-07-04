<?php

function snae_ecommerce_create_session($secret_key, $order_name, $unit_amount){
	if (!$secret_key || !$order_name || !$unit_amount) {
		return;
	}
	
	$stripe = new \Stripe\StripeClient($secret_key);

	$session = $stripe->checkout->sessions->create([
		'payment_method_types' => ['card'],
		'line_items' => [[
			'price_data' => [
				'currency' => 'gbp',
				'product_data' => [
					'name' => $order_name,
				],
				'unit_amount' => $unit_amount,
			],
			'quantity' => 1,
		]],
		'mode' => 'payment',
		'success_url' => 'https://example.com/success?session_id={CHECKOUT_SESSION_ID}',
		'cancel_url' => 'https://example.com/cancel',
	]);

	 return $session;
}

function snae_ecommerce_get_checkout_button($content, $workshop){
	$order_name = get_the_title($workshop);
	$unit_price = doubleval(carbon_get_post_meta($workshop, 'crb_workshop_price')) * 100;
	$secret_key = carbon_get_theme_option('crb_stripe_api_key_secret');
	$publishable_key = carbon_get_theme_option('crb_stripe_api_key_publishable');

	$session_id = snae_ecommerce_create_session($secret_key, $order_name, $unit_price)->id;

	if ($session_id && $publishable_key) {
		return '<button id="checkout-button" data-key="' . $publishable_key . '" data-secret="' . $session_id . '">' . $content . '</button>';
	} else {
		return;
	}
}

function snae_ecommerce_get_buy_now_form($button_attrs, $button_content, $workshop_id) {
	$product_data = ['name' => get_the_title($workshop_id)];

	return '<form method="POST" action="/checkout">
			<input type="hidden" name="workshop_id" value="' . $workshop_id . '">
			<button ' . $button_attrs . '>' . $button_content . '</button>
		</form>';
}
