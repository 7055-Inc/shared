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
	protected $elements;

	/**
	 * CSS formatting for the mail template
	 * @var string[]
	 */
	protected $stylesheets = array(
		'p' => 'font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;',
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
		foreach ( $this->stylesheets as $tag => $style ) {
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
	public function addButton( $text, $link, $target = '_blank',  $position = 'left', $textColor = '#FFFFFF', $bgColor = '#055474' ) {
		$style = sprintf("display:inline-block;padding:15px 10px;text-decoration:none;color:%s,background-color:%s", $textColor, $bgColor);
		$button = '<a href="'.$link.'" target="'.$target.'" style="'.$style.'">'.$text.'</a>';
		$pstyle = sprintf("font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;text-align:%s", $position);
		$paragraph = '<p style="'.$pstyle.'">'.$button.'</p>';
		$this->add($paragraph, false);
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