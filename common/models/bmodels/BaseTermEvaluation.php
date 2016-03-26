<?php

namespace common\models\bmodels;

use Yii;

/**
 * This is the model class for table "{{%term_evaluation}}".
 *
 * @property integer $id
 * @property integer $yearEvaluationID
 * @property integer $term
 * @property string $learnCapacity
 * @property string $conduct
 * @property string $created_time
 * @property string $updated_time
 */
class BaseTermEvaluation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%term_evaluation}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['yearEvaluationID', 'term', 'learnCapacity', 'conduct', 'created_time', 'updated_time'], 'required'],
            [['yearEvaluationID', 'term'], 'integer'],
            [['created_time', 'updated_time'], 'safe'],
            [['learnCapacity', 'conduct'], 'string', 'max' => 20],
            [['yearEvaluationID', 'term'], 'unique', 'targetAttribute' => ['yearEvaluationID', 'term'], 'message' => 'The combination of Year Evaluation ID and Term has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'yearEvaluationID' => 'Year Evaluation ID',
            'term' => 'Term',
            'learnCapacity' => 'Learn Capacity',
            'conduct' => 'Conduct',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
