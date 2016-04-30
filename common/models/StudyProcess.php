<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseStudyProcess;
use yii\behaviors\TimestampBehavior;

class StudyProcess extends BaseStudyProcess
{

    public static function tableName()
    {
        return '{{%study_process}}';
    }

    public function rules()
    {
        return [
            [['schoolReportID', 'fromYear', 'toYear', 'principalName', 'class', 'schoolID'], 'required'],
            [['schoolReportID', 'schoolID', 'fromYear', 'toYear'], 'integer'],
            [['fromYear', 'toYear'], 'in', 'range' => range(1900, 2200)],
            [['schoolReportID', 'fromYear', 'toYear', 'class', 'schoolID', 'principalName', 'created_time', 'updated_time'], 'safe'],
            [['class', 'principalName'], 'string', 'max' => 50],
            // [['fromYear', 'toYear', 'principalName', 'class', 'schoolID', 'principalName'], 
            // 'myRequired'],
            // [['fromYear', 'toYear', 'class', 'schoolID', 'principalName'], 'required', 
            // 'when' => function($model) {
            //     if($model->toYear != null || $model->fromYear != null || $model->class != null || $model->schoolID != null || $model->principalName != null) {
            //         return true;
            //     } else {
            //         return false;
            //     }
            // }]
        ];
    }

    public function checkClientSide() {
        return 'function() {
            var fromYear = $(".fromYear .input").val();
            var toYear = $(".toYear .input").val();
            var className = $(".class_content .input").val();
            var schoolID = $(".school .input").val();
            var principalName = $(".confirm_content .input").val();
            if(fromYear != "" || toYear != "" || className != "" || schoolID != "" || principalName != "") 
            {
                return true;
            } else {
                return false;
            }
        }';
    }

    public function myRequired($attribute, $params) {
        // if($this->fromYear != null || $this->toYear != null || $this->class != null || $this->schoolID != null || $this->principalName != null) {
        //     if($this->fromYear == null) {
        //         $this->addError($attribute, 'From year can not be blank');
        //     }
        //     if($this->toYear == null) {
        //         $this->addError($attribute, 'To year can not be blank');
        //     }
        //     if($this->class == null) {
        //         $this->addError($attribute, 'Class can not be blank');
        //     }
        //     if($this->schoolID == null) {
        //         $this->addError($attribute, 'School can not be blank');
        //     }
        //     if($this->principalName == null) {
        //         $this->addError($attribute, 'Principal Name can not be blank');
        //     }
        // }
        if($this->fromYear != null) {
            $this->addError($attribute, 'From year can not be blank');
        }
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
            'schoolReportID' => 'School Report ID',
            'fromYear' => 'From Year',
            'toYear' => 'To Year',
            'class' => 'Class',
            'schoolID' => 'School ID',
            'principalName' => 'Principle Name',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }

    public function getSchool() {
        return $this->hasOne(School::className(), ['id' => 'schoolID']);
    }
}
