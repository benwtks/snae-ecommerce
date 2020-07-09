<div id="snae-total">
	<h2>Total</h2>
	<div id="cart-totals">
	<?php
	function getItemTotalHTML($title, $amount) {
		echo '<div class="item-total">';
		echo '<span>' . $title. '</span>';
		echo '<span class="value">£' . number_format($amount,  2, '.', '') . '</span>';
		echo '</div>';
	}

	$total = 0;
	foreach ($workshops as $w) {
		$price = (float) carbon_get_post_meta($w, 'crb_workshop_price');
		$total += $price;

		getItemTotalHTML(get_the_title($w), $price);
	}
	getItemTotalHTML('Sub-total', $total);
	?>
	</div>
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
