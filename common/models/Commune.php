<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseCommune;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%commune}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $districtID
 * @property string $created_time
 * @property string $updated_time
 */
class Commune extends BaseCommune
{

    public static function tableName()
    {
        return '{{%commune}}';
    }

    public function rules()
    {
        return [
            [['name', 'districtID'], 'required'],
            [['districtID'], 'integer'],
            [['name', 'districtID', 'created_time', 'updated_time'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['name', 'districtID'], 'unique', 'targetAttribute' => ['name', 'districtID'], 'message' => 'The combination of Name and District ID has already been taken.'],
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
            'districtID' => 'District ID',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }

    public function getDistrict() {
        return $this->hasOne(District::className(), ['id' => 'districtID']);
    }
}
