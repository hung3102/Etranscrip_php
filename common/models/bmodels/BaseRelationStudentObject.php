<?php

namespace common\models\bmodels;

use Yii;

/**
 * This is the model class for table "{{%relation_student_object}}".
 *
 * @property integer $id
 * @property integer $studentID
 * @property integer $objectID
 * @property string $created_time
 * @property string $updated_time
 */
class BaseRelationStudentObject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%relation_student_object}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['studentID', 'objectID', 'created_time', 'updated_time'], 'required'],
            [['studentID', 'objectID'], 'integer'],
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
            'studentID' => 'Student ID',
            'objectID' => 'Object ID',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
