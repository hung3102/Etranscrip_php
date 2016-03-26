<?php

namespace common\models\bmodels;

use Yii;

/**
 * This is the model class for table "{{%year_evaluation}}".
 *
 * @property integer $id
 * @property integer $schoolReportID
 * @property string $class
 * @property string $fromYear
 * @property string $toYear
 * @property string $studyDepartment
 * @property string $note
 * @property string $teacherName
 * @property integer $missedLesson
 * @property string $upGradeType
 * @property string $vocationalCertificate
 * @property string $vocationalCertificateLevel
 * @property string $teacherComment
 * @property string $principalApproval
 * @property string $principalName
 * @property string $date
 * @property string $created_time
 * @property string $updated_time
 */
class BaseYearEvaluation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%year_evaluation}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['schoolReportID', 'class', 'fromYear', 'toYear', 'studyDepartment', 'note', 'teacherName', 'missedLesson', 'upGradeType', 'principalApproval', 'principalName', 'date', 'created_time', 'updated_time'], 'required'],
            [['schoolReportID', 'missedLesson'], 'integer'],
            [['fromYear', 'toYear', 'date', 'created_time', 'updated_time'], 'safe'],
            [['note', 'teacherComment', 'principalApproval'], 'string'],
            [['class'], 'string', 'max' => 20],
            [['studyDepartment'], 'string', 'max' => 10],
            [['teacherName', 'upGradeType', 'principalName'], 'string', 'max' => 100],
            [['vocationalCertificate'], 'string', 'max' => 255],
            [['vocationalCertificateLevel'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'schoolReportID' => 'School Report ID',
            'class' => 'Class',
            'fromYear' => 'From Year',
            'toYear' => 'To Year',
            'studyDepartment' => 'Study Department',
            'note' => 'Note',
            'teacherName' => 'Teacher Name',
            'missedLesson' => 'Missed Lesson',
            'upGradeType' => 'Up Grade Type',
            'vocationalCertificate' => 'Vocational Certificate',
            'vocationalCertificateLevel' => 'Vocational Certificate Level',
            'teacherComment' => 'Teacher Comment',
            'principalApproval' => 'Principal Approval',
            'principalName' => 'Principal Name',
            'date' => 'Date',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
