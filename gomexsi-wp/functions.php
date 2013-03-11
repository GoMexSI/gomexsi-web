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
}


function rhm_override_comment_default($post_content, $post){
	if($post->post_type)
	switch($post->post_type){
		case 'page':
			$post->comment_status = 'closed';
		break;
	}
	return $post_content;
}


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

/**
 * Based on function in wp-login.php
 * Handles registering a new user.
 *
 * @param string $user_login User's username for logging in
 * @param string $user_email User's email address to send password and add
 * @return int|WP_Error Either user's ID or error on failure.
 */
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