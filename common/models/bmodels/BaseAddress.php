<?php

namespace common\models\bmodels;

use Yii;

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
class BaseAddress extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%address}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['detailAddress', 'districtID', 'created_time', 'updated_time'], 'required'],
            [['communeID', 'districtID'], 'integer'],
            [['created_time', 'updated_time'], 'safe'],
            [['detailAddress'], 'string', 'max' => 255],
            [['detailAddress', 'communeID', 'districtID'], 'unique', 'targetAttribute' => ['detailAddress', 'communeID', 'districtID'], 'message' => 'The combination of Detail Address, Commune ID and District ID has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
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
