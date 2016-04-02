<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use common\models\bmodels\BaseDistrict;
use yii\behaviors\TimestampBehavior;
use common\models\Commune;
use common\models\Province;

class District extends BaseDistrict
{

    public static function tableName()
    {
        return '{{%district}}';
    }

    public function rules()
    {
        return [
            [['name', 'provinceID'], 'required'],
            [['provinceID'], 'integer'],
            [['name', 'provinceID', 'created_time', 'updated_time'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['name', 'provinceID'], 'unique', 'targetAttribute' => ['name', 'provinceID'], 'message' => 'The combination of Name and Province ID has already been taken.'],
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
            'provinceID' => 'Province ID',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }

    public function getProvince() {
        return $this->hasOne(Province::className(), ['id' => 'provinceID']);
    }

    public function getProvinceName() {
        if($this->province == null) {
            throw new Exception("Province not found with id ".$this->provinceID, 1);
        }
        return $this->province->name;
    }

    public function getCommunes() {
        return $this->hasMany(Commune::className(), ['districtID' => 'id']);
    }
}
