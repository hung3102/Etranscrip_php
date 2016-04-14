<?php
namespace backend\components\x12\x12test;
use backend\components\x12\Loop;
use backend\components\x12\Context;
use backend\components\x12\Segment;

class LoopTest {

	public function testLoop() {
		$loop = new Loop(new Context('~', '*', ':'), "ISA");
		return $loop != null ? "right" : "wrong";
	}

	public function testAddChildString() {
		$loop = new Loop(new Context('~', '*', ':'), "ISA");
		$child = $loop->addChild("GS");
		return $child != null ? "right" : "wrong";
	}

	public function testAddChildIntLoop() {
		$loop = new Loop(new Context('~', '*', ':'), "ISA");
		$gs = new Loop(new Context('~', '*', ':'), "GS");
		$st = new Loop(new Context('~', '*', ':'), "ST");
		$loop->addChild(0, $gs);
		$loop->addChild(1, $st);
		return $loop->getLoop(1)->getName() === "ST" ? "right" : "wrong";
	}

	public function testAddSegment() {
		$loop = new Loop(new Context('~', '*', ':'), "ST");
		$s = $loop->addSegment();
		return $s != null ? "right" : "wrong";
	}

	public function testAddSegmentString() {
		$loop = new Loop(new Context('~', '*', ':'), "ST");
		$loop->addSegment("ST*835*000000001");
		return $loop->getSegment(0)->getElement(0) === "ST" ? "right" : "wrong";
	}

	public function testAddSegmentSegment() {
		$loop = new Loop(new Context('~', '*', ':'), "ST");
		$segment = new Segment(new Context('~', '*', ':'));
		$segment->addElements("ST*835*000000001");
		$loop->addSegment($segment);
		return $loop->getSegment(0)->getElement(0) === "ST" ? "right" : "wrong";
	}

	public function testAddSegmentInt() {
		$loop = new Loop(new Context('~', '*', ':'), "ST");
		$loop->addSegment("BPR*DATA*NOT*VALID*RANDOM*TEXT");
		$loop->addSegment("TRN*1*0000000000*1999999999");
		$loop->addSegment("DTM*111*20090915");
		$segment = new Segment(new Context('~', '*', ':'));
		$segment->addElements("ST*835*000000001");
		$loop->addSegment(0, $segment);
		return $loop->getSegment(0)->getElement(0) === "ST" ? "right" : "wrong";
	}

	public function testAddSegmentIntString() {
		$loop = new Loop(new Context('~', '*', ':'), "ST");
		$loop->addSegment("BPR*DATA*NOT*VALID*RANDOM*TEXT");
		$loop->addSegment("TRN*1*0000000000*1999999999");
		$loop->addSegment("DTM*111*20090915");
		$loop->addSegment(0, "ST*835*000000001");
		return "ST" === $loop->getSegment(0)->getElement(0) ? "right" : "wrong";
	}

	public function testAddSegmentIntSegment() {
		$loop = new Loop(new Context('~', '*', ':'), "ST");
		$loop->addSegment("ST*835*000000001");
		$loop->addSegment("BPR*DATA*NOT*VALID*RANDOM*TEXT");
		$loop->addSegment("DTM*111*20090915");
		$segment = new Segment(new Context('~', '*', ':'));
		$segment->addElements("ST*835*000000001");
		$loop->addSegment(2, "TRN*1*0000000000*1999999999");
		return $loop->getSegment(2)->getElement(0) === "TRN" ? "right" : "wrong";
	}

	public function testAddChildIntString() {
		$loop = new Loop(new Context('~', '*', ':'), "ISA");
		$loop->addChild("GS");
		$loop->addChild(1, "ST");
		return $loop->getLoop(1)->getName() === "ST" ? "right" : "wrong";
	}

	public function testHasLoop() {
		$loop = new Loop(new Context('~', '*', ':'), "ISA");
		$loop->addChild("GS");
		$loop->addChild("ST");
		return $loop->hasLoop("ST") === true ? "right" : "wrong";
	}

	public function testFindLoop() {
		$loop = new Loop(new Context('~', '*', ':'), "ISA");
		$loop->addChild("GS");
		$loop->addChild("ST");
		$loop->addChild("1000A");
		$loop->addChild("1000B");
		$loop->addChild("2000");
		$loop->addChild("2100");
		$loop->addChild("2110");
		$loop->addChild("GE");
		$loop->addChild("IEA");
		$loops = $loop->findLoop("2000");
		return sizeof($loops) === 1 ? "right" : "wrong";
		assertEquals(new Integer(1), new Integer(loops.size()));
	}

	public function testFindSegment() {
		$loop = new Loop(new Context('~', '*', ':'), "ISA");
		$loop->addChild("GS");
		$loop->addChild("ST");
		$loop->addChild("1000A");
		$loop->addChild("1000B");
		$child1 = $loop->addChild("2000");
		$child1->addSegment("LX*1");
		$child2 = $loop->addChild("2000");
		$child2->addSegment("LX*2");
		$loop->addChild("2100");
		$loop->addChild("2110");
		$loop->addChild("GE");
		$loop->addChild("IEA");
		$segments = $loop->findSegment("LX");
		return sizeof($segments) === 2 ? "right" : "wrong";
	}

	public function testGetContext() {
		$loop = new Loop(new Context('~', '*', ':'), "ISA");
		return $loop->getContext()->toString();  // "[~,*,:]"
	}

	public function testGetLoop() {
		$loop = new Loop(new Context('~', '*', ':'), "X12");
		$loop->addChild("ISA");
		$loop->addChild("GS");
		$loop->addChild("ST");
		$loop->addChild("1000A");
		$loop->addChild("1000B");
		$loop->addChild("2000");
		$loop->addChild("2100");
		$loop->addChild("2110");
		$loop->addChild("GE");
		$loop->addChild("IEA");
		return $loop->getLoop(3)->getName() === "1000A" ? "right" : "wrong";
	}

	public function testGetSegment() {
		$loop = new Loop(new Context('~', '*', ':'), "ST");		
		$loop->addSegment("ST*835*000000001");
		$loop->addSegment("BPR*DATA*NOT*VALID*RANDOM*TEXT");
		$loop->addSegment("DTM*111*20090915");
		return $loop->getSegment(2)->getElement(0) === "DTM" ? "right" : "wrong";
	}

	public function testGetName() {
		$loop = new Loop(new Context('~', '*', ':'), "ST");
		return $loop->getName() === "ST" ? "right" : "wrong";
	}

	// public function testIterator() {
	// 	$loop = new Loop(new Context('~', '*', ':'), "ST");
	// 	assertNotNull($loop->iterator());
	// }

	public function testRemoveLoop() {
		$loop = new Loop(new Context('~', '*', ':'), "X12");
		$loop->addChild("ISA");
		$loop->addChild("GS");
		$loop->addChild("ST");
		$loop->addChild("1000A");
		$loop->addChild("1000B");
		$loop->addChild("2000");
		$loop->addChild("2100");
		$loop->addChild("2110");
		$loop->addChild("SE");
		$loop->addChild("GE");
		$loop->addChild("IEA");

		// $l1 = $loop->removeLoop(3);
		// return $l1->getName() === "1000A" ? "right" : "wrong";
		
		$l2 = $loop->removeLoop(0);
		return $l2->getName() === "ISA" ? "right" : "wrong";
		
		// $l3 = $loop->removeLoop(1);
		// return $l3->getName() === "ST" ? "right" : "wrong";
	}

	public function testRemoveSegment() {
		$loop = new Loop(new Context('~', '*', ':'), "ST");
		$loop->addSegment("BPR*DATA*NOT*VALID*RANDOM*TEXT");
		$loop->addSegment("TRN*1*0000000000*1999999999");
		$loop->addSegment("DTM*111*20090915");
		$loop->addSegment(0, "ST*835*000000001");
		$s = $loop->removeSegment(2);
		// return $s->toString() === "TRN*1*0000000000*1999999999" ? "right" : "wrong";
		return $loop->size() === 3 ? "right" : "wrong";
	}
	
	public function testChildList() {
		$loop = new Loop(new Context('~', '*', ':'), "X12");
		$loop->addChild("ISA");
		$loop->addChild("GS");
		$loop->addChild("ST");
		$loop->addChild("1000A");
		$loop->addChild("1000B");
		$loop->addChild("2000");
		$loop->addChild("2100");
		$loop->addChild("2110");
		$loop->addChild("SE");
		$loop->addChild("GE");
		$loop->addChild("IEA");
		$childList = $loop->childList();
		return sizeof($childList) === 11 ? "right" : "wrong";
	}

	public function testSize() {
		$loop = new Loop(new Context('~', '*', ':'), "ST");
		$loop->addSegment("ST*835*000000001");
		$loop->addSegment("BPR*DATA*NOT*VALID*RANDOM*TEXT");
		$loop->addSegment("DTM*111*20090915");
		return $loop->size() === 3 ? "right" : "wrong";
	}

	public function testSetContext() {
		$loop = new Loop(new Context('a', 'b', 'c'), "ST");
		$context = new Context('~', '*', ':');
		$loop->setContext($context);
		return $loop->getContext()->toString(); // "[~,*,:]"
	}

	public function testSetChildIntString() {
		$loop = new Loop(new Context('~', '*', ':'), "X12");
		$loop->addChild("ISA");
		$loop->addChild("GS");
		$loop->addChild("XX");
		$loop->addChild("1000A");
		$loop->addChild("1000B");
		$loop->addChild("2000");
		$loop->addChild("2100");
		$loop->addChild("2110");
		$loop->addChild("GE");
		$loop->addChild("IEA");
		$loop->setChild(2, "ST"); // test
		return $loop->getLoop(2)->getName() === "ST" ? "right" : "wrong";
	}

	public function testSetChildIntLoop() {
		$loop = new Loop(new Context('~', '*', ':'), "X12");
		$loop->addChild("ISA");
		$loop->addChild("GS");		
		$loop->addChild("XX");
		$loop->addChild("1000A");
		$loop->addChild("1000B");
		$loop->addChild("2000");
		$loop->addChild("2100");
		$loop->addChild("2110");
		$loop->addChild("GE");
		$loop->addChild("IEA");
		$loop->setChild(2, new Loop(new Context('~', '*', ':'), "ST"));
		return $loop->getLoop(2)->getName() === "ST" ? "right" : "wrong";
	}

	public function testSetSegmentInt() {
		$loop = new Loop(new Context('~', '*', ':'), "ST");
		$loop->addSegment("NOT*THE*RIGHT*SEGMENT");
		$loop->addSegment("BPR*DATA*NOT*VALID*RANDOM*TEXT");
		$loop->addSegment("TRN*1*0000000000*1999999999");
		$loop->addSegment("DTM*111*20090915");
		$segment = new Segment(new Context('~', '*', ':'));
		$segment->addElements("ST*835*000000001");
		$loop->setSegment(0, $segment);
		return $loop->getSegment(0)->getElement(0) === "ST" ? "right" : "wrong";
	}

	public function testSetSegmentIntString() {
		$loop = new Loop(new Context('~', '*', ':'), "ST");
		$loop->addSegment("NOT*THE*RIGHT*SEGMENT");
		$loop->addSegment("BPR*DATA*NOT*VALID*RANDOM*TEXT");
		$loop->addSegment("TRN*1*0000000000*1999999999");
		$loop->addSegment("DTM*111*20090915");
		$segment = new Segment(new Context('~', '*', ':'));
		$segment->addElements("ST*835*000000001");
		$loop->setSegment(0, $segment);
		return $loop->getSegment(0)->getElement(0) === "ST" ? "right" : "wrong";
	}

	public function testSetSegmentIntSegment() {
		$loop = new Loop(new Context('~', '*', ':'), "ST");
		$loop->addSegment("ST*835*000000001");
		$loop->addSegment("BPR*DATA*NOT*VALID*RANDOM*TEXT");
		$loop->addSegment("DTM*111*20090915");
		$loop->addSegment("NOT*THE*RIGHT*SEGMENT");
		$loop->setSegment(2, "TRN*1*0000000000*1999999999");
		return $loop->getSegment(2)->getElement(0) === "TRN" ? "right" : "wrong";
	}

	public function testSetName() {
		$loop = new Loop(new Context('~', '*', ':'), "AB");
		$loop->setName("ST");
		return $loop->getName(); // "ST"
	}

	public function testToString() {
		$loop = new Loop(new Context('~', '*', ':'), "ST");
		$loop->addSegment("ST*835*000000001");
		return $loop->toString(); // "ST*835*000000001~"
	}

	public function testToStringRemoveTrailingEmptyElements() {
		$loop = new Loop(new Context('~', '*', ':'), "ST");
		$s = $loop->addSegment("ST*835*000000001");
		$s->addElement("");
		$s->addElement("");
		return $loop->toString(true); // "ST*835*000000001~"
	}

	public function testToStringRemoveTrailingEmptyElementsTwo() {
		$loop = new Loop(new Context('~', '*', ':'), "ST");
		$s = $loop->addSegment("ST*835*000000001***ST05");
		$s->addElement(null);
		$s->addElement(null);
		return $loop->toString(true); // "ST*835*000000001***ST05~"
	}

	public function testToStringRemoveTrailingEmptyElementsThree() {
		$loop = new Loop(new Context('~', '*', ':'), "ST");
		$s1 = $loop->addSegment("ST1*ST101*ST102***ST105");
		$s1->addElement(null);
		$s1->addElement(null);
		$s2 = $loop->addSegment("ST2*ST201*ST202***ST205");
		$s2->addElement("");
		$s2->addElement("");	
		return $loop->toString(true); //"ST1*ST101*ST102***ST105~ST2*ST201*ST202***ST205~"
	}

	public function testToXML() {
		$loop = new Loop(new Context('~', '*', ':'), "ST");
		$loop->addSegment("ST*835*000000001");
		return $loop->toXML();// === "<LOOP NAME=\"ST\"><ST><ST01><![CDATA[835]]></ST01><ST02><![CDATA[000000001]]></ST02></ST></LOOP>" ? "right" : "wrong";
	}

}
