<?php

function snae_ecommerce_get_cart_url() {
	$pay_posts = new WP_Query( array(
		'post_type' => 'pay',
		'name' => 'cart',
		'posts_per_page' => -1
	));
	return get_post_permalink(wp_list_pluck($pay_posts->posts, 'ID')[0]);
}

function snae_ecommerce_get_checkout_url() {
	$pay_posts = new WP_Query( array(
		'post_type' => 'pay',
		'name' => 'checkout',
		'posts_per_page' => -1
	));
	return get_post_permalink(wp_list_pluck($pay_posts->posts, 'ID')[0]);
}

function snae_ecommerce_decrease_stock() {
	$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
	$client_secret = $_GET['intent'];
	$dietary = $_GET['dietary'];

	if ($client_secret) {
		$stripe_secret_key = carbon_get_theme_option('crb_stripe_api_key_secret');
		$stripe = new \Stripe\StripeClient($stripe_secret_key);
		$intent = $stripe->paymentIntents->retrieve($client_secret);

		$workshops = explode(",",$intent->metadata->workshops);

		foreach ($workshops as $workshop) {
			$old_stock = intval(carbon_get_post_meta($workshop, 'crb_workshop_places'));

			if ($old_stock > 0) {
				carbon_set_post_meta($workshop, 'crb_workshop_places', $old_stock - 1);

				if ($old_stock == 1) {
					carbon_set_post_meta($workshop, 'crb_workshop_bookable', 0);
				}
			} else {
			}
		}
	}

	$response = array( 'success' => 'true' );
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send();
}

add_action( 'wp_ajax_nopriv_payment', 'snae_ecommerce_decrease_stock' );
add_action( 'wp_ajax_payment', 'snae_ecommerce_decrease_stock' );

function snae_ecommerce_create_intent($secret_key, $workshops) {
	$stripe = new \Stripe\StripeClient($secret_key);

	$amount = 0;
	$description = "Workshops including: ";

	foreach ($workshops as $w) {
		$w_price = (float) carbon_get_post_meta($w, 'crb_workshop_price');
		$description  .= get_the_title($w) . " for £" . $w_price . ", ";

		$amount += ($w_price * 100);
	}

	$description = substr($description, 0, strlen($description) - 2);

	$success_page = get_page_link(carbon_get_theme_option('crb_success_page'));

	return $stripe->paymentIntents->create([
		'amount' => $amount,
		'currency' => 'gbp',
		'payment_method_types' => ['card'],
		'description' => $description,
		'metadata' => ['workshops' => implode(",", $workshops)],
	]);
}

function snae_ecommerce_get_checkout_button($content, $button_attrs = ''){
	$action = snae_ecommerce_get_checkout_url();

	return '<form id="start-checkout" method="POST" action="' . $action . '">
			<input type="hidden" name="cart_items" value="' . $workshop_id . '">
			<button ' . $button_attrs . ' id="checkout">' . $content . '<i class="dripicons-lock"></i></button>
		</form>';
}

function snae_ecommerce_get_add_to_cart_button($button_attrs, $button_content, $workshop_id) {
	$places = carbon_get_post_meta($workshop_id, 'crb_workshop_places');
	$bookable = $places > 0 ? carbon_get_post_meta($workshop_id, 'crb_workshop_bookable') : 0;
	$disabled = $bookable ? '' : 'disabled';

	return '<button id="add-to-cart-button"' . $button_attrs . '
		data-workshop="'      . $workshop_id . '"
		data-preview-url="'   . get_page_link($workshop_id) . '"
		data-cart="'          . snae_ecommerce_get_cart_url() . '"
		data-preview-title="' . get_the_title($workshop_id) . '"
		data-preview-photo="' . snae_ecommerce_get_first_workshop_photo_url($workshop_id, 'checkout') . '"
		data-preview-desc="'  . carbon_get_post_meta($workshop_id, 'crb_workshop_longer_desc') . '"
		data-preview-price="' . carbon_get_post_meta($workshop_id, 'crb_workshop_price') . '"' .
		$disabled . '>' . $button_content . '</button>';
}

function snae_ecommerce_workshop_ids($workshops) {
	return array_map(function($x){
		return substr(strstr($x,'-'),1);
	}, explode(',', $workshops));
}
