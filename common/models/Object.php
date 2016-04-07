<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseObject;
use yii\behaviors\TimestampBehavior;
use common\models\Student;

class Object extends BaseObject
{

    public static function tableName()
    {
        return '{{%object}}';
    }

    public function rules()
    {
        return [
            [['content'], 'string'],
            [['content'], 'required'],
            [['content', 'created_time', 'updated_time'], 'safe'],
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
            'content' => 'Content',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }

    public function getStudents() {
        return $this->hasMany(Student::className(), ['id' => 'studentID'])
            ->viaTable('tbl_relation_student_object', ['objectID' => 'id']);
    }

}
