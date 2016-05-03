<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseSubjectScore;
use yii\behaviors\TimestampBehavior;

class SubjectScore extends BaseSubjectScore
{

    public static function tableName()
    {
        return '{{%subject_score}}';
    }

    public function rules()
    {
        return [
            [['termEvaluationID', 'subjectID', 'score', 'teacherName'], 'required'],
            [['termEvaluationID', 'subjectID'], 'integer'],
            [['score'], 'number', 'max'=>10, 'min'=>0],
            [['termEvaluationID', 'subjectID', 'score', 'teacherName', 'created_time', 'updated_time'], 'safe'],
            [['teacherName'], 'string', 'max' => 100],
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
            'termEvaluationID' => 'Term Evaluation ID',
            'subjectID' => 'Subject ID',
            'score' => 'Score',
            'teacherName' => 'Teacher Name',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }

    public function getSubject() {
        return $this->hasOne(Subject::className(), ['id' => 'subjectID']);
    }
}
