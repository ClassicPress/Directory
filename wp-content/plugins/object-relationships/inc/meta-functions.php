<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

function add_relationship_meta( $relationship_id, $meta_key, $meta_value, $unique = false ) {
	return add_metadata( 'kts_object_relationship', $relationship_id, $meta_key, $meta_value, $unique );
}

function update_relationship_meta( $relationship_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( 'kts_object_relationship', $relationship_id, $meta_key, $meta_value, $prev_value );
}

function delete_relationship_meta( $relationship_id, $meta_key, $meta_value = '' ) {
	return delete_metadata( 'kts_object_relationship', $relationship_id, $meta_key, $meta_value );
}

function get_relationship_meta( $relationship_id, $meta_key = '', $single = '' ) {
	return get_metadata( 'kts_object_relationship', $relationship_id, $meta_key, $single );
}
