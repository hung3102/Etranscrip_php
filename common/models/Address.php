<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseAddress;
use yii\behaviors\TimestampBehavior;

class Address extends BaseAddress
{

    public static function tableName()
    {
        return '{{%address}}';
    }

    public function rules()
    {
        return [
            [['districtID'], 'required'],
            [['communeID', 'districtID'], 'integer'],
            [['detailAddress', 'districtID', 'created_time', 'updated_time'], 'safe'],
            [['detailAddress'], 'string', 'max' => 255],
            [['detailAddress', 'communeID', 'districtID'], 'unique', 'targetAttribute' => ['detailAddress', 'communeID', 'districtID'], 'message' => 'The combination of Detail Address, Commune ID and District ID has already been taken.'],
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
            'detailAddress' => 'Detail Address',
            'communeID' => 'Commune ID',
            'districtID' => 'District ID',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }

    public function getCommuneName() {
        if($this->communeID == null) {
            return null;
        }
        if(Commune::findOne($this->communeID) == null) {
            throw new Exception("Commune not found with id ".$this->communeID, 1);
        }
        return $this->hasOne(Commune::className(), ['id' => 'communeID'])->one()->name;
    }

    public function getDistrict() {
        return $this->hasOne(District::className(), ['id' => 'districtID']);
    }

    public function getCommune() {
        return $this->hasOne(Commune::className(), ['id' => 'communeID']);
    }

    public function getDistrictName() {
        if($this->district == null) {
            throw new Exception("District not found with id ". $this->districtID, 1);
        }
        return $this->district->name;
    }

    public function getFullAddress() {
        if($this->detailAddress != null) {
            if($this->getCommuneName() != null) {
                return $this->detailAddress.', '.$this->getCommuneName().', '
                    .$this->getDistrictName().', '.$this->district->getProvinceName().'.';
            } else {
                return $this->detailAddress.', '.$this->getDistrictName().', '
                    .$this->district->getProvinceName().'.';
            }
        } else {
            if($this->getCommuneName() != null) {
                return $this->getCommuneName().', '.$this->getDistrictName().', '
                    .$this->district->getProvinceName().'.';
            } else {
                return $this->getDistrictName().', '.$this->district->getProvinceName().'.';
            }
        }
    }

    public function getFullReverseAddress() {
        $return = "";
        $return .= $this->district->province->name . ", ". $this->district->name;
        if($this->commune != null) {
            $return .= ", " . $this->commune->name;
        }
        if($this->detailAddress != null) {
            $return .= ", " . $this->detailAddress;
        }
        return $return;
    }

}
