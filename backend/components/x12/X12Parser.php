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
			throw new FormatException("Error: Size of ST segment line in the file is not right!", 1);
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

		$count1 = 0;$count2 = 0;$count3 = 0;
		foreach ($segments_array as $line) {
			$tokens = explode($context->getElementSeparator(), $line);
			if($this->doesChildLoopMatch($this->cfMarker, $tokens)) {
				echo "<pre>";
				print_r("C".++$count1.": ".$line);
				echo "</pre>";
				$loop = $loop->addChild($this->cfMarker->getName());
				$loop->addSegment($line);
			} else if($this->doesParentLoopMatch($this->cfMarker, $tokens, $loop)) {
				echo "<pre>";
				print_r("P".++$count2.": ".$line);
				echo "</pre>";
				$loop = $this->loopMarker->addChild($this->cfMarker->getName());
				$loop->addSegment($line);
			} else {
				echo "<pre>";
				print_r("O".++$count3.": ".$line);
				echo "</pre>";
				$loop->addSegment($line);
			}
		}
		
		return $x12;
	}
	
	/**
	 * The method takes a InputStream and converts it into a X12 object. The X12
	 * class has methods to convert it into XML format as well as methods to
	 * modify the contents.
	 * 
	 * @param source
	 *            InputStream
	 * @return the X12 object
	 * @throws FormatException
	 * @throws IOException
	 */

	// public EDI parse(InputStream source) throws FormatException, IOException {
	// 	StringBuilder strBuffer = new StringBuilder();
	// 	char[] cbuf = new char[1024];
	// 	int length = -1;

	// 	Reader reader = new BufferedReader(new InputStreamReader(source));

	// 	while ((length = reader.read(cbuf)) != -1) {
	// 		strBuffer.append(cbuf, 0, length);
	// 	}

	// 	String strSource = strBuffer.toString();
	// 	return parse(strSource);
	// }
		
	/**
	 * The method takes a X12 string and converts it into a X2 object. The X12
	 * class has methods to convert it into XML format as well as methods to
	 * modify the contents.
	 * 
	 * @param source
	 *            String
	 * @return the X12 object
	 * @throws FormatException
	 * @throws IOException
	 */
	// public EDI parse(String source) throws FormatException {
	// 	if (source.length() < SIZE) {
	// 		throw new FormatException();
	// 	}
	// 	$context = new Context();
	// 	$context->setSegmentSeparator(source.charAt(POS_SEGMENT));
	// 	$context->setElementSeparator(source.charAt(POS_ELEMENT));
	// 	$context->setCompositeElementSeparator(source.charAt(POS_COMPOSITE_ELEMENT));

	// 	$scanner = new Scanner(source);
	// 	$x12 = scanSource(scanner, context);
	// 	scanner.close();
	// 	return x12;
	// }
 
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
