<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseReligion;

/**
 * This is the model class for table "{{%religion}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $created_time
 * @property string $updated_time
 */
class Religion extends BaseReligion
{
    
    public static function tableName()
    {
        return '{{%religion}}';
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
