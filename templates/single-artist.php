<?php
/*
 * The template for displaying a single artist profile
 */

get_header();
?>
	<div id="primary">
		<main id="main" class="site-main">
			<div class="artist-about">
				<div class="content-area content-wrapper artist-wrapper">
					<div class="artist-header">
						<div class="artist-details">
							<?php echo (snae_ecommerce_get_artist_image(get_the_ID(), 120, 'artist-photo')) ?>
							<div class="intro">
								<h1 class="name"><?php echo(get_the_title()) ?></h1>
								<span><?php echo(carbon_get_post_meta(get_the_ID(), 'crb_job')) ?></span>
							</div>
						</div>
						<div class="links">
							<?php
							function print_artist_icon($crb_name) {
								$site = substr(strrchr($crb_name, "_"), 1);
								$class = "fab fa-" . $site;
								$link = carbon_get_post_meta(get_the_ID(), $crb_name);

								if ($link) {
									echo "<a class='artist-icon " . $site . "' href='" . $link . "'><i class='" . $class . "'></i></a>";
								}
							}

							print_artist_icon('crb_artist_facebook');
							print_artist_icon('crb_artist_twitter');
							print_artist_icon('crb_artist_linkedin');
							print_artist_icon('crb_artist_youtube');
							print_artist_icon('crb_artist_instagram');
							print_artist_icon('crb_artist_etsy');

							$website = carbon_get_post_meta(get_the_ID(), 'crb_artist_website');
							if ($website) {
								echo '<a class="website-link" href="' . $website . '">Website</a>';
							}

							$video = carbon_get_post_meta(get_the_ID(), 'crb_artist_video');
							if ($video) {
								echo '<a class="website-link video" href="' . $video . '"><i class="fab fa-youtube"></i>Watch</a>';
							}

							?>
						</div>
					</div>
					<div class="artist-bio">
					<?php
						if ( have_posts() ) :
							while ( have_posts() ) :
								the_post();
								the_content();
							endwhile;
						endif;
					?>
					</div>
					<div class="artist-gallery">
						<?php echo do_shortcode(carbon_get_post_meta(get_the_ID(), 'crb_artist_gallery_shortcode')) ?>
					</div>
				</div>
			</div>
			<?php
			$workshops = snae_ecommerce_get_artist_workshops(get_the_ID());

			if ($workshops): ?>
			<div class="workshops">
				<div class="content-area content-wrapper artist-wrapper">
					<h2>Workshops</h2>
					<div class="workshop-previews">
					<?php
						$workshops = snae_ecommerce_get_artist_workshops(get_the_ID());

						foreach ($workshops as $w) {
							echo snae_ecommerce_get_workshop_preview($w);
						}
					?>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</main>
	</div>

<?php get_footer(); ?>
