<?php


/**
 * Framework: hook into APP_View_Page::_get_id()
 */
function app_wpml_appthemes_page_id_for_template( $page_id, $template ) {
	return icl_object_id( $page_id, 'page', true );
}
add_filter( 'appthemes_page_id_for_template', 'app_wpml_appthemes_page_id_for_template', 10, 2 );


/**
 * ClassiPress: hook into cp_add_new_listing(), set proper language for new listing
 */
function app_wpml_cp_add_new_listing( $post_id ) {
	global $sitepress;

	if ( $sitepress->get_current_language() == $sitepress->get_default_language() )
		return;

	$post_type = get_post_type( $post_id );
	$sitepress->set_element_language_details( $post_id, 'post_' . $post_type, null, $sitepress->get_current_language() );
}
add_action( 'cp_action_add_new_listing', 'app_wpml_cp_add_new_listing' );

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
	if ( ! empty( $result->field_values ) )  {
	    $options = explode(',',$result->field_values);
	    $new_options = array();
	    foreach ($options as $value) {
			$new_options[] = icl_t('classipress','value_'.$result->field_name." ".trim($value),$value);
	    }

	    $result->field_values = implode($new_options,',');
	}
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


/**
 * ClassiPress: language selector (for frontend edit-ad and order pages)
 *
 */
function app_wpml_ls($languages) {
	global $sitepress, $post;
	$lang_code = $sitepress->get_current_language();

	if (isset($_GET['aid'])) {
		// edit ad
		$aid = $_GET['aid'];
		$trid = $sitepress->get_element_trid($aid,'post_ad_listing');
		$translations = $sitepress->get_element_translations($trid,'post_ad_listing');

		foreach ($translations as $code => $translation) {
			if ($code != $lang_code) {
				$translated_aid = $translation->element_id;
				$edit_page = $sitepress->language_url($code).'edit-item/';
				$url = add_query_arg('aid',$translated_aid,$edit_page);
				$languages[$code]['url'] = $url;
			}
		}
	} else if (is_singular() && empty($languages) && get_post_type() == 'transaction') {
		//orders
		remove_filter('icl_ls_languages','app_wpml_ls');
		$languages = $sitepress->get_ls_languages(array('skip_missing' => false));
		$url = get_permalink($post->ID);
		foreach ($languages as $code => &$lang) {
			$lang['url'] = $sitepress->convert_url($url,$code);
		}
		add_filter('icl_ls_languages','app_wpml_ls');
	}
	return $languages;
}

add_filter('icl_ls_languages','app_wpml_ls');

/**
* Classipress: filter registration url
*/
function app_wpml_register($link) {
	//<p class="register"><a href="http://localhost/compat/wp-login.php?action=register">Registrarse</a></p>
	$url = appthemes_get_registration_url();
	return preg_replace('/href=".*"/s','href="'.$url.'"',$link);
}
add_filter('register','app_wpml_register');

/**
* Classipress: show categories in all languages for form layouts
* This way one can define a single form for all languages, with custom
* field labels translated through string translation
*/



function show_all_categories_in_form_properties() {
	if (isset($_GET['action']) &&
		in_array($_GET['action'], array('editform','addform')) &&
		(!isset($_GET['lang']))) { //|| $_GET['lang'] != 'all')
		$url = add_query_arg('lang','all');
		wp_redirect($url);
	}
}

add_action('init','show_all_categories_in_form_properties');

function app_wpml_custom_fields($action,$field_id) {
	global $wpdb;

	$query = "SELECT * FROM {$wpdb->prefix}cp_ad_fields WHERE field_id=%d";
	$field = $wpdb->get_row($wpdb->prepare($query,$field_id));

	switch($action) {
		case 'addfield':
		case 'editfield':
			icl_register_string('classipress','label_'.$field->field_name,$field->field_label);
			icl_register_string('classipress','tooltip_'.$field->field_name,$field->field_tooltip);
			if (!empty($field->field_values)) {
				$options = array_map('trim',explode( ',', $field->field_values ));
				foreach($options as $val) {
					icl_register_string('classipress','value_'.$field->field_name." $val",$val);
				}
			}
		break;
		case 'delete':
			icl_unregister_string('classipress','label_'.$field->field_name,$field->field_label);
			icl_unregister_string('classipress','tooltip_'.$field->field_name,$field->field_tooltip);
			if (!empty($field->field_values)) {
				$options = array_map('trim',explode( ',', $field->field_values ));
				foreach($options as $val) {
					icl_unregister_string('classipress','value_'.$field->field_name." $val",$val);
				}
			}
		break;
		default: error_log('Invalid action in '.__FUNCTION__);
		break;
	}

}

add_action('cp_custom_fields','app_wpml_custom_fields',10,2);

function app_wpml_custom_field_values($options, $field_name) {
	$translated_options = array();
	foreach ($options as $value) {
		$translated_options[] = icl_t('classipress','value_'.$field_name,$value);
	}

	return $translated_options;
}

add_filter('cp_custom_field_values','app_wpml_custom_field_values');

function app_wpml_convert_url($url) {
	global $sitepress;
	return $sitepress->convert_url($url);
}

add_filter( 'appthemes_order_return_url','app_wpml_convert_url');
//add_filter('register','app_wpml_convert_url');

/**
* Orders
*/

function app_wpml_create_order($order_id,$order) {
    global $sitepress;
    if ($order->post_type != 'transaction')
		return;
    $sitepress->set_element_language_details( $order_id, 'post_transaction', null, $sitepress->get_current_language() );
}

add_action('save_post','app_wpml_create_order',20,2); //after WPML's save_post_actions

