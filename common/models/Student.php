<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseStudent;

class Student extends BaseStudent
{
    
    public static function tableName()
    {
        return '{{%student}}';
    }

    
    public function rules()
    {
        return [
            [['name', 'gender', 'birthday', 'currentAddressID', 'nativeAddressID', 'ethnicID'], 'required'],
            [['gender', 'currentAddressID', 'nativeAddressID', 'ethnicID', 'religionID'], 'integer'],
            [['name', 'gender', 'birthday', 'currentAddressID', 'nativeAddressID', 'ethnicID', 'fatherName', 'fatherJob', 'motherName', 'motherJob', 'created_time', 'updated_time'], 'safe'],
            [['name'], 'string', 'max' => 200],
            [['imageURL'], 'string', 'max' => 255],
            [['fatherName', 'fatherJob', 'motherName', 'motherJob', 'tutorName', 'tutorJob'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'imageURL' => 'Image Url',
            'gender' => 'Gender',
            'birthday' => 'Birthday',
            'currentAddressID' => 'Current Address ID',
            'nativeAddressID' => 'Native Address ID',
            'ethnicID' => 'Ethnic ID',
            'religionID' => 'Religion ID',
            'fatherName' => 'Father Name',
            'fatherJob' => 'Father Job',
            'motherName' => 'Mother Name',
            'motherJob' => 'Mother Job',
            'tutorName' => 'Tutor Name',
            'tutorJob' => 'Tutor Job',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
