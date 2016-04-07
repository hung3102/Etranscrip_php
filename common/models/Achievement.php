<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseAchievement;
use yii\behaviors\TimestampBehavior;

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
}
