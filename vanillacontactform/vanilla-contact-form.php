<?php

/*
Plugin Name: Vanilla Contact Form
Description: A bare-bones WordPress contact form with Bootstrap 4 styling. Simple and sweet, like Vanilla!
Version: 1.0
Author: Dominique Moody
Author URI: https://dominiquethedeveloper.com
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Exit if accesed directly
if(!defined('ABSPATH')) {
  exit;
}

// Add Bootstrap 4 styles
function vcf_add_styles() {
  wp_enqueue_style('vcf_bootstrap', plugins_url('bootstrap.min.css', __FILE__));
}

add_action('wp_enqueue_scripts', 'vcf_add_styles');

function vcf_form_code() {
	echo '<form class="text-center" style="margin: 50px auto;" action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
	echo '<div class="form-group">';
	echo '<input class="form-control" type="text" pattern="[a-zA-Z0-9 ]+" name="vcf_name" placeholder="Name" value="' . ( isset( $_POST["vcf_name"] ) ? esc_attr( $_POST["vcf_name"] ) : '' ) . '" required  />';
	echo '</div>';
	echo '<div class="form-group">';
	echo '<input class="form-control" type="email" name="vcf_email" placeholder="Email" value="' . ( isset( $_POST["vcf_email"] ) ? esc_attr( $_POST["vcf_email"] ) : '' ) . '" required />';
	echo '</div>';
	echo '<div class="form-group">';
	echo '<input class="form-control" type="text" pattern="[a-zA-Z0-9 ]+" name="vcf_subject" placeholder="Subject" value="' . ( isset( $_POST["vcf_subject"] ) ? esc_attr( $_POST["vcf_subject"] ) : '' ) . '" />';
	echo '</div>';
	echo '<div class="form-group">';
	echo '<textarea class="form-control" name="vcf_message" rows="8" cols="80" placeholder="Message"' . ( isset( $_POST["vcf_message"] ) ? esc_attr( $_POST["vcf_message"] ) : '' ) . ' required >';
	echo '</textarea>';
  echo '</div>';
	echo '<button type="submit" class="btn btn-dark" name="vcf_submit">Submit</button>';
	echo '</form>';
}

function vcf_send_mail() {

	// if the submit button is clicked, send the email
	if ( isset( $_POST['vcf_submit'] ) ) {

		// sanitize form values
		$name    = sanitize_text_field( $_POST["vcf_name"] );
		$email   = sanitize_email( $_POST["vcf_email"] );
		$subject = sanitize_text_field( $_POST["vcf_subject"] );
		$message = sanitize_textarea_field( $_POST["vcf_message"] );

		// get the blog administrator's email address
		$to = get_option( 'admin_email' );

    $headers = "MIME-Version: 1.0" ."\r\n";
    $headers .= "Content-Type: text/html; charset=utf-8" ."\r\n";
    $headers .= "From: " .$name. "<".$email.">". "\r\n";
    $headers .= "Reply-To: $email \r\n";
    $headers .= "X-Priority: 3\r\n";

		// If email has been process for sending, display a success message
		if ( wp_mail( $to, $subject, $message, $headers ) ) {
			echo '<div class="alert alert-success" role="alert">';
			echo '<p>Your message has been sent!</p>';
			echo '</div>';
		} else {
      echo '<div class="alert alert-danger" role="alert">';
			echo '<p>Ooops! Something went wrong.</p>';
      echo '</div>';
		}
	}
}

function vcf_shortcode() {
	ob_start();
	vcf_send_mail();
	vcf_form_code();

	return ob_get_clean();
}

add_shortcode( 'vanilla_contact_form', 'vcf_shortcode' );
