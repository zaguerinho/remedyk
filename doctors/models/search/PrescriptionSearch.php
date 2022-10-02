<?php
	
	namespace doctors\models\search;
	
	use Yii;
	use yii\base\Model;
	use yii\data\ActiveDataProvider;
use common\models\Prescription;
use common\models\User;
	
	/**
	 * PrescriptionSearch represents the model behind the search form about `common\models\Prescription`.
	 */
	class PrescriptionSearch extends Prescription{
		
		public $r_filter;
		
		/**
		 * @inheritdoc
		 */
		public function rules(){
			return [
				[['id', 'patient_id', 'doctor_id', 'appointment_id'], 'integer'],
				[['datetime', 'notes'], 'safe'],
				[['is_active'], 'boolean'],
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
			$query = Prescription::find();
			
			$dataProvider = new ActiveDataProvider([
				'query' => $query,
			]);
			
			$this->load($params);
			
			$user = User::getUserIdentity();
			if ($user->isDoctor()){
				$doctor = $user->doctor;
				$query->where(['prescription.doctor_id' => $doctor->id]);
			}
			
			if(!$this->validate()){
				// uncomment the following line if you do not want to return any records when validation fails
				// $query->where('0=1');
				return $dataProvider;
			}
			
			$query->andFilterWhere([
				'id'             => $this->id,
				'datetime'       => $this->datetime,
				'is_active'      => $this->is_active,
				'patient_id'     => $this->patient_id,
				'doctor_id'      => $this->doctor_id,
				'appointment_id' => $this->appointment_id,
			]);
			
			$query->andFilterWhere(['like', 'notes', $this->notes]);
			
			
			$classN = array_pop(explode('\\', self::className()));
			if(!empty($_REQUEST[$classN]) && !empty($_REQUEST[$classN]['r_filter'])){
				$filter = trim($_REQUEST[$classN]['r_filter']);
				
				$query->andWhere('CONCAT("prescription".id,notes,"user".first_name,"user".last_name) ILIKE \'%'
								 . $filter
								 . '%\'')
					->leftJoin('patient', '"patient".id="prescription".patient_id')
					->leftJoin('user', '"user".id=patient.user_id')
				;
			}
			
			return $dataProvider;
		}
	}
