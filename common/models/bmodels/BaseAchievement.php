<?php

namespace common\models\bmodels;

use Yii;

/**
 * This is the model class for table "{{%achievement}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $yearEvaluationID
 * @property string $created_time
 * @property string $updated_time
 */
class BaseAchievement extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%achievement}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'yearEvaluationID', 'created_time', 'updated_time'], 'required'],
            [['yearEvaluationID'], 'integer'],
            [['created_time', 'updated_time'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
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
