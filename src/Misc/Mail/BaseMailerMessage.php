<?php


namespace The7055inc\Shared\Misc\Mail;

/**
 * Class BaseMailerMessage
 * @package The7055inc\Shared\Misc\Mail
 */
class BaseMailerMessage {

	/**
	 * List of message titles, paragraphs, etc.
	 * @var array $elements
	 */
	protected $elements = array();

	/**
	 * CSS formatting for the mail template
	 * @var string[]
	 */
	protected $styles = array(
		'h1' => 'margin-top:0;margin-bottom:15px;',
		'h2' => 'margin-top:0;margin-bottom:15px;',
		'h3' => 'margin-top:0;margin-bottom:15px;',
		'h4' => 'margin-top:0;margin-bottom:15px;',
		'h5' => 'margin-top:0;margin-bottom:15px;',
		'p'  => 'font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 10px; line-height: 1.3;',
	);


	/**
	 * Use stylesheets?
	 * @var bool
	 */
	protected $formatting = true;

	/**
	 * Enable / disable formatting
	 *
	 * @param $value
	 */
	public function setFormatting( $value ) {
		$this->formatting = (bool) $value;
	}

	/**
	 * Basic formatting with CSS
	 *
	 * @param $message
	 *
	 * @return string
	 */
	protected function applyStyle( $message ) {
		foreach ( $this->styles as $tag => $style ) {
			$message = str_replace( '<' . $tag . '>', '<' . $tag . ' style="' . $style . '">', $message );
		}

		return $message;
	}

	/**
	 * Add title to the document
	 *
	 * @param $type
	 * @param $title
	 */
	public function addTitle( $type, $title ) {
		$title = '<' . $type . '>' . $title . '</' . $type . '>';
		$this->add( $title );
	}

	/**
	 * Add paragraph to the document
	 *
	 * @param $paragraph
	 */
	public function addParagraph( $paragraph ) {
		$paragraph = '<p>' . $paragraph . '</p>';
		$this->add( $paragraph );
	}

	/**
	 * Add button to the document
	 *
	 * @param $text
	 * @param $link
	 * @param string $target
	 * @param string $position
	 * @param string $textColor
	 * @param string $bgColor
	 */
	public function addButton( $text, $link, $target = '_blank', $position = 'left', $textColor = '#FFFFFF', $bgColor = '#055474' ) {
		$style     = sprintf( "display:inline-block;padding:10px 20px;text-decoration:none;color:%s;background-color:%s", $textColor, $bgColor );
		$button    = '<a href="' . $link . '" target="' . $target . '" style="' . $style . '">' . $text . '</a>';
		$position = 'text-align:'.$position.';';
		$pstyle    = "display:inline-block;width:100%;font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;margin-bottom:10px;{$position}";
		$paragraph = '<p style="' . $pstyle . '">' . $button . '</p>';
		$this->add( $paragraph, false );
	}

	/**
	 * Add element to the elements array
	 *
	 * @param $element
	 * @param bool $applyCSS
	 */
	public function add( $element, $applyCSS = true ) {
		if ( $this->formatting && $applyCSS ) {
			$element = $this->applyStyle( $element );
		}

		array_push( $this->elements, $element );
	}

	/**
	 * Prepare a message for print
	 *
	 * @return string
	 */
	public function toString() {
		$final = '';
		foreach ( $this->elements as $element ) {
			$final .= $element . PHP_EOL;
		}

		return $final;
	}
}