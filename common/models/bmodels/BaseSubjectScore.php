<?php

namespace common\models\bmodels;

use Yii;

/**
 * This is the model class for table "{{%subject_score}}".
 *
 * @property integer $id
 * @property integer $termEvaluationID
 * @property integer $subjectID
 * @property double $score
 * @property string $teacherName
 * @property string $created_time
 * @property string $updated_time
 */
class BaseSubjectScore extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%subject_score}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['termEvaluationID', 'subjectID', 'score', 'teacherName', 'created_time', 'updated_time'], 'required'],
            [['termEvaluationID', 'subjectID'], 'integer'],
            [['score'], 'number'],
            [['created_time', 'updated_time'], 'safe'],
            [['teacherName'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
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
}
