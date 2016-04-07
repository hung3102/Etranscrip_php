<?php
namespace backend\components\x12;

/**
 * The X12 class is the object representation of an ANSI X12
 * transaction. The building block of an X12 transaction is an element. Some
 * elements may be made of sub elements. Elements combine to form segments.
 * Segments are grouped as loops. And a set of loops form an X12 transaction.
 */

class X12 extends Loop implements EDI {

   public function __construct() {
      $a = func_get_args();
      $i = func_num_args();
      if($i == 1 && method_exists($this, $f = 'init')) {
         call_user_func_array(array($this, $f), $a);
      }
   }

	/**
	 * The constructor takes a context object.
	 * 
	 * @param c
	 *            a Context object
	 */
	public function init($c) {
		parent::init($c, "X12");
	}
}
