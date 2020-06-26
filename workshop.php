<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

function snae_ecommerce_create_workshop_post_type() {
	register_post_type('workshop',
		array(
			'labels' => array(
				'name' => __( 'Workshops' ),
				'menu_name' => __( 'Workshops' ),
				'singular_name' => __( 'Workshop' ),
				'edit_item' => __('Edit Workshop'),
				'name_admin_bar'     => _x( 'Workshop', 'add new on admin bar'),
				'add_new'            => _x( 'Add New', 'workshop'),
				'add_new_item'       => __( 'Add New Workshop'),
				'new_item'           => __( 'New Workshop'),
				'edit_item'          => __( 'Edit Workshop'),
				'view_item'          => __( 'View Workshop'),
				'all_items'          => __( 'All Workshops'),
				'search_items'       => __( 'Search Workshops'),
				'parent_item_colon'  => __( 'Parent Workshops'),
				'not_found'          => __( 'No workshops found.'),
				'not_found_in_trash' => __( 'No workshops found in Trash.')
			),
			'public' => true,
			'has_archive' => false,
			'rewrite' => array('slug' => 'workshops'),
			'show_in_rest' => true,
			'menu_icon' => 'dashicons-cart',
			'taxonomies' => array( 'category' ),
			'supports' => array('title')
		)
	);

	add_image_size( 'workshop-preview', 1000, 800, true);
}

add_action( 'init', 'snae_ecommerce_create_workshop_post_type' );

function snae_ecommerce_get_artist_name_array() {
	$artist_query = new WP_Query( array(
		'post_type' => 'artist',
		'posts_per_page' => -1
	));

	$artists_array = $artist_query->posts;
	return wp_list_pluck( $artists_array, 'post_title', 'ID' );
}

function snae_ecommerce_crb_attach_workshop_options() {
	Container::make( 'post_meta', 'Workshop Details' )
		->where( 'post_type', '=', 'workshop' )
		->add_fields( array(
			Field::make( 'select', 'crb_workshop_artist', 'Artist' )
				->add_options( 'snae_ecommerce_get_artist_name_array' ),
			Field::make( 'textarea', 'crb_workshop_short_desc', 'Short Description (80 character limit)' )
				->set_attribute( 'maxLength', 80 ),
			Field::make( 'textarea', 'crb_workshop_longer_desc', 'Longer Description (280 character limit)' )
				->set_attribute( 'maxLength', 280 ),
			Field::make( 'textarea', 'crb_workshop_desc', 'Description' ),
		));

	Container::make( 'post_meta', 'Photos' )
		->where( 'post_type', '=', 'workshop' )
		->add_fields( array(
			Field::make( 'media_gallery', 'crb_workshop_photos', 'Photos' )
		));

	Container::make( 'post_meta', 'Ecommerce' )
		->where( 'post_type', '=', 'workshop' )
		->add_fields( array(
			Field::make( 'text', 'crb_workshop_price', 'Price (£)' )
				->set_attribute( 'placeholder', 'e.g. 49.99' ),
			Field::make( 'checkbox', 'crb_artist_sold_by_snae', 'Sold by SNAE' )
				->set_option_value( 'yes' ),
			Field::make( 'text', 'crb_workshop_places', 'Places available (Stock)' ),
			Field::make( 'complex', 'crb_workshop_guarantees', 'Customer guarantees' )
				->add_fields( array(
					Field::make( 'text', 'crb_workshop_guarantee', 'Guarantee'),
				)),
		));
}

add_action( 'carbon_fields_register_fields', 'snae_ecommerce_crb_attach_workshop_options' );

function snae_ecommerce_get_workshop_artist($artist) {
	$title = get_the_title($artist);
	$link = get_the_permalink($artist);

	return "<a href='" . $link . "' alt='artist-profile'>" . $title . "</a>";
}

function snae_ecommerce_save_workshop($post_id) {
	if (get_post_type($post_id) !== "workshop") {
		return false;
	}

	$photos = carbon_get_post_meta($post_id, 'crb_workshop_photos');

	foreach ($photos as $photo) {
		snae_ecommerce_resize_if_needed($photo, 200, 200, "workshop-thumbnail");
	}
}

function snae_ecommerce_print_workshop_thumbnail($size, $photo_ID, $alt, $class = "") {
	$onclick = 'updateWorkshop("' . wp_get_attachment_url($photo_ID) . '")';
	$cropped_url = snae_ecommerce_get_cropped_url($photo_ID, "workshop-thumbnail");

	return ("<img width='". $size . "' height='" . $size . "' class='workshop-thumbnail " . $class ."' src='" . $cropped_url . "' alt='" . $alt . "' onclick='". $onclick . "' />");
}

function snae_ecommerce_print_workshop_thumbnails($post_id, $size, $alt) {
	snae_ecommerce_save_workshop($post_id);
	$photos = carbon_get_post_meta($post_id, 'crb_workshop_photos');

	echo snae_ecommerce_print_workshop_thumbnail($size, $photos[0], $alt, "selected");
	unset($photos[0]);

	foreach ($photos as $photo_id) {
		echo snae_ecommerce_print_workshop_thumbnail($size, $photo_id, $alt);
	}
}

function snae_ecommerce_get_first_workshop_photo_url($post_id, $size = '') {
	$photos = carbon_get_post_meta($post_id, 'crb_workshop_photos');

	return $size? wp_get_attachment_image_url($photos[0], $size) :
		wp_get_attachment_url($photos[0]);
}

?>
