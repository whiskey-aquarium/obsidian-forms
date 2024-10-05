<?php
declare( strict_types = 1 );

function automattic_2011_register_brand_asset_block() {
	register_block_type_from_metadata( __DIR__ . '/block.json' );
}
add_action( 'init', 'automattic_2011_register_brand_asset_block' );
