<?php
	
	namespace doctors\models\search;
	
	use common\models\User;
	use Yii;
	use yii\base\Model;
	use yii\data\ActiveDataProvider;
	use common\models\Commission;
	
	/**
	 * CommissionSearch represents the model behind the search form about `common\models\Commission`.
	 */
	class CommissionSearch extends Commission{
		
		/**
		 * @var string
		 */
		public $r_filter;
		
		/**
		 * @inheritdoc
		 */
		public function rules(){
			return [
				[['id', 'appointment_id', 'status'], 'integer'],
				[['amount', 'percent'], 'number'],
				[['paid_on'], 'safe'],
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
			$query = Commission::find();
			$query->innerJoin('appointment', 'appointment.id = commission.appointment_id');
			
			$user = User::getUserIdentity();
			
			if($user->isDoctor()){
				$doctor = $user->doctor;
				$query->where(['appointment.doctor_id' => $doctor->id]);
			}
			elseif($user->isPatient()){
				$patient = $user->patient;
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
				'id'             => $this->id,
				'appointment_id' => $this->appointment_id,
				'amount'         => $this->amount,
				'percent'        => $this->percent,
				'paid_on'        => $this->paid_on,
				'status'         => $this->status,
			]);
			
			$classN = array_pop(explode('\\', self::className()));
			if(!empty($_REQUEST[$classN]) && !empty($_REQUEST[$classN]['r_filter'])){
				$filter = trim($_REQUEST[$classN]['r_filter']);
				
				$query->andWhere('CONCAT("commission".id,appointment_id,amount,percent,paid_on) ILIKE \'%'
								 . $filter
								 . '%\'');
			}
			
			return $dataProvider;
		}
	}
