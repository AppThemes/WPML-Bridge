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
	$result->field_label = icl_translate( APP_TD, 'label_' . $result->field_label, $result->field_label );
	return $result;
}
add_filter( 'cp_ad_details_field', 'app_wpml_cp_ad_details_field', 10, 3 );


/**
 * ClassiPress: hook into cp_formbuilder()
 */
function app_wpml_cp_formbuilder_field( $result, $post ) {
	$result->field_label = icl_translate( APP_TD, 'label_' . $result->field_label, $result->field_label );
	return $result;
}
add_filter( 'cp_formbuilder_field', 'app_wpml_cp_formbuilder_field', 10, 2 );


