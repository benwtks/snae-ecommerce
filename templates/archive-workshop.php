<?php
/*
 * The template for displaying an archive of workshops
 */

get_header();
?>
	<div id="primary">
		<main id="main" class="site-main">
			<div class="workshop-archive">
				<div class="content-area content-wrapper page-wrapper">
					<h2>Workshops</h2>
					<div class="workshop-previews">
					<?php
					if (have_posts()) :
						while(have_posts()) :
							the_post();
							echo snae_ecommerce_get_workshop_preview(get_the_ID());
						endwhile;
					endif;
					?>
					</div>
				</div>
			</div>
		</main>
	</div>

<?php get_footer(); ?>
