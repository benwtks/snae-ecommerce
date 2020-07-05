<?php
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$order_item_id = $_POST['workshop_id'];

if (!$order_item_id) {
	wp_redirect(home_url());
	exit();
}

$order_item_title = get_the_title($order_item_id);
$order_item_price = carbon_get_post_meta($order_item_id, 'crb_workshop_price');
$order_item_desc = carbon_get_post_meta($order_item_id, 'crb_workshop_longer_desc');
$order_item_photo = snae_ecommerce_get_first_workshop_photo_url($order_item_id, 'checkout');

get_header(); ?>

	<div id="primary">
		<main id="main" class="site-main">
			<div class="content-area content-wrapper page-wrapper">
				<h1>Book Workshop</h1>
				<div class="snae-orders">
					<div class="order">
						<div class="order-details">
							<img src="<?php echo $order_item_photo ?>" alt="Item photo">
							<div class="order-meta">
								<h2 class="order-title"><?php echo $order_item_title ?></h2>
								<p><?php echo $order_item_desc ?></p>
							</div>
						</div>
						<span class="order-price">£<?php echo $order_item_price ?></span>
					</div>
				</div>
				<?php
				$button = snae_ecommerce_get_checkout_button("Checkout", $order_item_id);
				echo ($button? $button : "Sorry, we can't take orders at the moment due to an error. Please get in touch to let us know.");
				?>
			</div>
		</main>
	</div>

<?php get_footer(); ?>
