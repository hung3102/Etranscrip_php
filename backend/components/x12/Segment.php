<?php
namespace backend\components\x12;

/**
 * This class represents an X12 segment.
 */
class Segment{
	private static $serialVersionUID; // long type
	const EMPTY_STRING = "";
	
	private $context; // context type
	private $elements = []; //string type

	public function __construct() {
		$a = func_get_args();
		$i = func_num_args();
		if($i == 1 && method_exists($this, $f = 'init')) {
			call_user_func_array(array($this, $f), $a);
		}
	}

	public function __call($method, $arguments) {
		$arg_nums = count($arguments);
		switch ($method) {
			case 'addElement':
				if($arg_nums == 1) {
					return call_user_func_array(array($this, 'addElement1'), $arguments);
				} else if($arg_nums == 2) {
					return call_user_func_array(array($this, 'addElement2'), $arguments);
				}
				break;

			case 'addElements':
				if($arg_nums == 1 && !is_array($arguments)) {
					return call_user_func_array(array($this, 'addElements1String'), $arguments);
				} else if($arg_nums == 1 && is_array($arguments)) {
					return call_user_func_array(array($this, 'addElements1Array'), $arguments);
				} else if($arg_nums >= 2) {
					return call_user_func_array(array($this, 'addElementsN'), $arguments);
				}	
				break;

			case 'addCompositeElement':
				if(!is_int($arguments[0])) {
					if($arg_nums == 1 && is_array($arguments[0])) {
						return call_user_func_array(array(
								$this, 'addCompositeElement1Array'
							), $arguments);	
					} else {
						return call_user_func_array(array($this, 'addCompositeElementN'), $arguments);
					}
					
				} else if(is_int($arguments[0])) {
					if(is_array($arguments[1])) {
						return call_user_func_array(array(
								$this, 'addCompositeElement2Array'
							), $arguments);	
					} else {
						return call_user_func_array(array($this, 'addCompositeElement2N'), $arguments);
					}
				}
				break;

			case 'setCompositeElement':
				if(is_array($arguments[1])) {
					return call_user_func_array(array($this, 'setCompositeElement2Array'), $arguments);
				} else {
					return call_user_func_array(array($this, 'setCompositeElement2N'), $arguments);
				}
				break;

			case 'toString':
				if($arg_nums == 0) {
					return call_user_func_array(array($this, 'toString0'), $arguments);
				} else if($arg_nums == 1) {
					return call_user_func_array(array($this, 'toString1'), $arguments);
				}
				break;

			case 'toXML':
				if($arg_nums == 0) {
					return call_user_func_array(array($this, 'toXML0'), $arguments);
				} else if($arg_nums == 1) {
					return call_user_func_array(array($this, 'toXML1'), $arguments);
				}
				break;
		
			default:
				break;
		}
	}

	/**
	 * The constructor takes a <code>Context</code> object as input. The context
	 * object represents the delimiters in a X12 transaction.
	 * 
	 * @param c
	 *            the context object
	 */
	public function init($c) {
		$this->context = $c;
	}

	/**
	 * Adds <code>String</code> element to the segment. The element is added at
	 * the end of the elements in the current segment.
	 * 
	 * @param e
	 *            the element to be added
	 * @return boolean
	 */
	public function addElement1($e) {
		if($this->elements[] = $e) {
			return true;
		}
		return false;
	}

	/**
	 * Inserts <code>String</code> element to the segment at the specified
	 * position
	 * 
	 * @param e
	 *            the element to be added
	 * @return boolean
	 */
	public function addElement2($index, $e) {
		if(array_splice($this->elements, $index, 0, array($e))) {
			return true;
		}
		return false;
	}

	/**
	 * Adds <code>String</code> with elements to the segment. The elements are
	 * added at the end of the elements in the current segment. e.g.
	 * <code>addElements("ISA*ISA01*ISA02");</code>
	 * 
	 * @param s
	 * @return boolean
	 */
	public function addElements1String($s) { // $s is a string
		$elements = explode($this->context->getElementSeparator(), $s);
		if($this->addElements($elements)) {
			return true;
		}
		return false;
	}

	/**
	 * Adds <code>String</code> elements to the segment. The elements are added
	 * at the end of the elements in the current segment. e.g.
	 * <code> addElements("ISA", "ISA01", "ISA02");</code>
	 * @param es
	 * @return boolean
	 */
	public function addElementsN(...$es) { // $es is optional number of string arguments
		foreach ($es as $s) {
			$this->elements[] = $s;
		}
		return true;
	}

	public function addElements1Array($es) { // $es is an array of elements
		foreach ($es as $s) {
			$this->elements[] = $s;
		}
		return true;
	}

	/**
	 * Adds strings as a composite element to the end of the segment.
	 * 
	 * @param ces
	 *            sub-elements of a composite element
	 * @return boolean
	 */
	public function addCompositeElementN(...$ces) { // $ces is optional number of string arguments
		$dump = "";
		foreach ($ces as $s) {
			$dump .= $s;
			$dump .= $this->context->getCompositeElementSeparator();
		}
		if($this->elements[] = substr($dump, 0, sizeof($dump)-2)) {
			return true;
		}
		return false;
	}

	public function addCompositeElement1Array($ces) { // $ces is array of arguments
		$dump = "";
		foreach ($ces as $s) {
			$dump .= $s;
			$dump .= $this->context->getCompositeElementSeparator();
		}
		if($this->elements[] = substr($dump, 0, sizeof($dump)-2)) {
			return true;
		}
		return false;
	}

	/**
	 * Inserts strings as a composite element to segment at specified position
	 * 
	 * @param ces
	 *            sub-elements of a composite element
	 */
	public function addCompositeElement2N($index, ...$ces) { // $ces is optional number of string arguments
		$dump = "";
		foreach ($ces as $s) {
			$dump .= $s;
			$dump .= $this->context->getCompositeElementSeparator();
		}
		array_splice($this->elements, $index, 0, array(substr($dump, 0, sizeof($dump)-2)));
	}

	public function addCompositeElement2Array($index, $ces) { // $ces is array of arguments
		$dump = "";
		foreach ($ces as $s) {
			$dump .= $s;
			$dump .= $this->context->getCompositeElementSeparator();
		}
		array_splice($this->elements, $index, 0, array(substr($dump, 0, sizeof($dump)-2)));
	}
	/**
	 * Returns the context object
	 * 
	 * @return $object
	 */
	public function getContext() {
		return $this->context;
	}

	/**
	 * Returns the <code>String<code> element at the specified position.
	 * 
	 * @param index
	 *            position
	 * @return the element at the specified position.
	 */
	public function getElement($index) {
		return $this->elements[$index];
	}

	/**
	 * 
	 * @return List of elements
	 */
	public function getElements() {
		return $this->elements;
	}
	
	/**
	 * Returns and <code>Iterator</code> to the elements in the segment.
	 * 
	 * @return Iterator<String>
	 */
	// public function iterator() {
	// 	return elements.iterator();
	// }

	/**
	 * Removes the element at the specified position in this list.
	 * 
	 * @param index
	 * @return
	 */
	public function removeElement($index) {
		$returned = $this->elements[$index];
		unset($this->elements[$index]);
		$this->elements = array_values($this->elements);
		return $returned;
	}

	/**
	 * Removes empty and null elements at the end of segment 
	 */
	private function removeTrailingEmptyElements() {
		for ($i = sizeof($this->elements)-1; $i >= 0 ; $i--) { 
			if($this->elements[$i] == null || strlen($this->elements[$i]) == 0) {
				unset($this->elements[$i]);
				$this->elements = array_values($this->elements);
			} else {
				break;
			}
		}
	}
	
	/**
	 * Sets the context of the segment
	 * 
	 * @param context
	 *            context object
	 */
	public function setContext($context) {
		$this->context = $context;
	}

	/**
	 * Replaces element at the specified position with the specified
	 * <code>String</code>
	 * 
	 * @param index
	 *            position of the element to be replaced
	 * @param s
	 *            new element with which to replace
	 */
	public function setElement($index, $s) {
		$this->elements[$index] = $s;
	}

	/**
	 * Replaces composite element at the specified position in segment.
	 * 
	 * @param ces
	 *            sub-elements of a composite element
	 */
	public function setCompositeElement2N($index, ...$ces) { // $ces is optional number of arugments
		$dump = "";
		foreach ($ces as $s) {
			$dump .= $s;
			$dump .= $this->context->getCompositeElementSeparator();
		}
		$this->elements[$index] = substr($dump, 0, sizeof($dump)-2);
	}

	public function setCompositeElement2Array($index, $ces) { // $ces is array of arguments
		$dump = "";
		foreach ($ces as $s) {
			$dump .= $s;
			$dump .= $this->context->getCompositeElementSeparator();
		}
		$this->elements[$index] = substr($dump, 0, sizeof($dump)-2);
	}

	/**
	 * Returns number of elements in the segment.
	 * 
	 * @return size
	 */
	public function size() {
		return sizeof($this->elements);
	}

	/**
	 * Returns the X12 representation of the segment.
	 */
	public function toString0() {
		$dump = "";
		foreach ($this->elements as $s) {
			$dump .= $s;
			$dump .= $this->context->getElementSeparator();
		}
		if(strlen($dump) == 0) {
			return EMPTY_STRING;
		}
		return substr($dump, 0, sizeof($dump)-2);
	}

	/**
	 * Returns the X12 representation of the segment.
	 * 
	 * @param bRemoveTrailingEmptyElements
	 * @return <code>String</code>
	 */
	public function toString1($bRemoveTrailingEmptyElements) {
		if ($bRemoveTrailingEmptyElements)
			$this->removeTrailingEmptyElements();
		return $this->toString();
	}
	
	/**
	 * Returns the XML representation of the segment.
	 * 
	 * @return <code>String</code>
	 */
	public function toXML0() {
		$dump = "";
		$dump .= "<" . $this->elements[0] . ">";
		for ($i = 1; $i < sizeof($this->elements); $i++) {
			$dump .= "<" . $this->elements[0] . sprintf("%'.02d", $i) . "><![CDATA[";
			$dump .= $this->elements[$i];
			$dump .= "]]></" . $this->elements[0] . sprintf("%'.02d", $i) . ">";

		}
		$dump .= "</" . $this->elements[0] . ">";
		return $dump;
	}

	/**
	 * Returns the XML representation of the segment.
	 * 
	 * @param bRemoveTrailingEmptyElements
	 * @return <code>String</code>
	 */
	public function toXML1($bRemoveTrailingEmptyElements) {
		if ($bRemoveTrailingEmptyElements)
			$this->removeTrailingEmptyElements();
		return $this->toXML();
	}

}
