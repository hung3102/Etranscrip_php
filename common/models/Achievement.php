<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseAchievement;

class Achievement extends BaseAchievement
{

    public static function tableName()
    {
        return '{{%achievement}}';
    }

    public function rules()
    {
        return [
            [['name', 'yearEvaluationID'], 'required'],
            [['yearEvaluationID'], 'integer'],
            [['name', 'yearEvaluationID', 'created_time', 'updated_time'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'yearEvaluationID' => 'Year Evaluation ID',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
