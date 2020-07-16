<?php
require_once( __DIR__ . '/stripe-php/init.php');

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

// Updates the places for the given workshops as part of set up for payment. Returns any out of stock items
function snae_ecommerce_update_workshop_places($workshops) {
	$full = array();

	foreach ($workshops as $workshop) {
		$old_stock = intval(carbon_get_post_meta($workshop, 'crb_workshop_places'));

		if ($old_stock > 0) {
			carbon_set_post_meta($workshop, 'crb_workshop_places', $old_stock - 1);

			if ($old_stock == 1) {
				carbon_set_post_meta($workshop, 'crb_workshop_bookable', 0);
			}
		} else {
			array_push($full, $workshop);
		}
	}
	return $full;
}

function snae_ecommerce_update_paymentintent($stripe, $intent_id, $dietary) {
	try {
		$stripe->paymentIntents->update( $intent_id,
			['metadata' => ['dietary' => $dietary ]]);
		return true;
	} catch (\Stripe\Exception\ApiErrorException | \Stripe\Exception\ApiConnectionException $e) {
		return false;
	}
}

// Function to get ready for a payment. Checks stock and updates meta.
// To be called from admin-ajax prior to confirming a payment.
// Returns 502, 406, 400 or 200 HTTP codes
function snae_ecommerce_set_up_for_payment() {
	// Retrieve POST params
	$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
	$client_secret = $_POST['intent'];
	$dietary = $_POST['dietary'];

	// Initialise Stripe object for API calls
	$stripe_secret_key = carbon_get_theme_option('crb_stripe_api_key_secret');
	$stripe = new \Stripe\StripeClient($stripe_secret_key);

	// Check for req params
	if (!$client_secret) {
		wp_send_json(array(
			'stock' => null,
			'updated' => false,
			'error' => 'Missing client secret'
		), 400);
	}

	if (!$dietary) {
		wp_send_json(array(
			'stock' => null,
			'updated' => false,
			'error' => 'Missing dietary'
		), 400);
	}

	// Update workshop places, send error if now full
	$intent_id = "pi_" . explode("_", $client_secret)[1];
	try {
		$intent = $stripe->paymentIntents->retrieve($intent_id);
	} catch (\Stripe\Exception\ApiErrorException | \Stripe\Exception\ApiConnectionException $e) {
		wp_send_json(array(
			'stock' => null,
			'updated' => false,
			'error' => 'Request Failed - Could not make Stripe API call'
		), 502);
	}

	$workshops = explode(",",$intent->metadata->workshops);

	$places_updated = snae_ecommerce_update_workshop_places($workshops);
	if (count($places_updated)) {
		wp_send_json(array(
			'stock' => false,
			'updated' => false,
			'error' => 'Insufficient Stock',
			'full' => $places_updated,
		), 406);
	}

	// Update Strip PaymentIntent with checkout form metadata
	$intent_updated = snae_ecommerce_update_paymentintent($stripe, $intent_id, $dietary);

	if (!$intent_updated) {
		wp_send_json(array(
			'stock' => true,
			'updated' => false,
			'error' => 'Request Failed - Could not make Stripe API call',
		), 502);
	}

	// All good, send response - 200 OK
	wp_send_json(array(
		'stock' => true,
		'updated' => true,
	), 200);
}

add_action( 'wp_ajax_nopriv_payment', 'snae_ecommerce_set_up_for_payment' );
add_action( 'wp_ajax_payment', 'snae_ecommerce_set_up_for_payment' );

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

	try {
		return $stripe->paymentIntents->create([
			'amount' => $amount,
			'currency' => 'gbp',
			'payment_method_types' => ['card'],
			'description' => $description,
			'metadata' => ['workshops' => implode(",", $workshops)],
		]);
	} catch (\Stripe\Exception\ApiErrorException | \Stripe\Exception\ApiConnectionException $e) {
		return $e;
	}
}

function snae_ecommerce_get_checkout_button($content, $button_attrs = ''){
	$action = snae_ecommerce_get_checkout_url();

	return '<form id="start-checkout" method="POST" action="' . $action . '">
			<input type="hidden" name="cart_items" value="">
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
