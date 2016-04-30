<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseYearEvaluation;
use yii\behaviors\TimestampBehavior;
use common\models\Achievement;
use common\models\TermEvaluation;

class YearEvaluation extends BaseYearEvaluation
{
    public static $department = ['KHTN', 'KHXH & NV', 'Cơ bản'];

    public static function tableName()
    {
        return '{{%year_evaluation}}';
    }

    public function rules()
    {
        return [
            [['schoolReportID', 'class', 'fromYear', 'toYear', 'studyDepartment', 'note', 'teacherName', 'missedLesson', 'upGradeType', 'teacherComment', 'principalApproval', 'principalName', 'date'], 'required'],
            [['schoolReportID', 'missedLesson'], 'integer'],
            [['schoolReportID', 'class', 'fromYear', 'toYear', 'studyDepartment', 'note', 'teacherName', 'missedLesson', 'principalApproval', 'principalName', 'date', 'created_time', 'updated_time'], 'safe'],
            [['note', 'teacherComment', 'principalApproval'], 'string'],
            [['class'], 'string', 'max' => 20],
            [['studyDepartment'], 'string', 'max' => 10],
            [['teacherName', 'principalName', 'upGradeType'], 'string', 'max' => 100],
            [['vocationalCertificate'], 'string', 'max' => 255],
            [['vocationalCertificateLevel'], 'string', 'max' => 50],
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
            'schoolReportID' => 'School Report ID',
            'class' => 'Class',
            'fromYear' => 'From Year',
            'toYear' => 'To Year',
            'studyDepartment' => 'Study Department',
            'note' => 'Note',
            'teacherName' => 'Teacher Name',
            'missedLesson' => 'Missed Lesson',
            'upGradeType' => 'Up Grade Type',
            'vocationalCertificate' => 'Vocational Certificate',
            'vocationalCertificateLevel' => 'Vocational Certificate Level',
            'teacherComment' => 'Teacher Comment',
            'principalApproval' => 'Principal Approval',
            'principalName' => 'Principal Name',
            'date' => 'Date',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }

    public function getAchievements() {
        return $this->hasMany(Achievement::className(), ['yearEvaluationID' => 'id']);
    }

    public function getTermEvaluations() {
        return $this->hasMany(TermEvaluation::className(), ['yearEvaluationID' => 'id']);
    }

    public function getAchievementString() {
        $achievements = $this->achievements;
        if($achievements == null) {
            return null;
        }
        $return = "";
        foreach ($achievements as $achievement) {
            $return .= $achievement->name . ", ";
        }
        return substr($return, 0, -2);
    }

    public function getAdvantageSubjects() {
        if($this->studyDepartment == 'KHTN') {
            return 'Toán, Lý, Hóa, Sinh';
        } else if($this->studyDepartment == 'Cơ bản') {
            return null;
        } else if($this->studyDepartment == 'KHXH & NV') {
            return 'Văn, Sử, Địa, Ngoại ngữ';
        } else {
            throw new Exception("Error: Unknow study department ".$this->studyDepartment, 1);
        }
    }

    public function checkTermExist($term_value) {
        if($this->termEvaluations == null) {
            return false;
        }
        foreach ($this->termEvaluations as $termEvaluation) {
            if($termEvaluation->term == $term_value) {
                return true;
            }
        }
        return false;
    }

    public function getTerm($term_value) {
        if($this->termEvaluations == null) {
            throw new Exception("Has not term evaluation in this year Evaluation", 1);
        }
        foreach ($this->termEvaluations as $termEvaluation) {
            if($termEvaluation->term == $term_value) {
                return $termEvaluation;
            }
        }
        return null;
    }

    public function getConduct($term_value) {
        if($this->getTerm($term_value) != null) {
            return $this->getTerm($term_value)->conduct;
        } else {
            return null;
        }
    }

    public function getLearnCapacity($term_value) {
        if($this->getTerm($term_value) != null) {
            return $this->getTerm($term_value)->learnCapacity;
        } else {
            return null;
        }
    }
}
