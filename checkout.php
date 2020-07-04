<?php

function snae_ecommerce_create_session($product_data, $unit_amount){
	$stripe_api_key = carbon_get_theme_option('crb_stripe_api_key');

	if (!$stripe_api_key || !$unit_amount) {
		return;
	}

	\Stripe\Stripe::setApiKey($stripe_api_key);

	return \Stripe\Checkout\Session::create([
		'payment_method_types' => ['card'],
		'line_items' => [[
			'price_data' => [
				'currency' => 'gbp',
				'product_data' => $product_data,
				'unit_amount' => $unit_amount,
			],
			'quantity' => 1,
		]],
		'mode' => 'payment',
		'success_url' => 'http://google.com',
		'cancel_url' => 'http://google.com',
	]);
}

function snae_ecommerce_get_checkout_button($attrs, $content, $product_data, $unit_price){
	$session_id = snae_ecommerce_create_session($product_data, $unit_price)->$id;

	if ($session_id) {
		return '<button ' . $attrs . ' id="checkout-button" data-secret="' . $session_id . '">' . $content . '</button>';
	} else {
		return;
	}
}
