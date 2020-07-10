<?php

$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

$workshops = snae_ecommerce_workshop_ids($_POST['cart_items']);

if (!$_POST['cart_items']) {
	wp_redirect(snae_ecommerce_get_cart_url());
	exit();
}

$intent = snae_ecommerce_create_intent(carbon_get_theme_option('crb_stripe_api_key_secret'), $workshops);

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
