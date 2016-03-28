<?php

namespace common\models\bmodels;

use Yii;

/**
 * This is the model class for table "{{%school}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $addressID
 * @property string $created_time
 * @property string $updated_time
 */
class BaseSchool extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%school}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'addressID', 'created_time', 'updated_time'], 'required'],
            [['addressID'], 'integer'],
            [['created_time', 'updated_time'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['name', 'addressID'], 'unique', 'targetAttribute' => ['name', 'addressID'], 'message' => 'The combination of Name and Address ID has already been taken.'],
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
            'addressID' => 'Address ID',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
