<?php
/*
 * The template for displaying an archive of artists
 */

get_header();
?>
	<div id="primary">
		<main id="main" class="site-main">
			<div class="artist-archive">
				<div class="content-area content-wrapper page-wrapper">
					<h2>Artists</h2>
					<div class="artist-previews">
					<?php
					if (have_posts()) :
						while(have_posts()) :
							the_post();
							echo snae_ecommerce_get_artist_preview(get_the_ID());
						endwhile;
					endif;
					?>
					</div>
				</div>
			</div>
		</main>
	</div>

<?php get_footer(); ?>
