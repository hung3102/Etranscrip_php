<?php

namespace common\models\bmodels;

use Yii;

/**
 * This is the model class for table "{{%student}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $imageURL
 * @property integer $gender
 * @property string $birthday
 * @property integer $currentAddressID
 * @property integer $nativeAddressID
 * @property integer $ethnicID
 * @property integer $religionID
 * @property string $fatherName
 * @property string $fatherJob
 * @property string $motherName
 * @property string $motherJob
 * @property string $tutorName
 * @property string $tutorJob
 * @property string $created_time
 * @property string $updated_time
 */
class BaseStudent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%student}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'gender', 'birthday', 'currentAddressID', 'nativeAddressID', 'ethnicID', 'fatherName', 'fatherJob', 'motherName', 'motherJob', 'created_time', 'updated_time'], 'required'],
            [['gender', 'currentAddressID', 'nativeAddressID', 'ethnicID', 'religionID'], 'integer'],
            [['birthday', 'created_time', 'updated_time'], 'safe'],
            [['name'], 'string', 'max' => 200],
            [['imageURL'], 'string', 'max' => 255],
            [['fatherName', 'fatherJob', 'motherName', 'motherJob', 'tutorName', 'tutorJob'], 'string', 'max' => 100],
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
