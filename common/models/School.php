<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseSchool;
use yii\behaviors\TimestampBehavior;
use common\models\Address;

class School extends BaseSchool
{
    
    public static function tableName()
    {
        return '{{%school}}';
    }

    public function rules()
    {
        return [
            [['name', 'addressID'], 'required'],
            [['addressID'], 'integer'],
            [['name', 'addressID', 'created_time', 'updated_time'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['name', 'addressID'], 'unique', 'targetAttribute' => ['name', 'addressID'], 'message' => 'The combination of Name and Address ID has already been taken.'],
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
            'addressID' => 'Address ID',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }

    public function getAddress() {
        return $this->hasOne(Address::className(), ['id' => 'addressID']);
    }
}
