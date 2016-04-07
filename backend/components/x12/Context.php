<?php
namespace backend\components\x12;

/**
 * The class represents an X12 context. A X12 context consists of a segment
 * separator, element separator and a composite element separator.
 */
class Context {
	private $s;
	private $e;
	private $c;

	/**
	 * Constructor which takes the segment separator, element separator and
	 * composite element separator as input.
	 * 
	 * @param s
	 *            segment separator
	 * @param e
	 *            element separator
	 * @param c
	 *            composite element separator
	 */
	public function __construct() {
		$a = func_get_args();
		$i = func_num_args();
		if($i == 3 && method_exists($this, $f = 'init')) {
			call_user_func_array(array($this, $f), $a);
		}
	}

	public function init($s, $e, $c) {
		$this->s = $s;
		$this->e = $e;
		$this->c = $c;
	}

	/**
	 * Returns the composite element separator.
	 * 
	 * @return composite element separator
	 */
	public function getCompositeElementSeparator() {
		return $this->c;
	}

	/**
	 * Returns the element separator.
	 * 
	 * @return an element separator
	 */
	public function getElementSeparator() {
		return $this->e;
	}

	/**
	 * Returns the segment separator.
	 * 
	 * @return a segment separator
	 */
	public function getSegmentSeparator() {
		return $this->s;
	}

	/**
	 * Sets the composite element separator.
	 * 
	 * @param c
	 *            the composite element separator.
	 */
	public function setCompositeElementSeparator($c) {
		$this->c = $c;
	}

	/**
	 * Sets the element separator.
	 * 
	 * @param e
	 *            the element separator.
	 */
	public function setElementSeparator($e) {
		$this->e = $e;
	}

	/**
	 * Sets the segment separator.
	 * 
	 * @param s
	 *            the segment separator
	 */
	public function setSegmentSeparator($s) {
		$this->s = $s;
	}

	/**
	 * Returns a <code>String</code> consisting of segment, element and
	 * composite element separator.
	 */
	public function toString() {
		return "[" . $this->s . "," . $this->e . "," . $this->c . "]";
	}

}
