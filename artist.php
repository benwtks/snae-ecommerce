<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;
require_once( __DIR__ . '/photo-resizer.php');

function snae_ecommerce_create_artist_post_type() {
	register_post_type('artist',
		array(
			'labels' => array(
				'name' => __( 'Artists' ),
				'menu_name' => __( 'Artists' ),
				'singular_name' => __( 'Artist' ),
				'name_admin_bar'     => _x( 'Artist', 'add new on admin bar'),
				'add_new'            => _x( 'Add New', 'artist'),
				'add_new_item'       => __( 'Add New Artist'),
				'new_item'           => __( 'New Artist'),
				'edit_item'          => __( 'Edit Artist'),
				'view_item'          => __( 'View Artist'),
				'all_items'          => __( 'All Artists'),
				'search_items'       => __( 'Search Artists'),
				'parent_item_colon'  => __( 'Parent Artists:'),
				'not_found'          => __( 'No artists found.'),
				'not_found_in_trash' => __( 'No artists found in Trash.')
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'artists'),
			'show_in_rest' => true,
			'menu_icon' => 'dashicons-admin-customizer',
			'supports' => array('title', 'editor')
		)
	);
}

add_action('init', 'snae_ecommerce_create_artist_post_type' );

function snae_ecommerce_crb_attach_artist_options() {
	Container::make( 'post_meta', 'Artist Details' )
		->where( 'post_type', '=', 'artist' )
		->add_fields( array(
			Field::make( 'image', 'crb_artist_photo', 'Profile photo' )
				->set_required(true),
			Field::make( 'text', 'crb_job', 'Job title' )
				->set_required(true),
			Field::make( 'textarea', 'crb_short_bio', 'Short Bio (80 character limit)' )
				->set_required(true)
				->set_attribute( 'maxLength', 80 ),
			Field::make( 'textarea', 'crb_longer_bio', 'Longer Bio (1500 character limit)' )
				->set_attribute( 'maxLength', 1500 )
				->set_required(true),
		));

	Container::make( 'post_meta', 'Links' )
		->where( 'post_type', '=', 'artist' )
		->add_fields( array(
			Field::make( 'text', 'crb_artist_website', 'Website/Portfolio'),
			Field::make( 'text', 'crb_artist_video', 'Single artist video link'),
			Field::make( 'text', 'crb_artist_facebook', 'Facebook'),
			Field::make( 'text', 'crb_artist_twitter', 'Twitter'),
			Field::make( 'text', 'crb_artist_instagram', 'Instagram'),
			Field::make( 'text', 'crb_artist_youtube', 'Youtube channel'),
			Field::make( 'text', 'crb_artist_etsy', 'Etsy'),
			Field::make( 'text', 'crb_artist_linkedin', 'LinkedIn')
		));

	Container::make( 'post_meta', 'Gallery' )
		->where( 'post_type', '=', 'artist' )
		->add_fields( array(
			Field::make( 'text', 'crb_artist_gallery_shortcode', 'Gallery code (e.g. Envira Gallery)')
		));

}

add_action( 'carbon_fields_register_fields', 'snae_ecommerce_crb_attach_artist_options' );

function snae_ecommerce_save_artist($post_id) {
	if (get_post_type($post_id) !== "artist") {
		return false;
	}

	$photo_ID = carbon_get_post_meta($post_id, 'crb_artist_photo');
	snae_ecommerce_resize_if_needed($photo_ID, 400, 400, "artist-photo");
}

function snae_ecommerce_get_artist_image($post_id, $size, $alt) {
	snae_ecommerce_save_artist($post_id);
	$photo_ID = carbon_get_post_meta($post_id, 'crb_artist_photo');
	$cropped_url = snae_ecommerce_get_cropped_url($photo_ID, "artist-photo");

	return ("<img width='". $size . "' height='" . $size . "' class='artist-photo' src='" . $cropped_url . "' alt='" . $alt . "'/>");
}

function snae_ecommerce_get_artist_workshops($post_id) {
	$workshop_query = new WP_Query( array(
		'post_type' => 'workshop',
		'posts_per_page' => -1
	));

	$workshop_ids = wp_list_pluck( $workshop_query->posts, 'ID' );

	foreach ($workshop_ids as $w_id => $workshop) {
		$artist = carbon_get_post_meta($workshop, 'crb_workshop_artist');

		if ($artist !== $post_id) {
			unset($workshop_ids[$w_id]);
		}
	}

	return $workshop_ids;
}

?>
