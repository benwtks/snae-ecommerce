<?php
require_once ABSPATH . '/wp-admin/includes/image.php';

function snae_ecommerce_generate_cropped_path($photo_ID, $file, $label) {
	$photo_folder = substr($file, 0, strrpos($file, "/")) . "/";
	$photo_file_type = strrchr($file, '.');

	return "uploads/" . $photo_folder . $photo_ID . "-" . $label . $photo_file_type;
}

function snae_ecommerce_get_cropped_url($photo_ID, $label) {
	$file = wp_get_attachment_metadata($photo_ID)['file'];

	return content_url(snae_ecommerce_generate_cropped_path($photo_ID, $file, $label));
}

function snae_ecommerce_resize_if_needed($photo_ID, $width, $height, $label) {
	$file = wp_get_attachment_metadata($photo_ID)['file'];

	$cropped_path = snae_ecommerce_generate_cropped_path($photo_ID, $file, $label);
	$cropped_url = content_url($cropped_path);

	if (!file_exists(WP_CONTENT_DIR . "/" . $cropped_path)) {
		$image = wp_get_image_editor(WP_CONTENT_DIR . '/uploads/' . $file);

		if (! is_WP_error($editor)) {
			$image->resize($width,$height, array( 'center', 'center'));
			$image->save("wp-content/" . $cropped_path);
		} else {
			return false;
		}
	}

	return true;
}

?>
