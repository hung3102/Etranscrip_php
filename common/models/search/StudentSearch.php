<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Student;

class StudentSearch extends Student
{
    public $schoolReport;
    public $currentAddress;
    public $nativeAddress;

    public function rules()
    {
        return [
            [['id', 'gender', 'currentAddressID', 'nativeAddressID', 'ethnicID'], 'integer'],
            [['name', 'imageURL', 'birthday', 'fatherName', 'fatherJob', 'motherName', 'motherJob', 'tutorName', 'tutorJob', 'created_time', 'updated_time', 'schoolReport', 'currentAddress', 'nativeAddress'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {   
        $query = Student::find();

        // add conditions that should always apply here
        $query->joinWith(['schoolReport']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['schoolReport'] = [
            'asc' => ['tbl_school_report.number' => SORT_ASC],
            'desc' => ['tbl_school_report.number' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'gender' => $this->gender,
            // 'birthday' => $this->birthday,
            'currentAddressID' => $this->currentAddressID,
            'nativeAddressID' => $this->nativeAddressID,
            'ethnicID' => $this->ethnicID,
            'created_time' => $this->created_time,
            'updated_time' => $this->updated_time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'imageURL', $this->imageURL])
            ->andFilterWhere(['like', 'fatherName', $this->fatherName])
            ->andFilterWhere(['like', 'fatherJob', $this->fatherJob])
            ->andFilterWhere(['like', 'motherName', $this->motherName])
            ->andFilterWhere(['like', 'motherJob', $this->motherJob])
            ->andFilterWhere(['like', 'tutorName', $this->tutorName])
            ->andFilterWhere(['like', 'tutorJob', $this->tutorJob])
            ->andFilterWhere(['like', 'birthday', $this->birthday])
            ->andFilterWhere(['like', 'tbl_school_report.number', $this->schoolReport]);

        // $query->andFilterWhere(['like', 'currentAddressID', $this->currentAddress->getFullAddress()]);

        return $dataProvider;
    }
}
