<?php

namespace enterprise\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Appointment;

/**
 * AppointmentSearch represents the model behind the search form about `common\models\Appointment`.
 */
class AppointmentSearch extends Appointment
{
	public $r_filter;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'patient_id', 'doctor_id', 'office_id', 'operating_room_id', 'procedure2doctor_id', 'currency_id', 'changed_by'], 'integer'],
            [['is_procedure', 'is_done', 'is_active', 'is_quot'], 'boolean'],
            [['date', 'is_waiting', 'notes', 'confirmation_datetime', 'cancel_datetime', 'start_time', 'end_time', 'created_at'], 'safe'],
            [['price'], 'number'],
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
        $query = Appointment::find();

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
            'is_procedure' => $this->is_procedure,
            'date' => $this->date,
            'is_waiting' => $this->is_waiting,
            'is_done' => $this->is_done,
            'is_active' => $this->is_active,
            'price' => $this->price,
            'status' => $this->status,
            'confirmation_datetime' => $this->confirmation_datetime,
            'cancel_datetime' => $this->cancel_datetime,
            'is_quot' => $this->is_quot,
            'patient_id' => $this->patient_id,
            'doctor_id' => $this->doctor_id,
            'office_id' => $this->office_id,
            'operating_room_id' => $this->operating_room_id,
            'procedure2doctor_id' => $this->procedure2doctor_id,
            'currency_id' => $this->currency_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'changed_by' => $this->changed_by,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'notes', $this->notes]);
        
        
        if(!empty($_REQUEST['AppointmentSearch']) && !empty($_REQUEST['AppointmentSearch']['r_filter'])){
        	//Doctor name or Patient name
        	$filter = trim($_REQUEST['AppointmentSearch']['r_filter']);
        	
        	$query->andWhere('CONCAT(user1.first_name, user1.last_name, user2.first_name, user2.last_name, appointment.id, appointment.date) ILIKE \'%'. $filter .'%\'');
        	$query
        	->leftJoin(['d' => 'doctor'], 'd.id = appointment.doctor_id')
        	->leftJoin(['p' => 'patient'], 'p.id = appointment.patient_id')
        	->leftJoin(['user1' => 'user'], 'user1.id = d.user_id')
        	->leftJoin(['user2' => 'user'], 'user2.id = p.user_id');
        }

        return $dataProvider;
    }
}
