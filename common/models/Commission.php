<?php
	
	namespace common\models;
	
	use Yii;
	
	/**
	 * This is the model class for table "commission".
	 *
	 * @property integer       $id
	 * @property integer       $appointment_id
	 * @property integer	   $payment_method_id
	 * @property string        $amount
	 * @property string        $percent
	 * @property string        $paid_on
	 * @property integer       $status
	 * @property string		   $conekta_order_id
	 * @property integer       $doctor_payment_id
	 *
	 * @property Appointment   $appointment
	 * @property DoctorPayment $doctorPayment
	 * @property PaymentMethod $paymentMethod
	 */
	class Commission extends \yii\db\ActiveRecord{
		const STATUS_PENDING_APPOINTMENT = 1;
		const STATUS_INVOICE_REQUEST     = 2;
		const STATUS_PENDING_PAYMENT     = 3;
		const STATUS_PAID                = 4;
		const STATUS_CANCELLED           = 5;
		
		/**
		 * @inheritdoc
		 */
		public static function tableName(){
			return 'commission';
		}
		
		/**
		 * @inheritdoc
		 */
		public function rules(){
			return [
				[['appointment_id', 'status', 'payment_method_id', 'doctor_payment_id'], 'integer'],
				[['amount', 'percent'], 'number'],
				[['paid_on', 'conekta_order_id'], 'safe'],
				[
					['appointment_id'],
					'exist',
					'skipOnError'     => true,
					'targetClass'     => Appointment::className(),
					'targetAttribute' => ['appointment_id' => 'id'],
				],
				[
					['doctor_payment_id'],
					'exist',
					'skipOnError'     => true,
					'targetClass'     => DoctorPayment::className(),
					'targetAttribute' => ['doctor_payment_id' => 'id'],
				],
			];
		}
		
		/**
		 * @inheritdoc
		 */
		public function attributeLabels(){
			return [
				'id'                => Yii::t('app', 'ID'),
				'appointment_id'    => Yii::t('app', 'Appointment'),
				'amount'            => Yii::t('app', 'Amount'),
				'percent'           => Yii::t('app', 'Paid(%)'),
				'paid_on'           => Yii::t('app', 'Paid On'),
				'status'            => Yii::t('app', 'Status'),
				'doctor_payment_id' => Yii::t('app', 'Doctor Payment'),
				'payment_method_id' => Yii::t('app', 'Payment Method'),
				'conekta_order_id' => Yii::t('app', 'Reference'),
			];
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAppointment(){
			return $this->hasOne(Appointment::className(), ['id' => 'appointment_id'])
				->inverseOf('commissions')
				;
		}
		
		/**
		 *
		 * @return \yii\db\ActiveQuery
		 */
		public function getDoctorPayment(){
			return $this->hasOne(DoctorPayment::className(), ['id' => 'doctor_payment_id'])
				->inverseOf('commissions')
				;
		}
		
		public function getPaymentMethod(){
			return $this->hasOne(PaymentMethod::className(), ['id' => 'payment_method_id'])->inverseOf('commissions');
		}
		
	}
