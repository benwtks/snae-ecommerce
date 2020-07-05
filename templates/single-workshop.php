<?php
/*
 * The template for displaying a single workshop
 */

get_header();

$raise_details = carbon_get_theme_option('crb_ecommerce_raise_details');
$artist_id = carbon_get_post_meta(get_the_ID(), 'crb_workshop_artist');

$unit_price_pounds = doubleval(carbon_get_post_meta(get_the_ID(), 'crb_workshop_price'));

?>
	<div id="primary" class="content-area content-wrapper workshop-wrapper">
		<main id="main" class="site-main">
			<?php if (!$raise_details) : ?>

			<div class="workshop-meta">
				<h1 class="title"><?php echo(get_the_title()) ?></h1>
				<?php echo snae_ecommerce_get_workshop_artist($artist_id) ?>
			</div>
			<div class="workshop-top">

			<?php else : ?>

			<div class="workshop-top raised">
				<div class="workshop-panel">
					<div class="workshop-meta">
						<h1 class="title"><?php echo(get_the_title()) ?></h1>
						<?php echo snae_ecommerce_get_workshop_artist($artist_id) ?>
					</div>

			<?php endif; ?>

				<div class="workshop-photos">
					<div class="thumbnails">
						<?php snae_ecommerce_print_workshop_thumbnails(get_the_ID(), 100, "workshop thumbnail"); ?>
					</div>
					<?php
					$first_url = snae_ecommerce_get_first_workshop_photo_url(get_the_ID());

					echo "<img id='featured' class='workshop-photo' src='" . $first_url . "' >"
					?>
				</div>

			<?php if ($raise_details) { echo '</div>'; } ?>

				<div class="ecommerce-details">
						<div class="intro">
							<h2 class="title"><?php echo(get_the_title()) ?></h2>
							<p class="description" ><?php echo carbon_get_post_meta(get_the_ID(), 'crb_workshop_longer_desc') ?></p>
							<?php
							$places = carbon_get_post_meta(get_the_ID(), 'crb_workshop_places');
							$bookable = carbon_get_post_meta(get_the_ID(), 'crb_workshop_bookable');
							echo $bookable ? 'Places available: ' . $places : 'Not currently bookable';
							?>
						</div>
						<div class="buy">
							<span class="price"><span class="currency">£</span>
									<?php echo $unit_price_pounds ?>
							</span>
							<?php echo snae_ecommerce_get_buy_now_form('class="buy-now"', "Buy now", get_the_ID())  ?>
						</div>
						<div class="workshop-guarantees">
							<ul>
								<?php
								$refund_title = carbon_get_theme_option('crb_workshop_refund_title');
								$refund_url = get_page_link(carbon_get_theme_option('crb_workshop_refund_policy'));
								$std_guarantees = carbon_get_theme_option('crb_standard_workshop_guarantees');
								$guarantees = carbon_get_post_meta(get_the_ID(), 'crb_workshop_guarantees');

								if ($refund_title && $refund_url) {
									echo '<li class="guarantee refund">' . $refund_title . '<a href="' . $refund_url . '" alt="full policy"><i class="dripicons-question"></i></a></li>';
								}

								foreach (array_merge($std_guarantees, $guarantees) as $g) {
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
			</div>
			<div class="workshop-middle">
				<h2 class="title">Workshop Description</h2>
				<div class="description">
					<?php
						if ( have_posts() ) :
							while ( have_posts() ) :
								the_post();
								the_content();
							endwhile;
						endif;
					?>
				</div>
				<div class="artist">
					<div class="artist-name">
						<div class="artist-details">
							<?php echo (snae_ecommerce_get_artist_image($artist_id, 90, 'artist-photo')) ?>
							<div class="intro">
								<h2 class="name"><?php echo(snae_ecommerce_get_workshop_artist($artist_id)) ?></h2>
								<span><?php echo(carbon_get_post_meta($artist_id, 'crb_job')) ?></span>
							</div>
						</div>
						<a class="see-more" href="<?php echo(get_permalink($artist_id)) ?>">Learn more</a>
					</div>
					<div class="artist-bio">
					<?php
					$bio = carbon_get_post_meta($artist_id, 'crb_longer_bio');

					foreach (explode(PHP_EOL, trim($bio, PHP_EOL)) as $paragraph) {
						echo ("<p>" . $paragraph . "</p>");
					}
					?>
					</div>
				</div>
			</div>
		</main>
	</div>

<?php get_footer(); ?>
