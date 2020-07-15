<?php

$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

$workshops = snae_ecommerce_workshop_ids($_POST['cart_items']);
$secret_key = carbon_get_theme_option('crb_stripe_api_key_secret');
$publishable_key = carbon_get_theme_option('crb_stripe_api_key_publishable');

if (!$_POST['cart_items'] || !$secret_key || !$publishable_key) {
	wp_redirect(snae_ecommerce_get_cart_url());
	exit();
}

try {
	$intent = snae_ecommerce_create_intent($secret_key, $workshops);
} catch (\Stripe\Exception\InvalidArgumentException | \Stripe\Exception\ApiErrorException | \Stripe\Exception\ApiErrorException $e) {
	wp_redirect(snae_ecommerce_get_cart_url() . '?error=' . $e->getMessage());
	exit();
}


if(!$intent) {
	wp_redirect(snae_ecommerce_get_cart_url() . '?error=could-not-create-intent');
	exit();
}

if (!snae_ecommerce_get_cart_url()) {
	wp_redirect(snae_ecommerce_get_cart_url() . '?error=error-getting-cart');
	exit();
}

if (!carbon_get_theme_option('crb_success_page')) {
	wp_redirect(snae_ecommerce_get_cart_url() . '?error=error-getting-success-page');
	exit();
}

get_header();
?>
	<div id="primary">
		<main id="main" class="site-main">
		<div id="cart-page" class="content-area content-wrapper page-wrapper" data-items="<?php echo implode(',', $workshops) ?>">
				<h1>Checkout</h1>
				<div class="pay-wrapper">
					<?php include __DIR__ . '/cart-parts/form.php' ?>
					<?php include __DIR__ . '/cart-parts/checkout-total.php' ?>
				</div>
			</div>
		</main>
	</div>

<?php get_footer(); ?>
