<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseSubjectScore;

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
            [['score'], 'number'],
            [['termEvaluationID', 'subjectID', 'score', 'teacherName', 'created_time', 'updated_time'], 'safe'],
            [['teacherName'], 'string', 'max' => 100],
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
