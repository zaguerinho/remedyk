<?php
	
	namespace common\models;
	
	use Yii;
	
	/**
	 * This is the model class for table "patient".
	 *
	 * @property integer         $id
	 * @property string          $gender
	 * @property integer         $promo_points
	 * @property integer         $user_id
	 * @property integer         $address_id
	 * @property integer         $tax_data_id
	 * @property integer         $referred_by
	 * @property integer         $age
	 * @property string          $blood_type
	 * @property string          $height
	 * @property string          $weight
	 *
	 * @property Appointment[]   $appointments
	 * @property ClinicalStory[] $clinicalStories
	 * @property Address         $address
	 * @property TaxData         $taxData
	 * @property User            $user
	 * @property User            $referredBy
	 * @property Prescription[]  $prescriptions
	 * @property Qualification[] $qualifications
	 * @property SearchHistory[] $searchHistories
	 * @property Doctor[]        $doctors
	 */
	class Patient extends \common\models\BaseModel{
		/**
		 * @inheritdoc
		 */
		public static function tableName(){
			return 'patient';
		}
		
		/**
		 * @inheritdoc
		 */
		public function rules(){
			return [
				[['promo_points', 'user_id', 'address_id', 'tax_data_id', 'referred_by', 'age'], 'integer'],
				[['height', 'weight'], 'number'],
				[['gender', 'blood_type'], 'string', 'max' => 255],
				[
					['address_id'],
					'exist',
					'skipOnError'     => true,
					'targetClass'     => Address::className(),
					'targetAttribute' => ['address_id' => 'id'],
				],
				[
					['tax_data_id'],
					'exist',
					'skipOnError'     => true,
					'targetClass'     => TaxData::className(),
					'targetAttribute' => ['tax_data_id' => 'id'],
				],
				[
					['user_id'],
					'exist',
					'skipOnError'     => true,
					'targetClass'     => User::className(),
					'targetAttribute' => ['user_id' => 'id'],
				],
				[
					['referred_by'],
					'exist',
					'skipOnError'     => true,
					'targetClass'     => User::className(),
					'targetAttribute' => ['referred_by' => 'id'],
				],
			];
		}
		
		/**
		 * @inheritdoc
		 */
		public function attributeLabels(){
			return [
				'id'           => Yii::t('app', 'ID'),
				'gender'       => Yii::t('app', 'Sex'),
				'promo_points' => Yii::t('app', 'Promo Points'),
				'user_id'      => Yii::t('app', 'User'),
				'address_id'   => Yii::t('app', 'Address'),
				'tax_data_id'  => Yii::t('app', 'Tax Data'),
				'referred_by'  => Yii::t('app', 'Referred By'),
				'age'          => Yii::t('app', 'Age'),
				'blood_type'   => Yii::t('app', 'Blood Type'),
				'height'       => Yii::t('app', 'Height'),
				'weight'       => Yii::t('app', 'Weight'),
			];
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAppointments(){
			return $this->hasMany(Appointment::className(), ['patient_id' => 'id'])
				->inverseOf('patient')
				;
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getDoctors(){
			return $this->hasMany(Doctor::className(), ['id' => 'doctor_id'])
				->viaTable('appointment', ['patient_id' => 'id'])
				;
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getClinicalStories(){
			return $this->hasMany(ClinicalStory::className(), ['patient_id' => 'id'])
				->inverseOf('patient')
				;
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAddress(){
			return $this->hasOne(Address::className(), ['id' => 'address_id'])
				->inverseOf('patients')
				;
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getTaxData(){
			return $this->hasOne(TaxData::className(), ['id' => 'tax_data_id'])
				->inverseOf('patients')
				;
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser(){
			return $this->hasOne(User::className(), ['id' => 'user_id'])
				->inverseOf('patient')
				;
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getReferredBy(){
			return $this->hasOne(User::className(), ['id' => 'referred_by'])
				->inverseOf('patients0')
				;
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getPrescriptions(){
			return $this->hasMany(Prescription::className(), ['patient_id' => 'id'])
				->inverseOf('patient')
				;
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getQualifications(){
			return $this->hasMany(Qualification::className(), ['patient_id' => 'id'])
				->inverseOf('patient')
				;
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getSearchHistories(){
			return $this->hasMany(SearchHistory::className(), ['patient_id' => 'id'])
				->inverseOf('patient')
				;
		}
		
		public function getPicture(){
			$patientDomain = Yii::$app->params['patientsDomain'];
			$picture       = $this->user->picture
				? $patientDomain . $this->user->picture : $patientDomain
														  . '/images/patient_default.png';
			
			return $picture;
		}
		
		public function setPicture(){
			return $this->user->setProfilePicture();
		}
		
		public function getAppointmentStatus($user_id = null){			
			$appointment = $this->getLastAppointment($user_id);
			
			if(!$appointment)
				return false;
			
			return $appointment->status;
		}
		
		/**
		 * Gets the next appointment that have not occur yet with the specified doctor
		 * (If there is a current open appointment or there is no active appointment, a new appointment will be
		 * returned)
		 *
		 * @param integer|null $user_id (optional) The user id of the doctor. If not defined, the user will be the
		 *                              current user
		 *
		 * @return \common\models\Appointment|\yii\db\ActiveRecord|array|NULL
		 */
		public function getNextAppointment($user_id = null){
			$appointment = $this->getLastAppointment($user_id);
			if(!$appointment)
				return new Appointment();
			if($appointment->status == Appointment::STATUS_CANCELLED
			   || $appointment->status == Appointment::STATUS_OPEN
			   || $appointment->status == Appointment::STATUS_CLOSED)
				return new Appointment();
			
			return $appointment;
		}
		
		/**
		 * Gets the currentliy open appointment between this patient and the specified doctor
		 * (returns false in the case there is no open appointment)
		 *
		 * @param integer|null $user_id (optional) The user id of the doctor. If not defined, the user will be the
		 *                              current user
		 *
		 * @return boolean|\yii\db\ActiveRecord|array|NULL
		 */
		public function getOpenAppointment($user_id = null){
			if($user_id == null)
				$user = Yii::$app->user->identity;
			else
				$user = User::findIdentity($user_id);
			$doctor = $user->doctor;
			
			$appointment = Appointment::find()
				->where(['doctor_id' => $doctor->id, 'patient_id' => $this->id, 'status' => Appointment::STATUS_OPEN])
				->orderBy('date desc')
				->one()
			;
			if(!$appointment)
				return false;
			
			return $appointment;
		}
		
		public function isPatientOf($user_id=null){
			$user = User::findIdentity($user_id);
			$doctor = $user->doctor;
			if (!$doctor)
				return false;
			
			$appointment = Appointment::find()->where(['patient_id' => $this->id, 'doctor_id' => $doctor->id, 'status' => [Appointment::STATUS_OPEN, Appointment::STATUS_CLOSED]])->one();
			return ($appointment != null);
		}
		
		private function getLastAppointment($user_id = null){
			if($user_id == null)
				$user = Yii::$app->user->identity;
			else
				$user = User::findIdentity($user_id);
			/* @var $user \common\models\User */
			
			if (!$user->isDoctor()){
				return null;
			}
				
			$where = ['patient_id' => $this->id];
			if($user->isDoctor()){
				$where['doctor_id'] = $user->doctor->id;
			}
			
			$appointment = Appointment::find()
				->where($where)
				->orderBy('date desc')
				->one()
			;
			
			return $appointment;
		}
		
		/**
		 * @return \common\models\Address
		 */
		public function getAddressOrCreateOne(){
			if(!$this->address)
				return new Address();
			
			return $this->address;
		}
		
		public function getRating($doctor_id){
			$result = Qualification::find()->where(['doctor_id' => $doctor_id, 'patient_id' => $this->id])->one();
			if (!$result){
				return 0;
			}
			return $result->rate;
		}
		public function setRating($doctor_id, $value){
			$qualification = Qualification::find()->where(['doctor_id' => $doctor_id, 'patient_id' => $this->id])->one();
			if (!$qualification){
				$qualification = new Qualification(['doctor_id' => $doctor_id, 'patient_id' => $this->id, 'is_active' => true]);
			}
			$qualification->rate = $value;
			return $qualification->save();
		}
	}
