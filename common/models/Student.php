<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseStudent;
use yii\behaviors\TimestampBehavior;
use common\models\Object;

class Student extends BaseStudent
{
    const MALE = 1;
    const FEMALE = 2;

    static $gender = [
        self::MALE => 'Nam',
        self::FEMALE => 'Nữ',
    ];

    public static function tableName()
    {
        return '{{%student}}';
    }

    
    public function rules()
    {
        return [
            [['name', 'gender', 'birthday', 'currentAddressID', 'nativeAddressID', 'ethnicID'], 'required'],
            [['gender', 'currentAddressID', 'nativeAddressID', 'ethnicID', 'religionID'], 'integer'],
            [['name', 'gender', 'birthday', 'currentAddressID', 'nativeAddressID', 'ethnicID', 'fatherName', 'fatherJob', 'motherName', 'motherJob', 'tutorName', 'tutorJob', 'created_time', 'updated_time'], 'safe'],
            [['name'], 'string', 'max' => 200],
            [['imageURL'], 'string', 'max' => 255],
            [['fatherName', 'fatherJob', 'motherName', 'motherJob', 'tutorName', 'tutorJob'], 'string', 'max' => 100],
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

    public function getEthnic() {
        return $this->hasOne(Ethnic::className(), ['id' => 'ethnicID']);
    }

    public function getReligion() {
        return $this->hasOne(Religion::className(), ['id' => 'religionID']);
    }

    public function getCurrentAddress() {
        return $this->hasOne(Address::className(), ['id' => 'currentAddressID']);
    }

    public function getNativeAddress() {
        return $this->hasOne(Address::className(), ['id' => 'nativeAddressID']);
    }

    public function getGenderText() {
        if(isset(self::$gender[$this->gender])) {
            return self::$gender[$this->gender];
        } else {
            throw new Exception("Error : Unknow genderText with genderType ".$this->gender, 1);
        }
    }

    public function getGenderIndex($genderText) {
        $key = array_search($genderText, self::$gender);
        if($key != false) {
            return $key;
        } else {
            throw new Exception("Error : Unknow gender ".$genderText, 1);
        }
    }

    public function getObjects() {
        return $this->hasMany(Object::className(), ['id' => 'objectID'])
            ->viaTable('tbl_relation_student_object', ['studentID' => 'id']);
    }

    public function getObjectsText() {
        if($this->objects == null) {
            return null;
        } else {
            $return = "";
            foreach ($this->objects as $object) {
                $return .= $object->content . ", ";
            }
            return substr($return, 0, -2);
        }
    }

    public function getSchoolReport() {
        return $this->hasOne(SchoolReport::className(), ['studentID' => 'id']);
    }

}
