<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Student;
use common\models\SchoolReport;
use common\models\Address;
use yii\db\Query;

class StudentSearch extends Student
{
    public $schoolReportNumber;
    public $currentAddress;
    public $schoolID;
    public $communeID;
    public $districtID;
    public $provinceID;

    public function rules()
    {
        return [
            [['id', 'gender', 'ethnicID'], 'integer'],
            [['name', 'image', 'birthday', 'fatherName', 'fatherJob', 'motherName', 'motherJob', 'tutorName', 'tutorJob', 'created_time', 'updated_time', 'schoolReportNumber', 'currentAddress', 'schoolID', 'communeID', 'districtID', 'provinceID'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {   
        $query = Student::find();
        $subQuery1 = SchoolReport::find()->select('studentID, number')->groupBy('studentID');
        $q1 = new Query;
        $subQuery2 = $q1->select(['tbl_student.id as studentID', 'detailAddress', 'tbl_commune.id as communeID', 'tbl_commune.name as communeName', 'tbl_district.id as districtID', 'tbl_district.name as districtName', 'tbl_province.id as provinceID', 'tbl_province.name as provinceName'])
            ->from(['tbl_address'])
            ->leftJoin('tbl_student', 'tbl_student.currentAddressID = tbl_address.id')
            ->leftJoin('tbl_commune', 'tbl_address.communeID = tbl_commune.id')
            ->leftJoin('tbl_district', 'tbl_address.districtID = tbl_district.id')
            ->leftJoin('tbl_province', 'tbl_district.provinceID = tbl_province.id')
            ->groupBy('studentID');
        $q2 = new Query;
        $subQuery3 = $q2->select(['std.id as studentID', 'tbl_school.id as schoolID'])
            ->from(['tbl_school'])
            ->leftJoin('tbl_year_evaluation', 'tbl_school.id = tbl_year_evaluation.schoolID')
            ->leftJoin('tbl_school_report', 'tbl_year_evaluation.schoolReportID = tbl_school_report.id')
            ->leftJoin('tbl_student std', 'std.id = tbl_school_report.studentID')
            ->groupBy('studentID');
        $query->leftJoin(['srNumber' => $subQuery1], 'srNumber.studentID = id');
        $query->leftJoin(['address' => $subQuery2], 'address.studentID = id');
        $query->leftJoin(['school' => $subQuery3], 'school.studentID = id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $dataProvider->setSort([
            'attributes' => [
                'id', 'name', 'birthday',
                'schoolReportNumber' => [
                    'asc' => ['srNumber.number' => SORT_ASC],
                    'desc' => ['srNumber.number' => SORT_DESC],
                    'label' => 'School Report Number',
                ],
                'currentAddress'=>[
                    'asc'=>['address.provinceName'=>SORT_ASC, 'address.districtName'=>SORT_ASC, 'address.communeName'=>SORT_ASC, 'address.detailAddress'=>SORT_ASC],
                    'desc'=>['address.provinceName'=>SORT_DESC, 'address.districtName'=>SORT_DESC, 'address.communeName'=>SORT_DESC, 'address.detailAddress'=>SORT_DESC],
                    'label'=>'Current Address',
                ],
            ]
        ]);

        if(!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'id' => $this->id,
            'gender' => $this->gender,
            'ethnicID' => $this->ethnicID,
            'school.schoolID' => $this->schoolID,
            'address.communeID' => $this->communeID,
            'address.districtID' => $this->districtID,
            'address.provinceID' => $this->provinceID,
        ]);

        $query->andFilterWhere(['like', 'fatherName', $this->fatherName])
            ->andFilterWhere(['like', 'fatherJob', $this->fatherJob])
            ->andFilterWhere(['like', 'motherName', $this->motherName])
            ->andFilterWhere(['like', 'motherJob', $this->motherJob])
            ->andFilterWhere(['like', 'tutorName', $this->tutorName])
            ->andFilterWhere(['like', 'tutorJob', $this->tutorJob])
            ->andFilterWhere(['like', 'DATE_FORMAT(birthday, "%d/%m/%Y")', $this->birthday])
            ->andFilterWhere(['like', 'srNumber.number', $this->schoolReportNumber]);

        $query->andWhere('address.detailAddress LIKE "%' . $this->currentAddress . '%" ' . 
                'OR address.communeName LIKE "%' . $this->currentAddress . '%" ' . 
                'OR address.districtName LIKE "%' . $this->currentAddress . '%" ' .
                'OR address.provinceName LIKE "%' . $this->currentAddress . '%"');
        if($this->name != null) {
            $query->andWhere('name REGEXP "[[:<:]]' . $this->name . '[[:>:]]"');
        }

        return $dataProvider;
    }
}
