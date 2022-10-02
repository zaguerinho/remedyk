<?php
	
	namespace doctors\models\search;
	
	use Yii;
	use yii\base\Model;
	use yii\data\ActiveDataProvider;
	use common\models\Appointment;
	
	/**
	 * AppointmentSearch represents the model behind the search form about `common\models\Appointment`.
	 */
	class AppointmentSearch extends Appointment{
		
		/**
		 * R-Level filter
		 *
		 * @var string
		 */
		public $r_filter;
		
		/**
		 * @inheritdoc
		 */
		public function rules(){
			return [
				[
					[
						'id',
						'status',
						'patient_id',
						'doctor_id',
						'office_id',
						'operating_room_id',
						'procedure2doctor_id',
						'currency_id',
					],
					'integer',
				],
				[['is_procedure', 'is_done', 'is_active', 'is_quot'], 'boolean'],
				[['date', 'is_waiting', 'notes', 'confirmation_datetime', 'cancel_datetime'], 'safe'],
				[['price'], 'number'],
			];
		}
		
		/**
		 * @inheritdoc
		 */
		public function scenarios(){
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
		public function search($params){
			$query = Appointment::find();
			$user = Yii::$app->getUser()->identity;
			if($user->isDoctor()){
				$doctor = $user->doctor;
				$query->leftJoin('patient', '"patient".id="appointment".patient_id')
				->leftJoin('user', '"user".id=patient.user_id');
				$query->where(['appointment.doctor_id' => $doctor->id]);
			}
			elseif($user->isPatient()){
				$patient = $user->patient;
				$query->leftJoin('doctor', '"doctor".id="appointment".doctor_id')
				->leftJoin('user', '"user".id=doctor.user_id');
				$query->where(['appointment.patient_id' => $patient->id]);
			}
			$dataProvider = new ActiveDataProvider([
				'query' => $query,
			]);
			
			$this->load($params);
			
			if(!$this->validate()){
				// uncomment the following line if you do not want to return any records when validation fails
				// $query->where('0=1');
				return $dataProvider;
			}
			
			$query->andFilterWhere([
				'id'                    => $this->id,
				'is_procedure'          => $this->is_procedure,
				'date'                  => $this->date,
				'is_waiting'            => $this->is_waiting,
				'is_done'               => $this->is_done,
				'is_active'             => $this->is_active,
				'price'                 => $this->price,
				'status'                => $this->status,
				'confirmation_datetime' => $this->confirmation_datetime,
				'cancel_datetime'       => $this->cancel_datetime,
				'is_quot'               => $this->is_quot,
				'patient_id'            => $this->patient_id,
				'doctor_id'             => $this->doctor_id,
				'office_id'             => $this->office_id,
				'operating_room_id'     => $this->operating_room_id,
				'procedure2doctor_id'   => $this->procedure2doctor_id,
				'currency_id'           => $this->currency_id,
			])
				->andFilterWhere(['like', 'notes', $this->notes])
			;
			
			if(!empty($_REQUEST['AppointmentSearch']) && !empty($_REQUEST['AppointmentSearch']['r_filter'])){
				$filter = trim($_REQUEST['AppointmentSearch']['r_filter']);
				
				$query->andWhere('CONCAT(first_name,last_name,email, date) ILIKE \'%' . $filter . '%\'');
			
			
				
			}
			
			return $dataProvider;
		}
	}
