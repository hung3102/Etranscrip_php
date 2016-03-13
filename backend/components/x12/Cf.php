<?php
/**
 * The <code>Cf</code> class represents a configuration element. Each Cf
 * instance represents items required to identify a Loop in a X12 transaction.
 * Some Loops can be identified by only the segment id. Others require segment
 * id and additional qualifiers to be able to identify the Loop.
 * 
 * <code>Cf</code> needs to be used in conjunction with X12Parser, to be able to
 * parse a X12 transaction into a loop hierarchy.
 * 
 * A X12 Cf can be loaded using many ways: custom code O/X mapping DI or any
 * other way you may find appropriate
 * 
 * A Sample 835 hierarchy is shown below. Each row shows a Cf element, in the
 * format
 * 
 * <pre>
 *    (A) - (B) - (C) - (D)
 *    (A) - Loop Name
 *    (B) - Segment id, that identifies the loop
 *    (C) - Segment qualifiers, that are needed to identify the loop. If there are multiple
 *          qualifiers they need to be separated by COMMA.
 *    (D) - Position in the segment where the qualifiers are present
 * </pre>
 * 
 * e.g. In X12 835, Loops 1000A and 1000B have the same segment id (N1), to
 * differentiate them we need additional attributes. The N102 (index 1) element
 * has PR for 1000A loop and PE for 1000B loop.
 * 
 * <pre>
 * +--X12
 * |  +--ISA - ISA
 * |  |  +--GS - GS
 * |  |  |  +--ST - ST - 835, - 1
 * |  |  |  |  +--1000A - N1 - PR, - 1
 * |  |  |  |  +--1000B - N1 - PE, - 1
 * |  |  |  |  +--2000 - LX
 * |  |  |  |  |  +--2100 - CLP
 * |  |  |  |  |  |  +--2110 - SVC
 * |  |  |  +--SE - SE
 * |  |  +--GE - GE
 * |  +--IEA - IEA
 * </pre>
 * 
 * To parse a X12 835 in the above hierarchy, you need to create a Cf object
 * that represent the hierarchy. Here is the sample code to achieve this.
 * 
 * <pre>
 * Cf cfX12 = new Cf("X12"); // root node
 * Cf cfISA = cfX12.addChild("ISA", "ISA"); // add as child of X12 
 * Cf cfGS = cfISA.addChild("GS", "GS"); // add as child of ISA
 * Cf cfST = cfGS.addChild("ST", "ST", "835", 1); // add as child of GS
 * cfST.addChild("1000A", "N1", "PR", 1); // add as child of ST
 * cfST.addChild("1000B", "N1", "PE", 1); // add as child of ST
 * Cf cf2000 = cfST.addChild("2000", "LX")
 * Cf cf2100 = cf2000.addChild("2100", "CLP");
 * cf2100.addChild("2110", "SVC");
 * cfISA.addChild("GE", "GE");
 * cfX12.addChild("IEA", "IEA");
 * </pre>
 * 
 * Alternate hierarchy for the same transaction. On most occasions a simple
 * hierarchy like below would work. Only when there is more that one loop that
 * is identified by the same segment id and additional qualifiers, you need to
 * put them under the appropriate parent Cf.
 * 
 * <pre>
 *  +--X12
 *  |  +--ISA - ISA
 *  |  +--GS - GS
 *  |  +--ST - ST - 835, - 1
 *  |  +--1000A - N1 - PR, - 1
 *  |  +--1000B - N1 - PE, - 1
 *  |  +--2000 - LX
 *  |  +--2100 - CLP
 *  |  +--2110 - SVC
 *  |  +--SE - SE  
 *  |  +--GE - GE  
 *  |  +--IEA - IEA
 * </pre>
 *
 */

namespace backend\components\x12;

public class Cf {
	private $name, $segment; // String type
	private $segmentQuals = []; // array string type
	private $segmentQualPos; // int type
	private $depth;

	private $children = []; // elements are Cf type
	private $parent; // Cf type

	public function __construct() {
		$a = func_get_args();
		$i = func_num_args();
		if(method_exists($this, $f = 'init'.$i)) {
			call_user_func_array(array($this, $f), $a);
		}
	}

	private function init1($name) {
		$this->name = $name;
	}

	private function init2($name, $segment) {
		$this->name = $name;
		$this->segment = $segment;
	}

	private function init4($name, $segment, $segmentQual, $segmentQualPos) {
		$this->name = $name;
		$this->segment = $segment;
		array_merge($this->segmentQuals, explode(",", $segmentQual));
		$this->segmentQualPos = $segmentQualPos;
	}

	public function __call($method, $arguments) {
		if($method == 'addChild') {
			$arg_nums = count($arguments);
			if( $arg_nums == 1) {
				return call_user_func_array(array($this, 'addChild1'), $arguments);
			} else if($arg_nums == 2) {
				return call_user_func_array(array($this, 'addChild2'), $arguments);
			} else if($arg_nums == 4) {
				return call_user_func_array(array($this, 'addChild3'), $arguments);
			}
		}
	}

	private function addChild1($cf) {
		$cf->depth = $this->depth + 1;
		$this->children[] = $cf;
		$cf->setParent($this);
	}

	private function addChild2($name, $segment) {
		$cf = new Cf($name, $segment);
		$cf->depth = $this->depth + 1;
		$this->children[] = $cf;
		$cf->setParent($this);
		return $cf;
	}

	private function addChild3($name, $segment, $segmentQual, $segmentQualPos) {
		$cf = new Cf($name, $segment, $segmentQual, $segmentQualPos);
		$cf->depth = $this->depth + 1;
		$this->children[] = $cf;
		$cf->setParent($this);
		return $cf;
	}

	public function childList() {
		return $this->children;
	}

	public function hasChildren() {
		return sizeof($this->children) > 0 ? true : false;
	}

	public function hasParent() {
		return $this->parent == null ? false : true;
	}

	public function getParent() {
		return $this->parent;
	}

	public function getName() {
		return $this->name;
	}

	public function getSegment() {
		return $this->segment;
	}

	public function getSegmentQuals() {
		return $this->segmentQuals;
	}

	public function getSegmentQualPos() {
		return $this->segmentQualPos;
	}

	public function setParent($cf) {
		$this->parent = $cf;
	}

	public function setChildren($cfList) {
		array_merge($this->children, $cfList);
		foreach ($cfList as $cf) {
			$cf->depth = $this->depth + 1;
			$cf->setParent($this);
		}
	}

	public function setName($name) {
		$this->name = $name;
	}	

	public function setSegment($segment) {
		$this->segment = $segment;
	}

	public function setSegmentQuals($segmentQuals) {
		array_merge($this->segmentQuals, $segmentQuals);
	}

	public function setSegmentQualPos($segmentQualPos) {
		$this->segmentQualPos = $segmentQualPos;
	}

	public function toString() {
		$dump = "";
		for ($i=0; $i < $this->depth; $i++) { 
			$dump .= "|  ";
		}
		$dump .= "+--";
		$dump .= $this->name;
		if($this->segment != null)
			$dump .= " - " . $this->segment;
		if($this->segmentQuals != null) {
			$dump .= " - ";
			foreach ($this->segmentQuals as $s) {
				$dump .= $s . ",";
			}
		}
		if($this->segmentQualPos != null) 
			$dump .= " - " . $this->segmentQualPos;
		$dump .= "\n";
		foreach ($this->children as $cf) {
			$dump .= $cf->toString();
		}

		return $dump;
	}

}