<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseProvince;

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

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
