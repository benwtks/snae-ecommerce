<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

function snae_ecommerce_plugin_options() {
	Container::make( 'theme_options', __( 'Ecommerce Options' ) )
		->add_fields( array(
			Field::make( 'text', 'crb_stripe_api_key_publishable', 'Publishable Stripe API Key'),
			Field::make( 'text', 'crb_stripe_api_key_secret', 'Secret Stripe API Key'),
			Field::make( 'text', 'crb_workshop_refund_title', 'Workshop Refund guarantee title'),
			Field::make( 'select', 'crb_workshop_refund_policy', 'Workshop Refund policy page')
				->add_options( 'snae_ecommerce_get_pages_array' ),
			Field::make( 'complex', 'crb_standard_workshop_guarantees', 'Fixed workshop guarantees (shown on all workshops)' )
				->add_fields( array(
					Field::make( 'text', 'crb_standard_workshop_guarantee', 'Standard guarantee'),
				)),
			Field::make( 'checkbox', 'crb_ecommerce_raise_details', __( 'Raise the right hand side details box to top' ) )
				->set_option_value( 'yes' ),

		));
}

add_action( 'carbon_fields_register_fields', 'snae_ecommerce_plugin_options' );


