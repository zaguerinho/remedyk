<?php

namespace enterprise\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Commission;

/**
 * CommissionSearch represents the model behind the search form about `common\models\Commission`.
 */
class CommissionSearch extends Commission
{
	
	public $r_filter;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'appointment_id', 'status', 'doctor_payment_id', 'payment_method_id'], 'integer'],
            [['amount', 'percent'], 'number'],
            [['paid_on', 'conekta_order_id'], 'safe'],
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
        $query = Commission::find();

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
            'appointment_id' => $this->appointment_id,
            'amount' => $this->amount,
            'percent' => $this->percent,
            'paid_on' => $this->paid_on,
            'status' => $this->status,
            'doctor_payment_id' => $this->doctor_payment_id,
            'payment_method_id' => $this->payment_method_id,
        ]);

        $query->andFilterWhere(['like', 'conekta_order_id', $this->conekta_order_id]);
        
        if(!empty($_REQUEST['CommissionSearch']) && !empty($_REQUEST['CommissionSearch']['r_filter'])){
        	//Doctor name or Patient name
        	$filter = trim($_REQUEST['CommissionSearch']['r_filter']);
        	$query->andWhere('CONCAT(user1.first_name, user1.last_name, user2.first_name, user2.last_name) ILIKE \'%'. $filter .'%\'');
        	$query
        	->leftJoin('appointment', 'appointment.id = commission.appointment_id')
        	->leftJoin('doctor', 'doctor.id = appointment.doctor_id')
        	->leftJoin('patient', 'patient.id = appointment.patient_id')
        	->leftJoin(['user1' => 'user'], 'user1.id = doctor.user_id')
        	->leftJoin(['user2' => 'user'], 'user2.id = patient.user_id');
        }

        return $dataProvider;
    }
}
