<?php
	
	namespace common\models;
	
	use Yii;
	use yii\helpers\Json;
	
	/**
	 * This is the model class for table "medicine".
	 *
	 * @property integer              $id
	 * @property string               $stores_equivalent_ids
	 * @property string               $name
	 * @property boolean              $is_active
	 *
	 * @property PrescriptionDetail[] $prescriptionDetails
	 */
	class Medicine extends \common\models\BaseModel{
		
		
		/**
		 * @var string
		 */
		public $spanishName;
		
		/* @var string */
		public $englishName;
		
		
		/**
		 * @var string
		 */
		public $spanishStoreEq;
		
		/* @var string */
		public $englishStoreEq;
		
		
		public function getSpanishName(){
			$data = Json::decode($this->name, true);
			
			return !empty($data['es']) ? $data['es'] : '';
		}
		
		public function getEnglishName(){
			$data = Json::decode($this->name, true);
			
			return !empty($data['en']) ? $data['en'] : '';
		}
		
		public function getSpanishStoreEq(){
			$data = Json::decode($this->stores_equivalent_ids, true);
			
			return !empty($data['es']) ? $data['es'] : '';
		}
		
		public function getEnglishStoreEq(){
			$data = Json::decode($this->stores_equivalent_ids, true);
			
			return !empty($data['en']) ? $data['en'] : '';
		}
		
		public function beforeSave($insert){
			$this->name = json_encode([
				'es' => (string)$this->spanishName,
				'en' => (string)$this->englishName,
			]);
			
			$this->stores_equivalent_ids = json_encode([
				'es' => (string)$this->spanishStoreEq,
				'en' => (string)$this->englishStoreEq,
			]);
			
			
			return parent::beforeSave($insert);
		}
		
		
		/**
		 * @inheritdoc
		 */
		public static function tableName(){
			return 'medicine';
		}
		
		/**
		 * @inheritdoc
		 */
		public function rules(){
			return [
				[['stores_equivalent_ids', 'name'], 'string'],
				[['is_active'], 'boolean'],
				[
					[
						'spanishName',
						'englishName',
						'spanishStoreEq',
						'englishStoreEq',
					],
					'safe',
				],
			];
		}
		
		/**
		 * @inheritdoc
		 */
		public function attributeLabels(){
			return [
				'id'                    => Yii::t('app', 'ID'),
				'stores_equivalent_ids' => Yii::t('app', 'Stores Equivalent Ids'),
				'name'                  => Yii::t('app', 'Name'),
				'is_active'             => Yii::t('app', 'Is Active'),
				'spanishName'           => Yii::t('app', 'Spanish Name'),
				'englishName'           => Yii::t('app', 'English Name'),
				'spanishStoreEq'        => Yii::t('app', 'Stores Equivalent Ids in Spanish'),
				'englishStoreEq'        => Yii::t('app', 'Stores Equivalent Ids in English'),
			];
		}
		
		public function getPrescriptionDetails(){
			return $this->hasMany(PrescriptionDetail::className(), ['medicine_id' => 'id'])
				->inverseOf('medicine')
				;
		}
		
		public function getLocalized_name(){
			return Json::decode($this->name, true)[Yii::$app->language];
		}
		
		public function getLocalized_storesIds(){
			return Json::decode($this->stores_equivalent_ids, true)[Yii::$app->language];
		}
	}
