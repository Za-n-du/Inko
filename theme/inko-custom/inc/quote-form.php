<?php
/**
 * Quotation request form handler (slide 09).
 *
 * Mirrors the fields of the original site's Contact_us form: Name, Email,
 * Phone, File Upload, Details. Posts to admin-post.php, validates, and sends
 * the request to the studio with wp_mail() (attachment included, Reply-To
 * set to the client). For reliable delivery in production install an SMTP
 * plugin; WordPress' default mailer is often filtered as spam.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

const INKO_QUOTE_MAX_BYTES = 4194304; // 4 MB.

/**
 * Allowed upload types: extension => mime.
 *
 * @return array
 */
function inko_quote_mimes() {
	return array(
		'jpg|jpeg' => 'image/jpeg',
		'png'      => 'image/png',
		'webp'     => 'image/webp',
		'heic'     => 'image/heic',
		'pdf'      => 'application/pdf',
	);
}

/**
 * Redirects back to the form with a status code.
 *
 * @param string $status sent|invalid|file|error|wait.
 */
function inko_quote_redirect( $status ) {
	$back = wp_get_referer();
	if ( ! $back || wp_parse_url( $back, PHP_URL_HOST ) !== wp_parse_url( home_url(), PHP_URL_HOST ) ) {
		$back = home_url( '/' );
	}
	$back = remove_query_arg( 'quote', strtok( $back, '#' ) );
	wp_safe_redirect( add_query_arg( 'quote', $status, $back ) . '#quote' );
	exit;
}

/**
 * Handles the submission.
 */
function inko_quote_handle() {
	if ( ! isset( $_POST['inko_quote_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['inko_quote_nonce'] ) ), 'inko_quote' ) ) {
		inko_quote_redirect( 'error' );
	}

	// Honeypot: real visitors never see or fill this field.
	if ( ! empty( $_POST['website'] ) ) {
		inko_quote_redirect( 'sent' );
	}

	// Light rate limit per IP (60 seconds).
	$ip_key = 'inko_quote_' . md5( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown' );
	if ( get_transient( $ip_key ) ) {
		inko_quote_redirect( 'wait' );
	}

	$first   = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
	$last    = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$details = isset( $_POST['details'] ) ? sanitize_textarea_field( wp_unslash( $_POST['details'] ) ) : '';

	if ( '' === $first || '' === $last || ! is_email( $email ) || '' === trim( $details ) ) {
		inko_quote_redirect( 'invalid' );
	}

	// Optional reference file.
	$attachment = '';
	if ( ! empty( $_FILES['reference']['name'] ) ) {
		$file = $_FILES['reference']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- validated below.

		if ( UPLOAD_ERR_OK !== (int) $file['error'] || (int) $file['size'] > INKO_QUOTE_MAX_BYTES || ! is_uploaded_file( $file['tmp_name'] ) ) {
			inko_quote_redirect( 'file' );
		}

		$check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], inko_quote_mimes() );
		if ( empty( $check['ext'] ) || empty( $check['type'] ) ) {
			inko_quote_redirect( 'file' );
		}

		$name       = sanitize_file_name( 'reference-' . gmdate( 'Ymd-His' ) . '.' . $check['ext'] );
		$attachment = trailingslashit( get_temp_dir() ) . wp_unique_filename( get_temp_dir(), $name );
		if ( ! move_uploaded_file( $file['tmp_name'], $attachment ) ) {
			inko_quote_redirect( 'file' );
		}
	}

	$studio = inko_content( 'studio' );
	$to     = apply_filters( 'inko_quote_recipient', $studio['email'] );
	$client = trim( $first . ' ' . $last );

	$body  = "New quotation request from the website\n\n";
	$body .= "Name: {$client}\n";
	$body .= "Email: {$email}\n";
	$body .= 'Phone: ' . ( '' !== $phone ? $phone : '—' ) . "\n\n";
	$body .= "Details:\n{$details}\n";
	$body .= $attachment ? "\nReference file attached.\n" : "\nNo reference file.\n";

	$headers = array( 'Reply-To: ' . $client . ' <' . $email . '>' );

	$sent = wp_mail( $to, 'Quotation request — ' . $client, $body, $headers, $attachment ? array( $attachment ) : array() );

	if ( $attachment && file_exists( $attachment ) ) {
		wp_delete_file( $attachment );
	}

	if ( ! $sent ) {
		inko_quote_redirect( 'error' );
	}

	set_transient( $ip_key, 1, MINUTE_IN_SECONDS );
	inko_quote_redirect( 'sent' );
}
add_action( 'admin_post_nopriv_inko_quote', 'inko_quote_handle' );
add_action( 'admin_post_inko_quote', 'inko_quote_handle' );

/**
 * Current status message (from the redirect query arg).
 *
 * @return array{type:string,text:string}|null
 */
function inko_quote_status() {
	if ( empty( $_GET['quote'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return null;
	}

	$messages = array(
		'sent'    => array( 'ok', __( 'Thank you — your request has been sent. We’ll get back to you with a personalised quote.', 'inko-custom' ) ),
		'invalid' => array( 'error', __( 'Please fill in your first name, last name, a valid email and the details of your tattoo.', 'inko-custom' ) ),
		'file'    => array( 'error', __( 'The file could not be accepted. Please attach a JPG, PNG, WEBP, HEIC or PDF up to 4 MB.', 'inko-custom' ) ),
		'wait'    => array( 'error', __( 'You just sent a request. Please wait a minute before sending another.', 'inko-custom' ) ),
		'error'   => array( 'error', __( 'Sorry, your request could not be sent. Please email or call us instead.', 'inko-custom' ) ),
	);

	$key = sanitize_key( wp_unslash( $_GET['quote'] ) ); // phpcs:ignore WordPress.Security.NonceVerification

	return isset( $messages[ $key ] ) ? array( 'type' => $messages[ $key ][0], 'text' => $messages[ $key ][1] ) : null;
}
