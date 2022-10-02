<?php
	
	namespace doctors\models\search;
	
	use common\models\User;
	use Yii;
	use yii\base\Model;
	use yii\data\ActiveDataProvider;
	use common\models\Patient;
	
	/**
	 * PatientSearch represents the model behind the search form about `common\models\Patient`.
	 */
	class PatientSearch extends Patient{
		
		/**
		 * @var string
		 */
		public $r_filter;
		
		/**
		 * @inheritdoc
		 */
		public function rules(){
			return [
				[['id', 'promo_points', 'user_id', 'address_id', 'tax_data_id', 'referred_by'], 'integer'],
				[['gender'], 'safe'],
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
			$query = Patient::find();
			
			$dataProvider = new ActiveDataProvider([
				'query' => $query,
			]);
			
			$user = User::getUserIdentity();
			if($user->isDoctor()){
				$doctor = $user->doctor;
				$query->innerJoin('appointment', 'appointment.patient_id = patient.id');
				$query->where(['appointment.doctor_id' => $doctor->id]);
			}
			
			$query->groupBy('patient.id');
			$this->load($params);
			
			if(!$this->validate()){
				// uncomment the following line if you do not want to return any records when validation fails
				// $query->where('0=1');
				return $dataProvider;
			}
			
			$query->andFilterWhere([
				'id'           => $this->id,
				'promo_points' => $this->promo_points,
				'user_id'      => $this->user_id,
				'address_id'   => $this->address_id,
				'tax_data_id'  => $this->tax_data_id,
				'referred_by'  => $this->referred_by,
			]);
			
			$query->andFilterWhere(['like', 'gender', $this->gender]);
			
			$classN = array_pop(explode('\\', self::className()));
			if(!empty($_REQUEST[$classN]) && !empty($_REQUEST[$classN]['r_filter'])){
				$filter = trim($_REQUEST[$classN]['r_filter']);
				
				$query->andWhere('CONCAT(username,"user".first_name,"user".last_name, 	"user".email) ILIKE \'%'
								 . $filter
								 . '%\'')
					->leftJoin('user', '"user".id=patient.user_id')
				;
			}
			
			return $dataProvider;
		}
	}
