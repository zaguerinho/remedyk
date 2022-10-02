<?php
	
	namespace common\models\search;
	
	use Yii;
	use yii\base\Exception;
	use yii\base\Model;
	use yii\data\ActiveDataProvider;
	use common\models\Doctor as DoctorModel;
	
	/**
	 * Doctor represents the model behind the search form about `common\models\Doctor`.
	 */
	class Doctor extends DoctorModel{
		public $r_filter;
		
		/**
		 * @inheritdoc
		 */
		public function rules(){
			return [
				[
					[
						'id',
						'bank_data',
						'appointment_anticipation',
						'user_id',
						'postal_address_id',
						'tax_data_id',
						'currency_id',
					],
					'integer',
				],
				[['license_number', 'resume', 'notes', 'gender', 'appointment_duration'], 'safe'],
				[['appointment_price'], 'number'],
			];
		}
		
		/**
		 * @inheritdoc
		 */
		public function scenarios(){
			// bypass scenarios() implementation in the parent class
			return Model::scenarios();
		}
		
		
		public function search($params){
			$query = DoctorModel::find();
			$user  = Yii::$app->getUser()->identity;
			/* @var $user \common\models\User */
			
			$dataProvider = new ActiveDataProvider([
				'query' => $query,
			]);
			
			$this->load($params);
			
			if(!$this->validate()){
				return $dataProvider;
			}
			
			if($user->isPatient()){
				$patient = $user->getPatient();
				if($patient)
					$patient = $patient->one();
				else
					throw new Exception(Yii::t('app', 'Related patient was not found'));
				/* @var $patient \common\models\Patient */
				$doctors    = $patient->getDoctors()
					->all()
				;
				$doctorsIds = [];
				
				if(!empty($doctors)){
					foreach($doctors as $doctor){
						/* @var $doctor \common\models\Doctor */
						$doctorsIds[] = $doctor->id;
					}
				}
				
				$query->andWhere(['IN', '"doctor".id', $doctorsIds]);
			}
			
			$query->andFilterWhere([
				'"doctor".id'              => $this->id,
				'bank_data'                => $this->bank_data,
				'appointment_price'        => $this->appointment_price,
				'appointment_anticipation' => $this->appointment_anticipation,
				'user_id'                  => $this->user_id,
				'postal_address_id'        => $this->postal_address_id,
				'tax_data_id'              => $this->tax_data_id,
				'currency_id'              => $this->currency_id,
				'appointment_duration'     => $this->appointment_duration,
			]);
			
			$query->andFilterWhere(['like', 'license_number', $this->license_number])
				->andFilterWhere(['like', 'resume', $this->resume])
				->andFilterWhere(['like', 'notes', $this->notes])
				->andFilterWhere(['like', 'gender', $this->gender])
			;
			
			$classN = array_pop(explode('\\', self::className()));
			if(!empty($_REQUEST[$classN]) && !empty($_REQUEST[$classN]['r_filter'])){
				$filter = trim($_REQUEST[$classN]['r_filter']);
				
				$query->andWhere('CONCAT("user".first_name,"user".last_name, resume) ILIKE \'%' . $filter . '%\'')
					->leftJoin('user', '"user".id="doctor".user_id')
				;
			}
			
			return $dataProvider;
		}
	}
