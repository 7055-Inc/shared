<?php

namespace The7055inc\Shared\Misc\Mail;

/**
 * Class Mailer
 * @package The7055inc\Shared\Misc
 */
abstract class BaseMailer {

	/**
	 * Enabel or disable html emails
	 * @var bool
	 */
	protected $html = true;

	/**
	 * Use stylesheets?
	 * @var bool
	 */
	protected $use_stylesheets = true;

	/**
	 * CSS formatting for the mail template
	 * @var string[]
	 */
	protected $stylesheets = array(
		'p' => 'font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;',
	);

	/**
	 * Send email
	 *
	 * @param $to
	 * @param $subject
	 * @param $message
	 * @param string $headers
	 * @param array $attachemnts
	 *
	 * @return mixed
	 */
	public function send( $to, $subject, $message, $headers = '', $attachemnts = array() ) {

		if ( $this->html ) {
			$message = $this->toHtml( $message );
			if ( $this->use_stylesheets ) {
				$message = $this->applyCSS( $message );
			}
			add_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );
		}
		$result = wp_mail( $to, $subject, $message, $headers, $attachemnts );

		if ( $this->html ) {
			remove_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );
		}

		return $result;
	}

	/**
	 * The mail content
	 *
	 * @param $message
	 *
	 * @return mixed
	 */
	protected function toHtml( $message ) {
		return $message;
	}

	/**
	 * Basic formatting with CSS
	 *
	 * @param $message
	 *
	 * @return string
	 */
	protected function applyCSS( $message ) {
		foreach ( $this->stylesheets as $tag => $style ) {
			$message = str_replace( '<' . $tag . '>', '<' . $tag . ' style="' . $style . '">', $message );
		}

		return $message;
	}

	/**
	 * Set HTML content type
	 * @return string
	 */
	public function set_html_content_type() {
		return 'text/html';
	}

}