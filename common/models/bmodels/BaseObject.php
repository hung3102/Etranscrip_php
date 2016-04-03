<?php

namespace common\models\bmodels;

use Yii;

/**
 * This is the model class for table "{{%object}}".
 *
 * @property integer $id
 * @property string $content
 * @property string $created_time
 * @property string $updated_time
 */
class BaseObject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%object}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['created_time', 'updated_time'], 'required'],
            [['created_time', 'updated_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => 'Content',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
