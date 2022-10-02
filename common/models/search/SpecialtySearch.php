<?php
	
	namespace common\models\search;
	
	use Yii;
	use yii\base\Model;
	use yii\data\ActiveDataProvider;
	use common\models\Specialty;
	
	/**
	 * SpecialtySearch represents the model behind the search form about `common\models\Specialty`.
	 */
	class SpecialtySearch extends Specialty{
		
		/**
		 * @var string
		 */
		public $r_filter;
		
		/**
		 * @inheritdoc
		 */
		public function rules(){
			return [
				[['id'], 'integer'],
				[['name', 'specialist_name'], 'safe'],
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
			$query = Specialty::find();
			
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
				'id'        => $this->id,
				'is_active' => $this->is_active,
			]);
			
			$query->andFilterWhere(['like', 'name', $this->name])
				->andFilterWhere(['like', 'specialist_name', $this->specialist_name])
			;
			
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
