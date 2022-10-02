<?php
	
	namespace doctors\models\search;
	
	use Yii;
	use yii\base\Model;
	use yii\data\ActiveDataProvider;
	use common\models\DoctorPayment;
	
	/**
	 * DoctorPaymentSearch represents the model behind the search form about `common\models\DoctorPayment`.
	 */
	class DoctorPaymentSearch extends DoctorPayment{
		
		/**
		 * @var string
		 */
		public $r_filter;
		
		/**
		 * @inheritdoc
		 */
		public function rules(){
			return [
				[['id', 'status', 'doctor_id', 'user_id'], 'integer'],
				[['invoice_url', 'invoice_name', 'paid_on', 'notes', 'receipt_url', 'receipt_name'], 'safe'],
				[['amount'], 'number'],
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
			$doctor = Yii::$app->user->identity->doctor;
			$query  = DoctorPayment::find();
			$query->where(['"doctor_payment".doctor_id' => $doctor->id]);
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
				'id'                         => $this->id,
				'paid_on'                    => $this->paid_on,
				'status'                     => $this->status,
				'amount'                     => $this->amount,
				'"doctor_payment".doctor_id' => $this->doctor_id,
				'user_id'                    => $this->user_id,
			]);
			
			$query->andFilterWhere(['like', 'invoice_url', $this->invoice_url])
				->andFilterWhere(['like', 'invoice_name', $this->invoice_name])
				->andFilterWhere(['like', '"doctor_payment".notes', $this->notes])
				->andFilterWhere(['like', 'receipt_url', $this->receipt_url])
				->andFilterWhere(['like', 'receipt_name', $this->receipt_name])
			;
			
			
			$classN = array_pop(explode('\\', self::className()));
			if(!empty($_REQUEST[$classN]) && !empty($_REQUEST[$classN]['r_filter'])){
				$filter = trim($_REQUEST[$classN]['r_filter']);
				
				$query->andWhere('CONCAT("doctor_payment".id,invoice_name,"doctor_payment".notes,receipt_url,"doctor_payment".amount,"user".first_name,"user".last_name) ILIKE \'%'
								 . $filter
								 . '%\'')
					->leftJoin('user', '"user".id="doctor_payment".user_id')
				;
			}
			
			return $dataProvider;
		}
	}
