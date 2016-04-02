<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use common\models\bmodels\BaseTermEvaluation;
use yii\behaviors\TimestampBehavior;

class TermEvaluation extends BaseTermEvaluation
{

    const FIRST_TERM = 1;
    const SECOND_TERM = 2;
    const WHOLE_YEAR = 0;

    static $term = [
        self::FIRST_TERM => "Học kỳ 1",
        self::SECOND_TERM => "Học kỳ 2",
        self::WHOLE_YEAR => "Cả năm",
    ];

    public static function tableName()
    {
        return '{{%term_evaluation}}';
    }

    public function rules()
    {
        return [
            [['yearEvaluationID', 'term', 'learnCapacity', 'conduct'], 'required'],
            [['yearEvaluationID', 'term'], 'integer'],
            [['yearEvaluationID', 'term', 'learnCapacity', 'conduct', 'created_time', 'updated_time'], 'safe'],
            [['learnCapacity', 'conduct'], 'string', 'max' => 20],
            [['yearEvaluationID', 'term'], 'unique', 'targetAttribute' => ['yearEvaluationID', 'term'], 'message' => 'The combination of Year Evaluation ID and Term has already been taken.'],
        ];
    }

    public function behaviors()
    {
        date_default_timezone_set('Asia/Saigon');
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => date('Y:m:d h:i:s'),
                'createdAtAttribute' => 'created_time',
                'updatedAtAttribute' => 'updated_time',
            ],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'yearEvaluationID' => 'Year Evaluation ID',
            'term' => 'Term',
            'learnCapacity' => 'Learn Capacity',
            'conduct' => 'Conduct',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }

    public function getTermText() {
        if(isset(self::$term[$this->term])) {
            return self::$term[$this->term];
        } else {
            throw new Exception("Error : Doesn't know index ".$this->term." of term", 1);
        }
    }

    public function getTermIndex($termText) {
        $key = array_search($termText, self::$term);
        if($key !== false) {
            return $key;
        } else {
            throw new Exception("Error : Doesn't know term name ".$termText, 1);
        }
    }
}
