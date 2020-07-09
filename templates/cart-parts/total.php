<div id="snae-total">
	<h2>Total</h2>
	<div id="cart-totals">
	</div>
	<?php
	$button = snae_ecommerce_get_checkout_button("Checkout");
	echo ($button? $button : "Sorry, we can't take orders at the moment due to an error. Please get in touch to let us know.");
	?>
	<div class="workshop-guarantees">
		<ul>
			<?php
			$refund_title = carbon_get_theme_option('crb_workshop_refund_title');
			$refund_url = get_page_link(carbon_get_theme_option('crb_workshop_refund_policy'));
			$std_guarantees = carbon_get_theme_option('crb_standard_workshop_guarantees');

			if ($refund_title && $refund_url) {
				echo '<li class="guarantee refund">' . $refund_title . '<a href="' . $refund_url . '" alt="full policy"><i class="dripicons-question"></i></a></li>';
			}

			foreach ($std_guarantees as $g) {
				echo '<li class="guarantee">';

				if ($g['crb_workshop_guarantee']) {
					echo $g['crb_workshop_guarantee'];
				} else {
					echo $g['crb_standard_workshop_guarantee'];
				}

				echo '</li>';
			}
			?>
		</ul>
	</div>
</div>
