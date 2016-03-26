<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseRelationSchoolStudent;

class RelationSchoolStudent extends BaseRelationSchoolStudent
{
    
    public static function tableName()
    {
        return '{{%relation_school_student}}';
    }

    public function rules()
    {
        return [
            [['schoolID', 'studentID'], 'required'],
            [['schoolID', 'studentID'], 'integer'],
            [['schoolID', 'studentID', 'created_time', 'updated_time'], 'safe'],
            [['schoolID', 'studentID'], 'unique', 'targetAttribute' => ['schoolID', 'studentID'], 'message' => 'The combination of School ID and Student ID has already been taken.'],
        ];
    }

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
