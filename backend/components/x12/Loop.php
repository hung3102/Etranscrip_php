<?php
namespace backend\components\x12;
use backend\components\x12\Segment;

/**
 * The Loop class is the representation of an Loop in a ANSI X12
 * transaction. The building block of an X12 transaction is an element. Some
 * elements may be made of sub elements. Elements combine to form segments.
 * Segments are grouped as loops. And a set of loops form an X12 transaction.
 */

class Loop {
	private static $serialVersionUID; // long type
	private $context; // context type
	private $name; // string type
	private $segments = []; // segment array type
	private $loops = []; // loop array type
	private $parent; // loop type
	private $depth; // used to debug

	/**
	 * The constructor takes a context object.
	 * 
	 * @param c
	 *            a Context object
	 */
	public function __construct() {
		$a = func_get_args();
		$i = func_num_args();
		if($i == 2 && method_exists($this, $f = 'init')) {
			call_user_func_array(array($this, $f), $a);
		}
	}

	public function init($c, $name) {
		$this->context = $c;
		$this->name = $name;
		$this->parent = null;
	}

	public function __call($method, $arguments) {
		$arg_nums = count($arguments);
		switch ($method) {
			case 'addChild':
				if( $arg_nums == 1) {
					return call_user_func_array(array($this, 'addChild1'), $arguments);
				} else if($arg_nums == 2) {
					if($arguments[1] instanceof Loop) {
						return call_user_func_array(array($this, 'addChild2Loop'), $arguments);
					} else if(is_string($arguments[1])) {
						return call_user_func_array(array($this, 'addChild2Name'), $arguments);
					}
				}	
				break;
		
			case 'addSegment':
				if( $arg_nums == 0) {
					return call_user_func_array(array($this, 'addSegment0'), $arguments);
				} else if($arg_nums == 1) {
					if(is_string($arguments[0])) {
						return call_user_func_array(array($this, 'addSegment1String'), $arguments);
					} else if($arguments[0] instanceof Segment) {
						return call_user_func_array(array($this, 'addSegment1Segment'), $arguments);
					} else if(is_int($arguments[0])) {
						return call_user_func_array(array($this, 'addSegment1Index'), $arguments);
					}
				} else if($arg_nums == 2) {
					if(is_string($arguments[1])) {
						return call_user_func_array(array($this, 'addSegment2String'), $arguments);
					} else if($arguments[1] instanceof Segment) {
						return call_user_func_array(array($this, 'addSegment2Segment'), $arguments);
					}
				}
				break;

			case 'setChild':
				if(is_string($arguments[1])) {
					return call_user_func_array(array($this, 'setChildName'), $arguments);
				} else if($arguments[1] instanceof Loop) {
					return call_user_func_array(array($this, 'setChildLoop'), $arguments);
				}
				break;

			case 'setSegment':
				if($arg_nums == 1) {
					return call_user_func_array(array($this, 'setSegmentIndex'), $arguments);
				} else if($arg_nums == 2) {
					if(is_string($arguments[1])) {
						return call_user_func_array(array($this, 'setSegmentString'), $arguments);
					} else if($arguments[1] instanceof Segment) {
						return call_user_func_array(array($this, 'setSegmentSegment'), $arguments);
					}
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
	 * Creates an empty instance of <code>Loop</code> and adds the loop as a
	 * child to the current Loop. The returned instance can be used to add
	 * segments to the child $loop->
	 * 
	 * @param name
	 *            name of the loop
	 * @return a new child Loop object
	 */
	public function addChild1($name) {
		$l = new Loop($this->context, $name);
		$l->setParent($this);
		$l->depth = $this->depth + 1; // debug
		$this->loops[] = $l;
		return $l;
	}

	/**
	 * Inserts <code>Loop</code> as a child loop at the specified position.
	 * 
	 * @param index
	 *            position at which to add the $loop->
	 */
	public function addChild2Loop($index, $loop) {
		$loop->setParent($this);
		$loop->depth = $this->depth + 1; // debug
		array_splice($this->loops, $index, 0, array($loop));
	}

	/**
	 * Creates an empty instance of <code>Loop</code> and inserts the loop as a
	 * child loop at the specified position. The returned instance can be used
	 * to add segments to the child $loop->
	 * 
	 * @param index
	 *            position at which to add the loop
	 * @param name
	 *            name of the loop
	 * @return a new child Loop object
	 */
	public function addChild2Name($index, $name) {
		$l = new Loop($this->context, $name);
		$l->setParent($this);
		$l->depth = $this->depth + 1; // debug
		array_splice($this->loops, $index, 0, array($l));
		return $l;
	}

	/**
	 * Creates an empty instance of <code>Segment</code> and adds the segment to
	 * current Loop. The returned instance can be used to add elements to the
	 * segment.
	 * 
	 * @return a new Segment object
	 */
	public function addSegment0() {
		$s = new Segment($this->context);
		$this->segments[] = $s;
		return $s;
	}

	/**
	 * Takes a <code>String</code> representation of segment, creates a
	 * <code>Segment</code> object and adds the segment to the current Loop.
	 * 
	 * @param segment
	 *            <code>String</code> representation of the Segment.
	 * @return a new Segment object
	 */
	public function addSegment1String($segment) {
		$s = new Segment($this->context);
		$elements = explode($this->context->getElementSeparator(), $segment);
		$s->addElements($elements);
		$this->segments[] = $s;
		return $s;
	}

	/**
	 * Adds <code>Segment</code> at the end of the current Loop
	 * 
	 * @param segment
	 *            <code>Segment</code>
	 */
	public function addSegment1Segment($segment) {
		$this->segments[] = $segment;
	}

	/**
	 * Creates an empty instance of <code>Segment</code> and adds the segment at
	 * the specified position in the current Loop. The returned instance can be
	 * used to add elements to the segment.
	 * 
	 * @param index
	 *            position at which to add the segment.
	 * @return a new Segment object
	 */
	public function addSegment1Index($index) {
		$s = new Segment($this->context);
		array_splice($this->segments, $index, 0, array($s));
		return $s;
	}

	/**
	 * Takes a <code>String</code> representation of segment, creates a
	 * <code>Segment</code> object and adds the segment at the specified
	 * position in the current Loop.
	 * 
	 * @param index
	 *            position to add the segment.
	 * @param segment
	 *            <code>String</code> representation of the segment.
	 * @return a new Segment object
	 */
	public function addSegment2String($index, $segment) { //segment is string type
		$s = new Segment($this->context);
		$elements = explode($this->context->getElementSeparator(), $segment);
		$s->addElements($elements);
		array_splice($this->segments, $index, 0, array($s));
		return $s;
	}

	/**
	 * Adds <code>Segment</code> at the specified position in current Loop.
	 * 
	 * @param index
	 *            position to add the segment.
	 * @param segment
	 *            <code>String</code> representation of the segment.
	 */
	public function addSegment2Segment($index, $segment) { // segment is segment type
		array_splice($this->segments, $index, 0, array($segment));
	}

	/**
	 * Checks if the Loop contains the specified child Loop. It will check the
	 * complete child hierarchy.
	 * 
	 * @param name
	 *            name of a child loop
	 * @return boolean
	 */
	public function hasLoop($name) {
		foreach ($this->childList() as $l) {
			if($name === $l->getName()) {
				return true;
			}
			if($l->hasLoop($name)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get the loop in the X12 transaction It will check the complete child
	 * hierarchy.
	 * 
	 * @param name
	 *            name of a loop
	 * @return List<Loop>
	 */
	public function findLoop($name) {
		$foundLoops = [];
		foreach ($this->childList() as $l) {
			if ($name === $l->getName()) {
				$foundLoops[] = $l;
			}
			$moreLoops = $l->findLoop($name);
			if (sizeof($moreLoops) > 0) {
				array_merge($foundLoops, $moreLoops);
			}
		}
		
		return $foundLoops;
	}

	/**
	 * Get the segment in the X12 transaction It will check the current loop and
	 * the complete child hierarchy.
	 * 
	 * @param name
	 *            name of a segment
	 * @return List<Segment>
	 */
	public function findSegment($name) {
		$foundSegments = [];
		foreach ($this->segments as $s) {
			if ($name === $s->getElement(0)) {
				$foundSegments[] = $s;
			}
		}
		foreach ($this->childList() as $l) {
			$moreSegments = $l->findSegment($name);
			if (sizeof($moreSegments) > 0) {
				$foundSegments = array_merge($foundSegments, $moreSegments);
			}
		}
		
		return $foundSegments;
	}

	/**
	 * Returns the context of the X12 transaction.
	 * 
	 * @return Context object
	 */
	public function getContext() {
		return $this->context;
	}

	/**
	 * Returns the <code>Loop<code> at the specified position.
	 * 
	 * @param index
	 * @return Loop at the specified index
	 */
	public function getLoop($index) {
		return $this->loops[$index];
	}

	/**
	 * Returns the loops
	 * 
	 * @return List<Loop>
	 */
	public function getLoops() {
		return $this->loops;
	}

	/**
	 * 
	 * @return Parent Loop
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * Returns the <code>Segment<code> at the specified position.
	 * 
	 * @param index
	 * @return Segment at the specified index
	 */
	public function getSegment($index) {
		return $this->segments[$index];
	}

	/**
	 * Returns the segments in the current $loop->
	 * 
	 * @return List<Segment>
	 */
	public function getSegments() {
		return $this->segments;
	}
	
	/**
	 * Returns the name of the current Loop.
	 * 
	 * @return String
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns and <code>Iterator</code> to the segments in the $loop->
	 * 
	 * @return Iterator<Segment>
	 */
	
	// public function iterator() {
	// 	return $this->segments->getIterator();
	// }

	/**
	 * Removes the loop at the specified position in this list.
	 * 
	 * @param index
	 * @return
	 */
	public function removeLoop($index) {
		$returned = $this->loops[$index];
		unset($this->loops[$index]);
		$this->loops = array_values($this->loops);
		return $returned;
	}
		
	/**
	 * Removes the segment at the specified position in this list.
	 * 
	 * @param index
	 * @return
	 */
	public function removeSegment($index) {
		$returned = $this->segments[$index];
		unset($this->segments[$index]);
		$this->segments = array_values($this->segments);
		return $returned;
	}
		
	/**
	 * Returns <code>List<Loop></code> of child Loops
	 * 
	 * @return List<Loop>
	 */
	public function childList() {
		return $this->loops;
	}

	/**
	 * Returns number of segments in Loop and child loops
	 * 
	 * @return size
	 */
	public function size() {
		$size = 0;
		$size = sizeof($this->segments);
		foreach ($this->childList() as $l) {
			$size += $l->size();
		}
		
		return $size;
	}

	/**
	 * Sets the context of the current transaction.
	 * 
	 * @param context
	 */
	public function setContext($context) {
		$this->context = $context;
	}

	/**
	 * Creates a new <code>Loop</code> and replaces the child loop at the
	 * specified position. The returned instance can be used to add segments to
	 * the child $loop->
	 * 
	 * @param name
	 *            name of the loop 
	 * @param index
	 *            position at which to add the $loop->
	 * @return a new child Loop object
	 */
	public function setChildName($index, $name) {
		$l = new Loop($this->context, $name);
		$l->setParent($this);
		$l->depth = $this->depth + 1; // debug
		$this->loops[$index] = $l;
		return $l;
	}

	/**
	 * Replaces child <code>Loop</code> at the specified position.
	 * 
	 * @param index
	 *            position at which to add the $loop->
	 * @param loop
	 *            Loop to add            
	 */
	public function setChildLoop($index, $loop) {
		$loop->setParent($this);
		$loop->depth = $this->depth + 1; // debug
		$this->loops[$index] = $loop;
	}

	/**
	 * 
	 * @param parent
	 */
	public function setParent($parent) {
		$this->parent = $parent;
	}
		
	/**
	 * Creates an empty instance of <code>Segment</code> and replaces the
	 * segment at specified position in the X12 transaction. The returned
	 * instance can be used to add elements to the segment.
	 * 
	 * @param index
	 *            position at which to add the segment.
	 * @return a new Segment object
	 */
	public function setSegmentIndex($index) {
		$s = new Segment($this->context);
		$this->segments[$index] = $s;
		return $s;
	}

	/**
	 * Takes a <code>String</code> representation of segment, creates a
	 * <code>Segment</code> object and replaces the segment at the specified
	 * position in the X12 transaction.
	 * 
	 * @param index
	 *            position of the segment to be replaced.
	 * @param segment
	 *            <code>String</code> representation of the Segment.
	 * @return a new Segment object
	 */
	public function setSegmentString($index, $segment) {
		$s = new Segment($this->context);
		$elements = explode($this->context->getElementSeparator(), $segment);
		
		$s->addElements($elements);
		$this->segments[$index] = $s;
		return $s;
	}

	/**
	 * Replaces
	 * <code>Segment<code> at the specified position in X12 transaction.
	 * 
	 * @param index
	 *            position of the segment to be replaced.
	 * @param segment
	 *            <code>Segment</code>
	 */
	public function setSegmentSegment($index, $segment) {
		$this->segments[$index] = $segment;
	}

	/**
	 * Sets the name of the current Loop
	 * 
	 * @param name
	 *            <code>String</code>
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Returns the Loop in X12 <code>String</code> format. This method is used
	 * to convert the X12 object into a X12 transaction.
	 * 
	 * @return String
	 */
	public function toString0() {
		return $this->toString(false);
	}
 
	/**
	 * Returns the Loop in X12 <code>String</code> format. This method is used
	 * to convert the X12 object into a X12 transaction.
	 * 
	 * @param bRemoveTrailingEmptyElements
	 * @return
	 */
	public function toString1($bRemoveTrailingEmptyElements) {
		$dump = "";
		foreach ($this->segments as $s) {
			$dump .= $s->toString($bRemoveTrailingEmptyElements);
			$dump .= $this->context->getSegmentSeparator();
		}
		foreach ($this->childList() as $l) {
			$dump .= $l->toString($bRemoveTrailingEmptyElements);
		}
		
		return $dump;
	}

	/**
	 * Returns the Loop in XML <code>String</code> format. This method is used
	 * to convert the X12 object into a XML string.
	 * 
	 * @return XML String
	 */
	public function toXML0() {
		return $this->toXML(false);
	}

	/**
	 * Returns the Loop in XML <code>String</code> format. This method is used
	 * to convert the X12 object into a XML string.
	 * 
	 * @param bRemoveTrailingEmptyElements
	 * @return
	 */
	public function toXML1($bRemoveTrailingEmptyElements) {
		$dump = "";
		$dump .= "<LOOP NAME=\"" . $this->name . "\">";  // carefully here
		foreach ($this->segments as $s) {
			$dump .= $s->toXML($bRemoveTrailingEmptyElements);
		}
		foreach ($this->childList() as $l) {
			$dump .= $l->toXML($bRemoveTrailingEmptyElements);
		}
		$dump .= "</LOOP>";

		return $dump;
	}

	/**
	 * Generally not used. Mostly for debugging. 
	 * @return depth
	 */
	public function getDepth() {
		return $this->depth;
	}
}
