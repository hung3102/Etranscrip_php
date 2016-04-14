<?php
namespace backend\components\x12\x12test;
use backend\components\x12\Segment;
use backend\components\x12\Context;

class SegmentTest {

	public function testSegmentEmpty() {
		$s = new Segment(new Context('~', '*', ':'));
		return $s !== null ? "right" : "wrong";
	}

	public function testAddElementString() {
		$s = new Segment(new Context('~', '*', ':'));
		return $s->addElement("ISA") === true ? "right" : "wrong";
	}

	public function testAddElements() {
		$s = new Segment(new Context('~', '*', ':'));
		return $s->addElements("ISA", "ISA01", "ISA02") === true ? "right" : "wrong";
	}

	public function testAddCompositeElementStringArray() {
		$s = new Segment(new Context('~', '*', ':'));
		return $s->addCompositeElement("AB", "CD", "EF") === true ? "right" : "wrong";
	}

	public function testAddElementIntString() {
		$s = new Segment(new Context('~', '*', ':'));
		$s->addElements("ISA", "ISA01", "ISA02");
		return $s->addCompositeElement("ISA03_1", "ISA03_2", "ISA03_3") === true ? "right" : "wrong";
	}

	public function testAddCompositeElementIntStringArray() {
		$s = new Segment(new Context('~', '*', ':'));
		$s->addElements("ISA", "ISA01", "ISA02", "ISA04");
		$s->addCompositeElement(3, "ISA03_1", "ISA03_2", "ISA03_3");
		return $s->getElement(3) === "ISA03_1:ISA03_2:ISA03_3" ? "right" : "wrong";
	}

	public function testGetContext() {
		$s = new Segment(new Context('~', '*', ':'));
		return $s->getContext()->toString() === "[~,*,:]" ? "right" : "wrong";
	}

	public function testGetElement() {
		$s = new Segment(new Context('~', '*', ':'));
		$s->addElements("ISA", "ISA01", "ISA02", "ISA03");
		return $s->getElement(2) === "ISA02" ? "right" : "wrong";
	}

	// public function testIterator() {
	// 	$s = new Segment(new Context('~', '*', ':'));
	// 	$s->addElements("ISA", "ISA01", "ISA02", "ISA03");
	// 	assertNotNull($s->iterator());
	// }

	public function testRemoveElement() {
		$s = new Segment(new Context('~', '*', ':'));
		$s->addElements("ISA", "ISA01", "ISA02", "ISA03");
		$s->removeElement(2);
		return $s->toString() === "ISA*ISA01*ISA03" ? "right" : "wrong";
	}

	public function testRemoveElementTwo() {
		$s = new Segment(new Context('~', '*', ':'));
		$s->addElements("ISA", "ISA01", "ISA02", "ISA03");
		$s->removeElement(3);
		return $s->toString() === "ISA*ISA01*ISA02" ? "right" : "wrong";
	}
	
	public function testSetContext() {
		$s = new Segment(new Context('~', '*', ':'));
		$s->setContext(new Context('s', 'e', 'c'));
		return $s->getContext()->toString() === "[s,e,c]" ? "right" : "wrong";
	}

	public function testSetElement() {
		$s = new Segment(new Context('~', '*', ':'));
		$s->addElements("ISA", "ISA01", "ISA02", "ISA04", "ISA04");
		$s->setElement(3, "ISA03");
		return $s->getElement(3) === "ISA03" ? "right" : "wrong";
	}

	public function testSetCompositeElement() {
		$s = new Segment(new Context('~', '*', ':'));
		$s->addElements("ISA", "ISA01", "ISA02", "ISA04", "ISA04");
		$s->setCompositeElement(3, "ISA03_1", "ISA03_2", "ISA03_3");
		return $s->getElement(3) === "ISA03_1:ISA03_2:ISA03_3" ? "right" : "wrong";
	}

	public function testSize() {
		$s = new Segment(new Context('~', '*', ':'));
		$s->addElements("ISA", "ISA01", "ISA02", "ISA03", "ISA04");
		return $s->size() === 5 ? "right" : "wrong";
	}

	public function testToString() {
		$s = new Segment(new Context('~', '*', ':'));
		$s->addElements("ISA", "ISA01", "ISA02", "ISA03", "ISA04");
		$s->setCompositeElement(3, "ISA03_1", "ISA03_2", "ISA03_3");
		return $s->toString() === "ISA*ISA01*ISA02*ISA03_1:ISA03_2:ISA03_3*ISA04" ? "right" : "wrong";
	}

	public function testToStringRemoveTrailingEmptyElements() {
		$s = new Segment(new Context('~', '*', ':'));
		$s->addElements("ISA", "ISA01", "ISA02", "ISA03", "ISA04", "", "", "");
		return $s->toString(true) === "ISA*ISA01*ISA02*ISA03*ISA04" ? "right" : "wrong";
	}

	public function testToStringRemoveTrailingNullElements() {
		$s = new Segment(new Context('~', '*', ':'));
		$s->addElements("ISA", "ISA01", "ISA02", "ISA03", "ISA04", null, null, null);
		return $s->toString(true) === "ISA*ISA01*ISA02*ISA03*ISA04" ? "right" : "wrong";
	}

	public function testToXML() {
		$s = new Segment(new Context('~', '*', ':'));
		$s->addElements("ISA", "01", "02", "03", "04");
		$s->setCompositeElement(3, "03_1", "03_2", "03_3");
		return $s->toXML() === "<ISA><ISA01><![CDATA[01]]></ISA01><ISA02><![CDATA[02]]></ISA02><ISA03><![CDATA[03_1:03_2:03_3]]></ISA03><ISA04><![CDATA[04]]></ISA04></ISA>" ? "right" : "wrong";
	}

}
