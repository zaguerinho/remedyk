<?php
	
	namespace common\models\search;
	
	use Yii;
	use yii\base\Model;
	use yii\data\ActiveDataProvider;
	use common\models\Procedure;
	
	/**
	 * ProceduresSearch represents the model behind the search form about `common\models\Procedure`.
	 */
	class ProceduresSearch extends Procedure{
		
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
				[['name'], 'safe'],
				[['is_treatment', 'is_surgery', 'is_active'], 'boolean'],
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
			$query = Procedure::find();
			
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
				'id'           => $this->id,
				'is_treatment' => $this->is_treatment,
				'is_surgery'   => $this->is_surgery,
				'is_active'    => $this->is_active,
			]);
			
			$query->andFilterWhere(['like', 'name', $this->name]);
			
			return $dataProvider;
		}
	}
