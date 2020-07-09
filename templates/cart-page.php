<?php get_header(); ?>

	<div id="primary">
		<main id="main" class="site-main">
			<div id="cart-page" class="content-area content-wrapper page-wrapper">
				<h1>Cart</h1>
				<div class="pay-wrapper">
					<?php include WP_PLUGIN_DIR . '/snae-ecommerce/templates/cart-parts/cart.php' ?>
					<?php include WP_PLUGIN_DIR . '/snae-ecommerce/templates/cart-parts/total.php' ?>
				</div>
			</div>
		</main>
	</div>

<?php get_footer(); ?>
