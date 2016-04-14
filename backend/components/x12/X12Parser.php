<?php
namespace backend\components\x12;

/**
 * The class represents methods used to translate a X12 transaction represented
 * as a file or string into an X12 object.
 */
class X12Parser extends FormatException implements Parser {

 	const SIZE = 106;
	const POS_SEGMENT = 105;
	const POS_ELEMENT = 3;
	const POS_COMPOSITE_ELEMENT = 104;

	private $x12Cf; // Cf type
	private $cfMarker; // Cf type
	private $loopMarker; //Loop type

	public function __construct() {
		$a = func_get_args();
		$i = func_num_args();
		if($i == 1 && method_exists($this, $f = 'init')) {
			call_user_func_array(array($this, $f), $a);
		}
	}

	private function init($cf) {
		$this->x12Cf = $cf;
	}

	/**
	 * The method takes a X12 file and converts it into a X2 object. The X12
	 * class has methods to convert it into XML format as well as methods to
	 * modify the contents.
	 * 
	 * @param fileName
	 *            a X12 file
	 * @return the X12 object
	 * @throws FormatException
	 * @throws IOException
	 */
	
	public function parse($file) { // File type
		$f = fopen($file, 'r');
		$line = fgets($f);
		fclose($f);
		$line = trim($line, "\n\r\n");
		if(strlen($line) != self::SIZE) {
			throw new FormatException("Error: Size of ISA segment line in the file is not right!", 1);
		}
		$context = new Context();
		$context->setSegmentSeparator($line[self::POS_SEGMENT]);
		$context->setElementSeparator($line[self::POS_ELEMENT]);
		$context->setCompositeElementSeparator($line[self::POS_COMPOSITE_ELEMENT]);

		$x12 = $this->scanSource($file, $context);
		return $x12;
	}

	/**
	 * private helper method
	 * @param scanner
	 * @param context
	 * @return
	 */
	private function scanSource($file, $context) {
		$file_content = file_get_contents($file);

		$segmentSeparator = $context->getSegmentSeparator();
		// \r\n is newline in window and \n in unix system
		$delimiter = '/(' . $segmentSeparator . '\n|' . $segmentSeparator . '\r\n|' 
					. $segmentSeparator . '$)/';  
		$segments_array = preg_split($delimiter, $file_content, -1, PREG_SPLIT_NO_EMPTY);

		$this->cfMarker = $this->x12Cf;
		$x12 = new X12($context);
		$this->loopMarker = $x12;
		$loop = $x12;

		foreach ($segments_array as $line) {
			$tokens = explode($context->getElementSeparator(), $line);
			if($this->doesChildLoopMatch($this->cfMarker, $tokens)) {
				$loop = $loop->addChild($this->cfMarker->getName());
				$loop->addSegment($line);
			} else if($this->doesParentLoopMatch($this->cfMarker, $tokens, $loop)) {
				$loop = $this->loopMarker->addChild($this->cfMarker->getName());
				$loop->addSegment($line);
			} else {
				$loop->addSegment($line);
			}
		}
		
		return $x12;
	}
 
	/**
	 * Checks if the segment (or line read) matches to current loop
	 * 
	 * @param cf
	 *            Cf
	 * @param tokens
	 *            String[] represents the segment broken into elements
	 * @return boolean
	 */
	private function doesLoopMatch($cf, $tokens) {
		if($cf->getSegment() === $tokens[0]) {
			if($cf->getSegmentQualPos() === null) {
				return true;
			} else {
				foreach ($cf->getSegmentQuals() as $qual) {
					if($qual === $tokens[$cf->getSegmentQualPos()]) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Checks if the segment (or line read) matches to any of the child loops
	 * configuration.
	 * 
	 * @param cf
	 *            Cf
	 * @param tokens
	 *            String[] represents the segment broken into elements
	 * @return boolean
	 */
	private function doesChildLoopMatch($parent, $tokens) {
		foreach ($parent->childList() as $cf) {
			if($this->doesLoopMatch($cf, $tokens)) {
				$this->cfMarker = $cf;
				return true;
			}
		}
		return false;
	}

	/**
	 * Checks if the segment (or line read) matches the parent loop
	 * configuration.
	 * 
	 * @param cf
	 *            Cf
	 * @param tokens
	 *            String[] represents the segment broken into elements
	 * @param loop
	 *            Loop            
	 * @return boolean
	 */
	private function doesParentLoopMatch($child, $tokens, $loop) {
		$parent = $child->getParent();
		if ($parent === null)
			return false;
		
		$this->loopMarker = $loop->getParent();
		foreach ($parent->childList() as $cf) {
			if($this->doesLoopMatch($cf, $tokens)) {
				$this->cfMarker = $cf;
				return true;
			}
		}
		if($this->doesParentLoopMatch($parent, $tokens, $this->loopMarker))
			return true;
		
		return false;
	}
}
