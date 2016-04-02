<?php

namespace common\models\bmodels;

use Yii;

/**
 * This is the model class for table "{{%study_process}}".
 *
 * @property integer $id
 * @property integer $schoolReportID
 * @property string $fromYear
 * @property string $toYear
 * @property string $class
 * @property integer $schoolID
 * @property string $principalName
 * @property string $created_time
 * @property string $updated_time
 */
class BaseStudyProcess extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%study_process}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['schoolReportID', 'fromYear', 'toYear', 'class', 'schoolID', 'created_time', 'updated_time'], 'required'],
            [['schoolReportID', 'schoolID'], 'integer'],
            [['fromYear', 'toYear', 'created_time', 'updated_time'], 'safe'],
            [['class', 'principalName'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'schoolReportID' => 'School Report ID',
            'fromYear' => 'From Year',
            'toYear' => 'To Year',
            'class' => 'Class',
            'schoolID' => 'School ID',
            'principalName' => 'Principle Name',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
