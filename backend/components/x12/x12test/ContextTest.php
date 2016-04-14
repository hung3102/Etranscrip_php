<?php
namespace backend\components\x12\x12test;
use backend\components\x12\Context;

class ContextTest {

	public function testContext() {
		$ctxt = new Context();
		return $ctxt != null ? "right" : "wrong";
	}

	public function testContextCharacterCharacterCharacter() {
		$ctxt = new Context('a', 'b', 'c');
		return $ctxt != null ? "right" : "wrong";
	}

	public function testGetCompositeElementSeparator() {
		$ctxt = new Context('a', 'b', 'c');
		return $ctxt->getCompositeElementSeparator() === 'c' ? "right" : "wrong";
	}

	public function testGetElementSeparator() {
		$ctxt = new Context('a', 'b', 'c');
		return $ctxt->getElementSeparator() === 'b' ? "right" : "wrong";
	}

	public function testGetSegmentSeparator() {
		$ctxt = new Context('a', 'b', 'c');
		return $ctxt->getSegmentSeparator() === 'a' ? "right" : "wrong";
	}

	public function testSetCompositeElementSeparator() {
		$ctxt = new Context();
		$ctxt->setCompositeElementSeparator('c');
		return $ctxt->getCompositeElementSeparator() === 'c' ? "right" : "wrong";
	}

	public function testSetElementSeparator() {
		$ctxt = new Context();
		$ctxt->setElementSeparator('b');
		return $ctxt->getElementSeparator() === 'b' ? "right" : "wrong";
	}

	public function testSetSegmentSeparator() {
		$ctxt = new Context();
		$ctxt->setSegmentSeparator('b');
		return $ctxt->getSegmentSeparator() === 'b' ? "right" : "wrong";
	}

	public function testToString() {
		$ctxt = new Context('a', 'b', 'c');
		return $ctxt->toString();
	}

}
