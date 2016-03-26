<?php

namespace common\models\bmodels;

use Yii;

/**
 * This is the model class for table "{{%relation_school_student}}".
 *
 * @property integer $id
 * @property integer $schoolID
 * @property integer $studentID
 * @property string $created_time
 * @property string $updated_time
 */
class BaseRelationSchoolStudent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%relation_school_student}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['schoolID', 'studentID', 'created_time', 'updated_time'], 'required'],
            [['schoolID', 'studentID'], 'integer'],
            [['created_time', 'updated_time'], 'safe'],
            [['schoolID', 'studentID'], 'unique', 'targetAttribute' => ['schoolID', 'studentID'], 'message' => 'The combination of School ID and Student ID has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'schoolID' => 'School ID',
            'studentID' => 'Student ID',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
