<?php


/**
 * Framework: hook into APP_View_Page::_get_id()
 */
function app_wpml_appthemes_page_id_for_template( $page_id, $template ) {
	return icl_object_id( $page_id, 'page', true );
}
add_filter( 'appthemes_page_id_for_template', 'app_wpml_appthemes_page_id_for_template', 10, 2 );


/**
 * ClassiPress: hook into cp_get_ad_details()
 */
function app_wpml_cp_ad_details_field( $result, $post, $location ) {
	$result->field_label = icl_translate( APP_TD, 'label_' . $result->field_name, $result->field_label );
	return $result;
}
add_filter( 'cp_ad_details_field', 'app_wpml_cp_ad_details_field', 10, 3 );


/**
 * ClassiPress: hook into cp_formbuilder(), cp_formbuilder_review()
 */
function app_wpml_cp_formbuilder_field( $result ) {
	$result->field_label = icl_translate( APP_TD, 'label_' . $result->field_name, $result->field_label );
	if ( ! empty( $result->field_tooltip ) )
		$result->field_tooltip = icl_translate( APP_TD, 'tooltip_' . $result->field_name, $result->field_tooltip );
	return $result;
}
add_filter( 'cp_formbuilder_field', 'app_wpml_cp_formbuilder_field' );
add_filter( 'cp_formbuilder_review_field', 'app_wpml_cp_formbuilder_field' );


/**
 * ClassiPress: hook into cp_package_field filter
 */
function app_wpml_cp_package_field( $result, $type ) {
	$result->pack_name = icl_translate( APP_TD, 'pack_name_' . $result->pack_id, $result->pack_name );
	$result->pack_desc = icl_translate( APP_TD, 'pack_desc_' . $result->pack_id, $result->pack_desc );
	return $result;
}
add_filter( 'cp_package_field', 'app_wpml_cp_package_field', 10, 2 );


/**
 * ClassiPress: hook into get_pack()
 */
function app_wpml_cp_get_package( $package, $pack_id ) {
	if ( ! empty( $package ) ) {
		$package->pack_name = icl_translate( APP_TD, 'pack_name_' . $package->pack_id, $package->pack_name );
		$package->pack_desc = icl_translate( APP_TD, 'pack_desc_' . $package->pack_id, $package->pack_desc );
	}
	return $package;
}
add_filter( 'cp_get_package', 'app_wpml_cp_get_package', 10, 2 );


/**
 * ClassiPress: hook into cp_display_message()
 */
function app_wpml_cp_display_message( $message, $tag ) {
	return icl_translate( APP_TD, 'message_' . $tag, $message );
}
add_filter( 'cp_display_message', 'app_wpml_cp_display_message', 10, 2 );


