<?php

/* =============================================================================
   Theme Setup
   ========================================================================== */

add_action( 'after_setup_theme', 'rhm_child_theme_setup' );
function rhm_child_theme_setup(){
	global $content_width;
	$content_width = 660;
	
	// Override the default comments open preference for specified post-types (i.e., pages).
	add_filter( 'default_content', 'rhm_override_comment_default', 10, 2 );
	
	// Ajax registration action.
	//add_action( 'wp_ajax_nopriv_rhm_ajax_register', 'rhm_ajax_register' );

	// Ajax login action.
	add_action( 'wp_ajax_nopriv_rhm_ajax_login', 'rhm_ajax_login' );

	// Ajax data query.
	add_action( 'wp_ajax_nopriv_rhm_data_query', 'rhm_data_query' );
	add_action( 'wp_ajax_rhm_data_query', 'rhm_data_query' );

	// Ajax data locations.
	add_action( 'wp_ajax_nopriv_rhm_data_locations', 'rhm_data_locations' );
	add_action( 'wp_ajax_rhm_data_locations', 'rhm_data_locations' );

	// Ajax reference tags.
	add_action( 'wp_ajax_nopriv_rhm_ref_tag', 'rhm_ref_tag' );
	add_action( 'wp_ajax_rhm_ref_tag', 'rhm_ref_tag' );
	
	// Ajax statistics.
	add_action( 'wp_ajax_nopriv_rhm_stats_request', 'rhm_stats_request' );
	add_action( 'wp_ajax_rhm_stats_request', 'rhm_stats_request' );
	
	// Enqueue Google Maps API script.
	add_action( 'wp_enqueue_scripts', 'rhm_enqueue_google_maps_api' );
	
	// Enqueue jsPlumb.
	add_action( 'wp_enqueue_scripts', 'rhm_enqueue_jsPlumb' );
	
	// Enqueue child theme plugin scripts.
	add_action( 'wp_enqueue_scripts', 'rhm_enqueue_child_scripts' );
	
	// Redirect to home page after logging out.
	add_filter('logout_url', 'rhm_logout_redirect', 10, 2);
	
	// Add shortcodes.
	add_shortcode( 'stats', 'rhm_stats_handler' );
}

// Comments off by default for pages (but not posts).
function rhm_override_comment_default($post_content, $post){
	if($post->post_type)
	switch($post->post_type){
		case 'page':
			$post->comment_status = 'closed';
		break;
	}
	return $post_content;
}

// Handles user authentication.
// Javascript will display error or refresh the page if this returns successfully.
function rhm_ajax_login(){
	$user = wp_signon();
	if(is_wp_error($user)){
		echo $user->get_error_message();
		die();
	} else {
		echo '<strong>Success!</strong> Logging in...';
		die();
	}
}

// Logout redirect to home page.
function rhm_logout_redirect($logouturl, $redirect){
	if(is_admin()){
		$redirect = get_option('siteurl');
		return $logouturl . '&amp;redirect_to=' . urlencode($redirect);
	} else {
		return $logouturl;
	}
}

// Handles registering a new user.
// Based on function in wp-login.php.
//function rhm_ajax_register(){
//	$user_login = '';
//	$user_email = '';
//
//	extract($_POST);  // Should include $user_login and $user_email.
//
//	$errors = new WP_Error();
//
//	$sanitized_user_login = sanitize_user( $user_login );
//	$user_email = apply_filters( 'user_registration_email', $user_email );
//
//	// Check the username
//	if ( $sanitized_user_login == '' ) {
//		$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username.' ) );
//	} elseif ( ! validate_username( $user_login ) ) {
//		$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.' ) );
//		$sanitized_user_login = '';
//	} elseif ( username_exists( $sanitized_user_login ) ) {
//		$errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered. Please choose another one.' ) );
//	}
//
//	// Check the e-mail address
//	if ( $user_email == '' ) {
//		$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.' ) );
//	} elseif ( ! is_email( $user_email ) ) {
//		$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.' ) );
//		$user_email = '';
//	} elseif ( email_exists( $user_email ) ) {
//		$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.' ) );
//	}
//
//	do_action( 'register_post', $sanitized_user_login, $user_email, $errors );
//
//	$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );
//
//	if ( $errors->get_error_code() ) {
//		echo $errors->get_error_message();
//		die();
//	}
//
//	$user_pass = wp_generate_password( 12, false);
//	$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
//	if ( ! $user_id ) {
//		$errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !' ), get_option( 'admin_email' ) ) );
//		echo $errors->get_error_message();
//		die();
//	}
//
//	update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.
//
//	wp_new_user_notification( $user_id, $user_pass );
//
//	echo '<strong>Success!</strong> Your registration is complete. A randomly-generated password has been emailed to you.';
//	
//	die();
//}

add_filter('wpmu_signup_user_notification', 'gomexsi_wpmu_signup_user_notification', 10, 4);
function gomexsi_wpmu_signup_user_notification($user, $user_email, $key, $meta = '') {
	$sitename = get_bloginfo('name');
	$blog_id = get_current_blog_id();
	
	// Send email with activation link.
	$admin_email = get_option('admin_email');
	$from_name = get_option('blogname') == '' ? $sitename : esc_html(get_option('blogname'));
	$message_headers = "From: \"{$from_name}\" <{$admin_email}>\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
	
	$message = apply_filters(
		'wpmu_signup_user_notification_email',
		sprintf(
			"Hi %s,\n\nThank you for registering with %s.\n\nTo activate your account, please click the following link:\n\n%s.",
			$user,
			$sitename,
			site_url("?page=gf_activation&key=$key")
		),
		$user, $user_email, $key, $meta
	);
	
	$subject = apply_filters(
		'wpmu_signup_user_notification_subject',
		sprintf(
			'%s - Activate Your Account',
			$sitename
		),
		$user, $user_email, $key, $meta
	);
	
	wp_mail($user_email, $subject, $message, $message_headers);

	return false;
}

function gomexsi_activate_page_action() {
    add_filter('ngettext', 'gomexsi_activate_page_change_text');
    add_filter('gettext', 'gomexsi_activate_page_change_text');
}
add_action('activate_header', 'gomexsi_activate_page_action');

function gomexsi_activate_page_change_text($text){
    $search_1 = '<a href="%1$s">Log in</a> or go back to the <a href="%2$s">homepage</a>.';
    $replace_1 = '<a href="%2$s">Return to the home page</a> and click the login link in the upper right corner.';
    $text = str_ireplace($search_1, $replace_1, $text);
    $search_2 = '<a href="%1$s">log in</a>';
    $replace_2 = 'log in';
    $text = str_ireplace($search_2, $replace_2, $text);
	return $text;
}

// Handle data query.
function rhm_data_query(){
	include 'data-query-logic.php';
}

// Get all data locations from GloBI.
function rhm_data_locations(){
	include 'data-query-all-locations.php';
}

// Mark a checkbox as checked depending on input value.
function check_if($value_to_check, $check_against = null){
	// If the value to check exists AND
	//     there is nothing to check against OR
	//     there is something to check against which is equivalent to the value to check.
	if($value_to_check && (!$check_against || ($check_against && $value_to_check == $check_against))){
		echo 'checked ';
	}
}

// Mark a select option as selected depending on input value.
function select_if($value_to_check, $check_against){
	if($value_to_check && $value_to_check == $check_against){
		echo 'selected';	
	}
}

// Handle ref_tag click.
function rhm_ref_tag(){
	// REST URL, including the reference tag text.
	$url = 'http://api.globalbioticinteractions.org/findExternalUrlForStudy/' . rawurlencode($_POST['ref_tag']);
	
	// Initialize cURL request.
	$curl = curl_init($url);
	
	// Fail if the other server gives an error.
	curl_setopt($curl, CURLOPT_FAILONERROR, true);
	
	// Return result as string instead of parsing.
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	
	// Execute request and store result.
	$result = curl_exec ($curl);
	
	if(curl_error($curl)){
		$result = curl_error($curl);
	}
	
	// Close.
	curl_close ($curl);
	
	// Output the returned data.
	echo $result;
	
	// Must die here or else WordPress' Ajax system will die('0') afterwards,
	// resulting in a '0' stuck on the end of our returned data.
	die();
}

// Enque Google Maps API on template pages that need it.
function rhm_enqueue_google_maps_api() {
	if(is_page_template('data-query-taxonomic.php') || is_page_template('data-query-spatial.php') || is_page_template('data-query-universal.php')) {
		wp_enqueue_script('rhm_google_maps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCM9HegHcXZLQVXyODY7MdtXZ7BtvO_fyM&sensor=false&libraries=geometry');
		wp_enqueue_script('rhm_marker_clusterer', get_stylesheet_directory_uri() . '/js/marker-clusterer/src/markerclusterer_compiled.js');
	}
}

// Enque jsPlumb on template pages that need it.
function rhm_enqueue_jsPlumb() {
	if(is_page_template('data-query-exploration.php')) {
		// jsPlumb plugin stored in child theme.
		wp_enqueue_script('rhm_jsPlumb', get_stylesheet_directory_uri() . '/js/jsPlumb.js');
	}
}

function rhm_enqueue_child_scripts() {
	// JS plugins stored in child theme.
	wp_enqueue_script('rhm_js_child_plugins', get_stylesheet_directory_uri() . '/js/plugins.js');
	
	// Data Query Scripts
	if(is_page_template('data-query-taxonomic.php') || is_page_template('data-query-spatial.php') || is_page_template('data-query-universal.php') || is_page_template('data-query-exploration.php')) {
		wp_enqueue_script('wicket', get_stylesheet_directory_uri() . '/js/wicket.js');
		wp_enqueue_script('wicket_gmap3', get_stylesheet_directory_uri() . '/js/wicket-gmap3.js', array('wicket'));
		wp_enqueue_script('rhm_data_query', get_stylesheet_directory_uri() . '/js/data-query.js', array('wicket'));
		
		// Pass the current language to javascript.
		if(defined('QTRANSLATE_FILE')){
			wp_localize_script('rhm_data_query', 'qtranx_language', qtranxf_getLanguage());
		} else {
			wp_localize_script('rhm_data_query', 'qtranx_language', 'en');
		}
	}
}

// [stats]
function rhm_stats_handler( $atts, $content = null ) {
	extract( shortcode_atts( array(), $atts ) );
	
	$output = '<div class="stats gradient">';
	$output .= '<div class="container gradient clearfix">';
/* 	$output .= '<div class="single-stat"><div class="stats-visits stats-number">0</div><div class="stats-label">Visits Since Launch</div></div>'; */
/* 	$output .= '<div class="single-stat"><div class="stats-predators stats-number">0</div><div class="stats-label">Predators in Database</div></div>'; */
/* 	$output .= '<div class="single-stat"><div class="stats-prey stats-number">0</div><div class="stats-label">Prey in Database</div></div>'; */
	$output .= '<div class="single-stat first"><div class="stats-studies stats-number">0</div><div class="stats-label">'._q('References/Contributors', 'Referencias/Contribuciones').'</div></div>';
	$output .= '<div class="single-stat"><div class="stats-interactors stats-number">0</div><div class="stats-label">'._q('Unique Interactors', 'Interactores Únicos').'</div></div>';
	$output .= '<div class="single-stat last"><div class="stats-interactions stats-number">0</div><div class="stats-label">'._q('Total Interactions', 'Total de Interacciones').'</div></div>';
	$output .= '</div>';
	$output .= '</div>';
	
	return $output;
}

function rhm_stats_request(){
	$stats = array();
	
/*
	$site_visits_url = 'http://gomexsi.tamucc.edu/piwik/index.php?module=API&method=VisitsSummary.getVisits&idSite=1&period=range&date=2013-04-01,today&format=json&token_auth=f45983179513d9be6f7d4dbe7d23f40c';
	$curl_visitors = curl_init($site_visits_url);				// Initialize cURL request.
	curl_setopt($curl_visitors, CURLOPT_FAILONERROR, true);		// Fail if the other server gives an error.
	curl_setopt($curl_visitors, CURLOPT_RETURNTRANSFER, true);	// Return result as string instead of parsing.
	$site_visits = curl_exec($curl_visitors);					// Execute request and store result.
	if(curl_error($curl_visitors)){
		$site_visits = curl_error($curl_visitors);
		$stats['visits'] = '0';
	} else {
		$site_visits = json_decode($site_visits);
		$stats['visits'] = $site_visits->value;
	}
	curl_close ($curl_visitors);								// Close.
*/
	
	$data_stats_url = 'http://api.globalbioticinteractions.org/reports/sources?type=json&source=http://gomexsi.tamucc.edu';
	$curl_data = curl_init($data_stats_url);					// Initialize cURL request.
	curl_setopt($curl_data, CURLOPT_FAILONERROR, true);			// Fail if the other server gives an error.
	curl_setopt($curl_data, CURLOPT_RETURNTRANSFER, true);		// Return result as string instead of parsing.
	$data_stats = curl_exec($curl_data);						// Execute request and store result.
	if(curl_error($curl_data)){
		$data_stats = curl_error($curl_data);
		$stats['studies'] = '0';
		$stats['interactions'] = '2';
		$stats['predators'] = '0';
		$stats['prey'] = '1';
	} else {
		$data_stats = json_decode($data_stats);
		$stats['studies'] = $data_stats->data[0][6];
		$stats['interactions'] = $data_stats->data[0][4];
		$stats['predators'] = 0;
		$stats['prey'] = 0;
		$stats['interactors'] = $data_stats->data[0][5];
	}
	curl_close ($curl_data);									// Close.
	
	echo json_encode($stats);
	//echo '{"studies": 49, "interactions": 69584, "predators": 273, "prey": 1247, "interactors": 1520}';
	
	die('');
}

// Translation function to return either an English or Spanish string.
function _q($en, $es){
	if(defined('QTRANSLATE_FILE')){
		switch(qtranxf_getLanguage()){
			case 'en':
				return $en;
				break;
			case 'es':
				return $es;
				break;
			default:
				return $en;
		}
	} else {
		return $en;	
	}
}

// Translation function to echo either an English or Spanish string.
function _qe($en, $es){
	echo _q($en, $es);
}