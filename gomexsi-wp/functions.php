<?php

/* =============================================================================
   Theme Setup
   ========================================================================== */

add_action( 'after_setup_theme', 'rhm_child_theme_setup' );
function rhm_child_theme_setup(){
	// Override the default comments open preference for specified post-types (i.e., pages).
	add_filter( 'default_content', 'rhm_override_comment_default', 10, 2 );
	
	// Ajax registration action.
	add_action( 'wp_ajax_nopriv_rhm_ajax_register', 'rhm_ajax_register' );

	// Ajax login action.
	add_action( 'wp_ajax_nopriv_rhm_ajax_login', 'rhm_ajax_login' );

	// Ajax data query.
	add_action( 'wp_ajax_nopriv_rhm_data_query', 'rhm_data_query' );
	add_action( 'wp_ajax_rhm_data_query', 'rhm_data_query' );
	
	// Enqueue Google Maps API script.
	add_action( 'wp_enqueue_scripts', 'rhm_enqueue_google_maps_api' );
	
	// Enqueue jsPlumb.
	add_action( 'wp_enqueue_scripts', 'rhm_enqueue_jsPlumb' );
	
	// Enqueue child theme plugin scripts.
	add_action( 'wp_enqueue_scripts', 'rhm_enqueue_child_scripts' );
	
	// Redirect to home page after logging out.
	add_filter('logout_url', 'rhm_logout_redirect', 10, 2);
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
function rhm_ajax_register(){
	$user_login = '';
	$user_email = '';

	extract($_POST);  // Should include $user_login and $user_email.

	$errors = new WP_Error();

	$sanitized_user_login = sanitize_user( $user_login );
	$user_email = apply_filters( 'user_registration_email', $user_email );

	// Check the username
	if ( $sanitized_user_login == '' ) {
		$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username.' ) );
	} elseif ( ! validate_username( $user_login ) ) {
		$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.' ) );
		$sanitized_user_login = '';
	} elseif ( username_exists( $sanitized_user_login ) ) {
		$errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered. Please choose another one.' ) );
	}

	// Check the e-mail address
	if ( $user_email == '' ) {
		$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.' ) );
	} elseif ( ! is_email( $user_email ) ) {
		$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.' ) );
		$user_email = '';
	} elseif ( email_exists( $user_email ) ) {
		$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.' ) );
	}

	do_action( 'register_post', $sanitized_user_login, $user_email, $errors );

	$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );

	if ( $errors->get_error_code() ) {
		echo $errors->get_error_message();
		die();
	}

	$user_pass = wp_generate_password( 12, false);
	$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
	if ( ! $user_id ) {
		$errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !' ), get_option( 'admin_email' ) ) );
		echo $errors->get_error_message();
		die();
	}

	update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.

	wp_new_user_notification( $user_id, $user_pass );

	echo '<strong>Success!</strong> Your registration is complete. A randomly-generated password has been emailed to you.';
	
	die();
}

// Handle data query.
function rhm_data_query(){
	include 'data-query-logic.php';
}

// Enque Google Maps API on template pages that need it.
function rhm_enqueue_google_maps_api() {
	if(is_page_template('data-query-taxonomic.php') || is_page_template('data-query-spatial.php')) {
		wp_enqueue_script('rhm_google_maps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCM9HegHcXZLQVXyODY7MdtXZ7BtvO_fyM&sensor=false');
	}
}

// Enque Google Maps API on template pages that need it.
function rhm_enqueue_jsPlumb() {
	if(is_page_template('data-query-exploration.php')) {
		// jsPlumb plugin stored in child theme.
		wp_enqueue_script('rhm_jsPlumb', get_stylesheet_directory_uri() . '/js/jsPlumb.js');
	}
}

function rhm_enqueue_child_scripts() {
	// JS plugins stored in child theme.
	wp_enqueue_script('rhm_js_child_plugins', get_stylesheet_directory_uri() . '/js/plugins.js');
}

