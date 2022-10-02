<?php
	
	namespace common\models;
	
	use Yii;
	use yii\helpers\Json;
	
	/**
	 * This is the model class for table "procedure".
	 *
	 * @property integer               $id
	 * @property string                $name
	 * @property boolean               $is_treatment
	 * @property boolean               $is_surgery
	 * @property boolean               $is_active
	 *
	 * @property Procedure2doctor[]    $procedure2doctors
	 * @property Procedure2specialty[] $procedure2specialties
	 * @property string                $localized_name
	 */
	class Procedure extends \common\models\BaseModel{
		
		
		/**
		 * @var string
		 */
		public $spanishName;
		
		/* @var string */
		public $englishName;
		
		
		public function getSpanishName(){
			$data = Json::decode($this->name, true);
			
			return !empty($data['es']) ? $data['es'] : '';
		}
		
		public function getEnglishName(){
			$data = Json::decode($this->name, true);
			
			return !empty($data['en']) ? $data['es'] : '';
		}
		
		public function beforeSave($insert){
			$this->name = json_encode([
				'es' => (string)$this->spanishName,
				'en' => (string)$this->englishName,
			]);
			
			return parent::beforeSave($insert);
		}
		
		/**
		 * @inheritdoc
		 */
		public static function tableName(){
			return 'procedure';
		}
		
		/**
		 * @inheritdoc
		 */
		public function rules(){
			return [
				[['name'], 'string'],
				[['is_treatment', 'is_surgery', 'is_active'], 'boolean'],
				[
					[
						'spanishName',
						'englishName',
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
				'id'           => Yii::t('app', 'ID'),
				'name'         => Yii::t('app', 'Name'),
				'is_treatment' => Yii::t('app', 'Is Treatment'),
				'is_surgery'   => Yii::t('app', 'Is Surgery'),
				'is_active'    => Yii::t('app', 'Is Active'),
				'spanishName'  => Yii::t('app', 'Spanish Name'),
				'englishName'  => Yii::t('app', 'English Name'),
			];
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getProcedure2doctors(){
			return $this->hasMany(Procedure2doctor::className(), ['procedure_id' => 'id'])
				->inverseOf('procedure')
				;
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getProcedure2specialties(){
			return $this->hasMany(Procedure2specialty::className(), ['procedure_id' => 'id'])
				->inverseOf('procedure')
				;
		}
		
		public function getLocalized_name(){
			return Json::decode($this->name, true)[Yii::$app->language];
		}
		
	}
