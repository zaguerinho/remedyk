<?php
	
	namespace common\models;
	
	use Yii;
	
	/**
	 * This is the model class for table "prescription".
	 *
	 * @property integer              $id
	 * @property string               $datetime
	 * @property string               $notes
	 * @property boolean              $is_active
	 * @property integer              $patient_id
	 * @property integer              $doctor_id
	 * @property integer              $appointment_id
	 *
	 * @property Appointment          $appointment
	 * @property Doctor               $doctor
	 * @property Patient              $patient
	 * @property PrescriptionDetail[] $prescriptionDetails
	 */
	class Prescription extends \common\models\BaseModel{
		/**
		 * @inheritdoc
		 */
		public static function tableName(){
			return 'prescription';
		}
		
		/**
		 * @inheritdoc
		 */
		public function rules(){
			return [
				[['datetime'], 'safe'],
				[['notes'], 'string'],
				[['is_active'], 'boolean'],
				[['patient_id', 'doctor_id', 'appointment_id'], 'integer'],
				[['patient_id'], 'required'],
				[
					['appointment_id'],
					'exist',
					'skipOnError'     => true,
					'targetClass'     => Appointment::className(),
					'targetAttribute' => ['appointment_id' => 'id'],
				],
				[
					['doctor_id'],
					'exist',
					'skipOnError'     => true,
					'targetClass'     => Doctor::className(),
					'targetAttribute' => ['doctor_id' => 'id'],
				],
				[
					['patient_id'],
					'exist',
					'skipOnError'     => true,
					'targetClass'     => Patient::className(),
					'targetAttribute' => ['patient_id' => 'id'],
				],
			];
		}
		
		/**
		 * @inheritdoc
		 */
		public function attributeLabels(){
			return [
				'id'             => Yii::t('app', 'ID'),
				'datetime'       => Yii::t('app', 'Date and Time'),
				'notes'          => Yii::t('app', 'Notes'),
				'is_active'      => Yii::t('app', 'Is Active'),
				'patient_id'     => Yii::t('app', 'Patient'),
				'doctor_id'      => Yii::t('app', 'Doctor'),
				'appointment_id' => Yii::t('app', 'Appointment'),
			];
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAppointment(){
			return $this->hasOne(Appointment::className(), ['id' => 'appointment_id'])
				->inverseOf('prescriptions')
				;
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getDoctor(){
			return $this->hasOne(Doctor::className(), ['id' => 'doctor_id'])
				->inverseOf('prescriptions')
				;
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getPatient(){
			return $this->hasOne(Patient::className(), ['id' => 'patient_id'])
				->inverseOf('prescriptions')
				;
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getPrescriptionDetails(){
			return $this->hasMany(PrescriptionDetail::className(), ['prescription_id' => 'id'])
				->inverseOf('prescription')
				;
		}
	}
