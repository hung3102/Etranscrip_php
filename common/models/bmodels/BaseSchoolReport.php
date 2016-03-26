<?php

namespace common\models\bmodels;

use Yii;

/**
 * This is the model class for table "{{%school_report}}".
 *
 * @property integer $id
 * @property string $number
 * @property integer $studentID
 * @property string $date
 * @property string $created_time
 * @property string $updated_time
 */
class BaseSchoolReport extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%school_report}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number', 'studentID', 'date', 'created_time', 'updated_time'], 'required'],
            [['studentID'], 'integer'],
            [['date', 'created_time', 'updated_time'], 'safe'],
            [['number'], 'string', 'max' => 50],
            [['studentID'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Number',
            'studentID' => 'Student ID',
            'date' => 'Date',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
