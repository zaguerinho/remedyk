<?php
	
	namespace common\models;
	
	use Yii;
	use yii\helpers\Json;
	
	/**
	 * This is the model class for table "specialty".
	 *
	 * @property integer               $id
	 * @property string                $name
	 * @property string                $specialist_name
	 * @property boolean               $is_active
	 *
	 * @property Procedure2specialty[] $procedure2specialties
	 * @property Specialty2doctor[]    $specialty2doctors
	 * @property Procedure[]           $procedures
	 * @property Procedure2Doctors[]   $procedure2Doctors
	 * @property string                $localized_name
	 * @property string                $localized_specialist_name
	 */
	class Specialty extends \common\models\BaseModel{
		
		/**
		 * @var string
		 */
		public $spanishName;
		
		/* @var string */
		public $spanishSpecialistName;
		/**
		 * @var string
		 */
		public $englishName;
		
		/* @var string */
		public $englishSpecialistName;
		
		/**
		 * @inheritdoc
		 */
		public static function tableName(){
			return 'specialty';
		}
		
		/**
		 * @inheritdoc
		 */
		public function rules(){
			return [
				[['name'], 'string'],
				[['is_active'], 'boolean'],
				[
					[
						'spanishName',
						'spanishSpecialistName',
						'englishName',
						'englishSpecialistName',
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
				'name'                  => Yii::t('app', 'Name'),
				'is_active'             => Yii::t('app', 'Is Active'),
				'specialist_name'       => Yii::t('app', 'Specialist Name'),
				'spanishName'           => Yii::t('app', 'Spanish Name'),
				'spanishSpecialistName' => Yii::t('app', 'Spanish Specialist Name'),
				'englishName'           => Yii::t('app', 'English Name'),
				'englishSpecialistName' => Yii::t('app', 'English Specialist Name'),
			];
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getProcedure2specialties(){
			return $this->hasMany(Procedure2specialty::className(), ['specialty_id' => 'id'])
				->inverseOf('specialty')
				;
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getSpecialty2doctors(){
			return $this->hasMany(Specialty2doctor::className(), ['specialty_id' => 'id'])
				->inverseOf('specialty')
				;
		}
		
		/**
		 *
		 * @return \yii\db\ActiveQuery
		 */
		public function getProcedures(){
			return $this->hasMany(Procedure::className(), ['id' => 'procedure_id'])
				->viaTable('procedure2specialty', ['specialty_id' => 'id'])
				;
		}
		
		public function getProcedure2doctors(){
			return $this->hasMany(Procedure2doctor::className(), ['specialty_id' => 'id'])
				->inverseOf('specialty')
				;
		}
		
		public function getLocalized_name(){
			return Json::decode($this->name, true)[Yii::$app->language];
		}
		
		public function beforeSave($insert){
			$this->name = json_encode([
				'es' => (string)$this->spanishName,
				'en' => (string)$this->englishName,
			]);
			
			$this->specialist_name = json_encode([
				'es' => (string)$this->spanishSpecialistName,
				'en' => (string)$this->englishSpecialistName,
			]);
			
			
			return parent::beforeSave($insert);
		}
		
		
		public function getSpanishName(){
			$data = Json::decode($this->name, true);
			
			return !empty($data['es']) ? $data['es'] : '';
		}
		
		public function getEnglishName(){
			$data = Json::decode($this->name, true);
			
			return !empty($data['en']) ? $data['es'] : '';
		}
		
		public function getSpanishSpecialistName(){
			$data = Json::decode($this->specialist_name, true);
			
			return !empty($data['es']) ? $data['es'] : '';
		}
		
		public function getEnglishSpecialistName(){
			$data = Json::decode($this->specialist_name, true);
			
			return !empty($data['en']) ? $data['en'] : '';
		}
		
		public function getLocalized_specialist_name(){
			return Json::decode($this->specialist_name, true)[Yii::$app->language];
		}
	}
