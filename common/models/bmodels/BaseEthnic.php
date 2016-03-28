<?php

namespace common\models\bmodels;

use Yii;

/**
 * This is the model class for table "{{%ethnic}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $created_time
 * @property string $updated_time
 */
class BaseEthnic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ethnic}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'created_time', 'updated_time'], 'required'],
            [['created_time', 'updated_time'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['name'], 'unique'],
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
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
