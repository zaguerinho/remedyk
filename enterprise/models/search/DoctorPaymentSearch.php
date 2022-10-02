<?php

namespace enterprise\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DoctorPayment;

/**
 * DoctorPaymentSearch represents the model behind the search form about `common\models\DoctorPayment`.
 */
class DoctorPaymentSearch extends DoctorPayment
{
	public $r_filter;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'doctor_id', 'user_id', 'currency_id'], 'integer'],
            [['invoice_url', 'invoice_name', 'paid_on', 'notes', 'receipt_url', 'receipt_name'], 'safe'],
            [['amount'], 'number'],
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
        $query = DoctorPayment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'paid_on' => $this->paid_on,
            'status' => $this->status,
            'amount' => $this->amount,
            'doctor_id' => $this->doctor_id,
            'user_id' => $this->user_id,
            'currency_id' => $this->currency_id,
        ]);

        $query->andFilterWhere(['like', 'invoice_url', $this->invoice_url])
            ->andFilterWhere(['like', 'invoice_name', $this->invoice_name])
            ->andFilterWhere(['like', 'notes', $this->notes])
            ->andFilterWhere(['like', 'receipt_url', $this->receipt_url])
            ->andFilterWhere(['like', 'receipt_name', $this->receipt_name]);

            if(!empty($_REQUEST['DoctorPaymentSearch']) && !empty($_REQUEST['DoctorPaymentSearch']['r_filter'])){
            	$filter = trim($_REQUEST['DoctorPaymentSearch']['r_filter']);
            	$query->andWhere('CONCAT({{user}}.first_name, {{user}}.last_name, paid_on) ILIKE \'%'. $filter .'%\'');
            	
            	$query
            	->leftJoin('doctor', 'doctor.id = doctor_payment.doctor_id')
            	->leftJoin('{{user}}', '{{user}}.id = doctor.user_id');
            }
            
        return $dataProvider;
    }
}
