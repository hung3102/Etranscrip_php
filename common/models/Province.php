<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseProvince;
use yii\behaviors\TimestampBehavior;
use common\models\District;

class Province extends BaseProvince
{
    
    public static function tableName()
    {
        return '{{%province}}';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'created_time', 'updated_time'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['name'], 'unique'],
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
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }

    public function getDistricts() {
        return $this->hasMany(District::className(), ['provinceID' => 'id']);
    }
}
