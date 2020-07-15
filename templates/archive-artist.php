<?php
/*
 * The template for displaying an archive of artists
 */

global $wp_query;

get_header();
?>
	<div id="primary">
		<main id="main" class="site-main">
			<div class="artist-archive">
				<div class="content-area content-wrapper page-wrapper">
					<h2>Artists</h2>
					<?php
					echo '<div class="artist-previews">';
					if (have_posts()) :
						while(have_posts()) :
							the_post();
							echo snae_ecommerce_get_artist_preview(get_the_ID());
						endwhile;
					endif;
					echo '</div>';

					if ($wp_query->max_num_pages > 1) {
						echo '<nav class="archive-nav"><ul class="pager">';

						$current_page = $wp_query->get( 'paged' );

						echo '<li class="previous">';
						if ( $current_page != 0 ) {
							// If not first page
							echo '<a href="';
							echo previous_posts();
							echo '">';
							echo '<i class="dripicons-chevron-left"></i>Previous';
							echo '</a>';
						}
						echo '</li>';

						echo '<li class="next">';
						if ( $current_page != $wp_query->max_num_pages ) {
							// If not last page
							echo '<a href="';
							echo next_posts();
							echo '">';
							echo 'Next<i class="dripicons-chevron-right"></i>';
							echo '</a>';
						}
						echo '</li>';


						echo '</ul></nav>';
					}

					?>
				</div>
			</div>
		</main>
	</div>

<?php get_footer() ?>
