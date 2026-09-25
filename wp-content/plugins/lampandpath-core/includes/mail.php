<?php
/**
 * Sends WordPress email through an SMTP server set in the environment.
 *
 * The production container (Dockerfile) has no mail server, so without this
 * prayer request notifications and password resets would never leave it.
 * Nothing changes unless SMTP_HOST is set, so local DDEV (which catches mail
 * in Mailpit) and other hosts keep their own mail setup.
 *
 * Environment: SMTP_HOST, SMTP_PORT (default 587), SMTP_SECURE ("tls" by
 * default, "ssl", or "none"), SMTP_USER and SMTP_PASSWORD (when the server
 * needs a login), and SMTP_FROM (sender address, often required to be the SMTP
 * account's own address). A login is never sent unencrypted: with SMTP_USER
 * set, anything but "ssl" means STARTTLS.
 *
 * @package Lampandpath_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Points PHPMailer at the SMTP server from the environment.
 *
 * @param PHPMailer\PHPMailer\PHPMailer $phpmailer Mailer about to send.
 */
function lampandpath_core_smtp( $phpmailer ) {
	$host = (string) getenv( 'SMTP_HOST' );
	if ( '' === $host ) {
		return;
	}

	$port   = (int) getenv( 'SMTP_PORT' );
	$secure = getenv( 'SMTP_SECURE' );
	$secure = false === $secure ? 'tls' : strtolower( trim( $secure ) );
	$user   = (string) getenv( 'SMTP_USER' );

	// Anything but "tls" or "ssl" ("none", empty) means no encryption, which
	// PHPMailer still upgrades with STARTTLS whenever the server offers it.
	// Credentials must not travel in clear text, so a login requires encryption.
	if ( ! in_array( $secure, array( 'tls', 'ssl' ), true ) ) {
		$secure = '' !== $user ? 'tls' : '';
	}

	// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- PHPMailer's property names.
	$phpmailer->isSMTP();
	$phpmailer->Host       = $host;
	$phpmailer->Port       = $port ? $port : 587;
	$phpmailer->SMTPSecure = $secure;
	// Mail goes out while the visitor waits for a form to submit, so give up on
	// an unreachable server after seconds rather than PHPMailer's 5 minutes.
	$phpmailer->Timeout = 10;
	if ( '' !== $user ) {
		$phpmailer->SMTPAuth = true;
		$phpmailer->Username = $user;
		$phpmailer->Password = (string) getenv( 'SMTP_PASSWORD' );
	}
	// phpcs:enable
}
add_action( 'phpmailer_init', 'lampandpath_core_smtp' );

/**
 * The sender address from SMTP_FROM, when mail goes through SMTP_HOST.
 *
 * @return string Valid address, or an empty string to keep WordPress's sender.
 */
function lampandpath_core_smtp_from_address() {
	$address = (string) getenv( 'SMTP_FROM' );

	return ( '' !== (string) getenv( 'SMTP_HOST' ) && is_email( $address ) ) ? $address : '';
}

/**
 * Sends from SMTP_FROM when it is set.
 *
 * Many SMTP accounts only accept mail from their own address, which is often
 * not WordPress's default (wordpress@ and the site's domain). This filter runs
 * before WordPress hands the address to PHPMailer.
 *
 * @param string $from Default sender address.
 * @return string
 */
function lampandpath_core_smtp_from( $from ) {
	$address = lampandpath_core_smtp_from_address();

	return '' !== $address ? $address : $from;
}
add_filter( 'wp_mail_from', 'lampandpath_core_smtp_from' );

/**
 * Names the sender after the site, instead of "WordPress", when SMTP_FROM is set.
 *
 * @param string $name Default sender name.
 * @return string
 */
function lampandpath_core_smtp_from_name( $name ) {
	return ( 'WordPress' === $name && '' !== lampandpath_core_smtp_from_address() ) ? wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) : $name;
}
add_filter( 'wp_mail_from_name', 'lampandpath_core_smtp_from_name' );
