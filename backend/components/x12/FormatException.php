<?php
namespace backend\components\x12;
use yii\base\Exception;

/**
 * This class represents the format errors in the X12 transaction that is being
 * read to construct the X12 object.
 */

class FormatException extends Exception {
	private static $serialVersionUID;
}
