<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use common\models\bmodels\BaseTermEvaluation;
use yii\behaviors\TimestampBehavior;
use common\models\SubjectScore;
use common\models\Subject;
use common\models\YearEvaluation;

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

    public function getSubjectScores() {
        return $this->hasMany(SubjectScore::className(), ['termEvaluationID' => 'id']);
    }

    public function getYearEvaluation() {
        return $this->hasOne(YearEvaluation::className(), ['id' => 'yearEvaluationID']);
    }

    public function getSubjectScore($subjectName) {
        $subject = Subject::findOne(['name' => $subjectName]);
        if($subject == null) {
            throw new Exception("Error: Unknow subject ".$subjectName, 1);
        }
        if($this->subjectScores == null) {
            return null;
        }
        foreach ($this->subjectScores as $subjectScore) {
            if($subjectScore->subject->name == $subjectName) {
                return $subjectScore;
            }
        }
        return false;
    }

    public function getAverageScore() {
        $advantageSubjects = [];
        if($this->yearEvaluation == 'KHTN') {
            $advantageSubjects = ['Toán', 'Vật lý', 'Hóa học', 'Sinh học'];
        } else if($this->yearEvaluation == 'KHXH & NV') {
            $advantageSubjects = ['Ngữ văn', 'Địa lý', 'Lịch sử', 'Ngoại ngữ'];
        }
        $total = 0;
        $count = 0;
        foreach ($this->subjectScores as $subjectScore) {
            if(in_array($subjectScore->subject->name, $advantageSubjects)) {
                if($subjectScore->score != null) {
                    $total += ($subjectScore->score * 2);
                    $count += 2;
                }
            } else {
                if($subjectScore->score != null) {
                    $total += $subjectScore->score;
                    $count += 1;
                }
            }
        }
        if($count != 0) {
            return round($total/$count, 1);
        } else {
            return null;
        }
    }
}
