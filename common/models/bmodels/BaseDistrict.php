<?php

namespace common\models\bmodels;

use Yii;

/**
 * This is the model class for table "{{%district}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $provinceID
 * @property string $created_time
 * @property string $updated_time
 */
class BaseDistrict extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%district}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'provinceID', 'created_time', 'updated_time'], 'required'],
            [['provinceID'], 'integer'],
            [['created_time', 'updated_time'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['name', 'provinceID'], 'unique', 'targetAttribute' => ['name', 'provinceID'], 'message' => 'The combination of Name and Province ID has already been taken.'],
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
            'provinceID' => 'Province ID',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
