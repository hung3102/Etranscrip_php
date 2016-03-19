<?php
namespace backend\components\x12\x12test;
use backend\components\x12\Cf;

class CfTest {

	public function testCfString() {
		$cf = new Cf("ISA");
		if($cf != null) {
			return "right";
		} else {
			return "wrong";
		}
	}

	public function testCfStringString() {
		$cf = new Cf("2300", "CLM");
		if($cf != null) {
			return "right";
		} else {
			return "wrong";
		}
	}

	public function testCfStringStringStringInteger() {
		$cf = new Cf("1000A", "NM1", "41", 1);
		if($cf != null) {
			return "right";
		} else {
			return "wrong";
		}
	}

	public function testAddChildCf() {
		$cf1 = new Cf("ISA");
		$cf2 = new Cf("GS");
		$cf1->addChild($cf2);

		if($cf1->childList()[0] === $cf2) {
			return "right obj";
		} else {
			return "wrong obj";
		}

		if($cf1->childList()[0]->getName() === "GS") {
			return "right name";
		} else {
			return "wrong name";
		}
		
	}

	public function testAddChildStringString() {
		$cf1 = new Cf("GS");
		$cf2 = new Cf("ST", "ST01");
		$cf1->addChild($cf2);
		// if($cf1->childList()[0] === $cf2) {
		// 	return "right obj";
		// } else {
		// 	return "wrong obj";
		// }

		if($cf1->childList()[0]->getName() === "ST") {
			return "right name";
		} else {
			return "wrong name";
		}
	}

	public function testAddChildStringStringStringInteger() {
		$cf1 = new Cf("ST");
		$cf2 = new Cf("1000A", "NM1", "41", 1);
		$cf1->addChild($cf2);
		// if($cf1->childList()[0] === $cf2) {
		// 	return "right obj";
		// } else {
		// 	return "wrong obj";
		// }
		
		// if($cf1->childList()[0]->getName() === "1000A") {
		// 	return "right 1000A";
		// } else {
		// 	return "wrong 1000A";
		// }

		// if($cf1->childList()[0]->getSegment() === "NM1") {
		// 	return "right NM1";
		// } else {
		// 	return "wrong NM1";
		// }
		
		// if( $cf1->childList()[0]->getSegmentQuals()[0] === "41") {
		// 	return "right 41";
		// } else {
		// 	return "wrong 41";
		// }

		if($cf1->childList()[0]->getSegmentQualPos() === 1) {
			return "right 1";
		} else {
			return "wrong 1";
		}
	}

	public function testChildList() {
		$cf1 = new Cf("ST");
		$cf1->addChild(new Cf("1000A", "NM1", "41", 1));
		$cf1->addChild(new Cf("1000B", "NM1", "40", 1));

		// if( sizeof($cf1->childList()) === 2) {
		// 	return "right 2";
		// } else {
		// 	return "wrong 2";
		// }
		
		// if( $cf1->childList()[0]->getName() === "1000A") {
		// 	return "right 1000A";
		// } else {
		// 	return "wrong 1000A";
		// }

		if( $cf1->childList()[1]->getName() === "1000B") {
			return "right 1000B";
		} else {
			return "wrong 1000B";
		}
	}

	public function testHasChildren() {
		$cf1 = new Cf("ST");
		$cf1->addChild(new Cf("1000A", "NM1", "41", 1));
		$cf1->addChild(new Cf("1000B", "NM1", "40", 1));
		return $cf1->hasChildren() === true ? "right" : "wrong";
	}

	public function testHasParent() {
		$cf1 = new Cf("ST");
		$cf2 = $cf1->addChild("1000A", "NM1", "41", 1);
		$cf3 = $cf1->addChild("1000B", "NM1", "40", 1);
		// return $cf2->hasParent() === true ? "right" : "wrong";
		return $cf3->hasParent() === true ? "right" : "wrong";
	}

	public function testGetParent() {
		$cf1 = new Cf("ST");
		$cf2 = $cf1->addChild("1000A", "NM1", "41", 1);
		$cf3 = $cf1->addChild("1000B", "NM1", "40", 1);
		// return $cf2->getParent() === $cf1 ? "right" : "wrong";
		return $cf3->getParent() === $cf1 ? "right" : "wrong";
	}

	public function testGetName() {
		$cf = new Cf("ISA");
		return $cf->getName() === "ISA" ? "right" : "wrong";
	}

	public function testGetSegment() {
		$cf = new Cf("2300", "CLM");
		return $cf->getSegment() === "CLM" ? "right" : "wrong";
	}

	public function testGetSegmentQuals() {
		$cf = new Cf("1000A", "NM1", "41", 1);
		return $cf->getSegmentQuals()[0] === "41" ? "right" : "wrong";
	}

	public function testGetSegmentQualPos() {
		$cf = new Cf("1000A", "NM1", "41", 1);
		return $cf->getSegmentQualPos() === 1 ? "right" : "wrong";
	}

	public function testSetParent() {
		$cf1 = new Cf("ST");
		$cf2 = new Cf("1000A", "NM1", "41", 1);
		$cf2->setParent($cf1);
		return $cf2->getParent() === $cf1 ? "right" : "wrong";
	}

	public function testSetChildren() {
		$cf1 = new Cf("ST");
		$cf2 = new Cf("1000A", "NM1", "41", 1); //
		$cf3 = new Cf("1000B", "NM1", "40", 1); //
		$kids = [];
		$kids[] = $cf2;
		$kids[] = $cf3;
		$cf1->setChildren($kids);
		return sizeof($cf1->childList()) === 2 ? "right" : "wrong";
	}

	public function testSetName() {
		$cf = new Cf("XXX");
		$cf->setName("ISA");
		return $cf->getName() === "ISA" ? "right" : "wrong";
	}

	public function testSetSegment() {
		$cf = new Cf("XXXX", "XXX");
		$cf->setSegment("CLM");
		return $cf->getSegment() === "CLM" ? "right" : "wrong";
	}

	public function testSetSegmentQuals() {
		$cf = new Cf("1000A", "NM1");
		$quals[] = "41";
		$cf->setSegmentQuals($quals);
		return $cf->getSegmentQuals()[0] === "41" ? "right" : "wrong";
	}

	public function testSetSegmentQualPos() {
		$cf = new Cf("1000A", "NM1");
		$quals[] = "41";
		$cf->setSegmentQuals($quals);
		$cf->setSegmentQualPos(1);
		return $cf->getSegmentQualPos() === 1 ? "right" : "wrong";
	}

	public function testToString() {
		$cf = new Cf("1000A", "NM1", "41", 1);
		return $cf->toString();
	}

}
