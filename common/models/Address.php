<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseAddress;

/**
 * This is the model class for table "{{%address}}".
 *
 * @property integer $id
 * @property string $detailAddress
 * @property integer $communeID
 * @property integer $districtID
 * @property string $created_time
 * @property string $updated_time
 */
class Address extends BaseAddress
{

    public static function tableName()
    {
        return '{{%address}}';
    }

    public function rules()
    {
        return [
            [['detailAddress', 'districtID'], 'required'],
            [['communeID', 'districtID'], 'integer'],
            [['detailAddress', 'districtID', 'created_time', 'updated_time'], 'safe'],
            [['detailAddress'], 'string', 'max' => 255],
            [['detailAddress', 'communeID', 'districtID'], 'unique', 'targetAttribute' => ['detailAddress', 'communeID', 'districtID'], 'message' => 'The combination of Detail Address, Commune ID and District ID has already been taken.'],
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
}
