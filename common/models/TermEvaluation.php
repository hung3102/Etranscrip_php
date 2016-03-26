<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseTermEvaluation;

class TermEvaluation extends BaseTermEvaluation
{

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
}
