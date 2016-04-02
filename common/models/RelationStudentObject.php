<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseRelationStudentObject;
use yii\behaviors\TimestampBehavior;

class RelationStudentObject extends BaseRelationStudentObject
{
    
    public static function tableName()
    {
        return '{{%relation_student_object}}';
    }

    public function rules()
    {
        return [
            [['studentID', 'objectID'], 'required'],
            [['studentID', 'objectID'], 'integer'],
            [['studentID', 'objectID', 'created_time', 'updated_time'], 'safe'],
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
            'studentID' => 'Student ID',
            'objectID' => 'Object ID',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
