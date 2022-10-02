<?php

	namespace common\models;

	use Yii;
use yii\helpers\Json;
use yii\web\UploadedFile;

	/**
	 * This is the model class for table "doctor_payment".
	 *
	 * @property integer      $id
	 * @property string       $invoice_url
	 * @property string       $invoice_name
	 * @property string       $paid_on
	 * @property integer      $status
	 * @property string       $amount
	 * @property integer      $doctor_id
	 * @property integer      $user_id
	 * @property string       $notes
	 * @property string       $receipt_url
	 * @property string       $receipt_name
	 * @property integer	  $currency_id
	 *
	 * @property Doctor       $doctor
	 * @property User         $user
	 * @property Commission[] $commissions
	 * @property Currency     $currency
	 */
	class DoctorPayment extends \yii\db\ActiveRecord{

		public $invoiceBase64data, $receiptBase64data;

		const STATUS_PENDING_APPOINTMENT = 1;
		const STATUS_INVOICE_REQUEST     = 2;
		const STATUS_PENDING_PAYMENT     = 3;
		const STATUS_PAID                = 4;
		const STATUS_CANCELLED           = 5;

		/**
		 * @inheritdoc
		 */
		public static function tableName(){
			return 'doctor_payment';
		}

		/**
		 * @inheritdoc
		 */
		public function rules(){
			return [
				[['paid_on', 'amount', 'invoiceBase64data', 'receiptBase64data'], 'safe'],
				[['status', 'doctor_id', 'user_id', 'currency_id'] , 'integer'],
				[['notes'], 'string'],
				[['invoice_url', 'invoice_name', 'receipt_url', 'receipt_name'], 'string', 'max' => 255],
				[['receiptBase64data'], 'file', 'skipOnEmpty' => true, 'minSize' => 5*1024, 'maxSize' => 1024*1024*5, 'extensions' => 'pdf, jpg, png, gif, jpeg'],//check php.ini upload_max_filesize
				[
					['doctor_id'],
					'exist',
					'skipOnError'     => true,
					'targetClass'     => Doctor::className(),
					'targetAttribute' => ['doctor_id' => 'id'],
				],
				[
					['user_id'],
					'exist',
					'skipOnError'     => true,
					'targetClass'     => User::className(),
					'targetAttribute' => ['user_id' => 'id'],
				],
				[
						['currency_id'],
						'exist',
						'skipOnError'     => true,
						'targetClass'     => Currency::className(),
						'targetAttribute' => ['currency_id' => 'id'],
				],
			];
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels(){
			return [
				'id'           => Yii::t('app', 'ID'),
				'invoice_url'  => Yii::t('app', 'Invoice Url'),
				'invoice_name' => Yii::t('app', 'Invoice Name'),
				'paid_on'      => Yii::t('app', 'Paid On'),
				'status'       => Yii::t('app', 'Status'),
				'amount'       => Yii::t('app', 'Amount'),
				'doctor_id'    => Yii::t('app', 'Doctor'),
				'user_id'      => Yii::t('app', 'User'),
				'notes'        => Yii::t('app', 'Notes'),
				'receipt_url'  => Yii::t('app', 'Receipt Url'),
				'receipt_name' => Yii::t('app', 'Receipt Name'),
				'currency_id'  => Yii::t('app', 'Currency'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getDoctor(){
			return $this->hasOne(Doctor::className(), ['id' => 'doctor_id'])
				->inverseOf('doctorPayments')
				;
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser(){
			return $this->hasOne(User::className(), ['id' => 'user_id'])
				->inverseOf('doctorPayments')
				;
		}

		/**
		 *
		 * @return \yii\db\ActiveQuery
		 */
		public function getCommissions(){
			return $this->hasMany(Commission::className(), ['doctor_payment_id' => 'id'])
				->inverseOf('doctorPayment')
				;
		}

		public function getCurrency(){
			return $this->hasOne(Currency::className(), ['id' => 'currency_id'])->inverseOf('doctorPayments');
		}

		public function beforeSave($insert){
			if ($this->invoiceBase64data){
				$base64data = $this->invoiceBase64data;
				list($type, $file) = explode(';', $base64data);
				list(, $file) = explode(',', $file);
				$webroot = Yii::getAlias('@webroot');
				$prev_file = $this->invoice_url;
				if ($prev_file){
					// Delete previous file
					if (file_exists($webroot.$prev_file))
						unlink($webroot.$prev_file);
				}
				$ext = explode('/', $type)[1];
				switch ($type){
					case 'data:text/xml':
						$ext = 'xml';
						break;
					default:
						$ext = 'xml';
						break;
				}
				$file = base64_decode($file);
				$uploads = Yii::$app->params['uploadsDir'];
				if (!is_dir($webroot.$uploads))
					mkdir($webroot.$uploads);

				list($usec, $sec) = explode(' ', microtime());
				$filename = $uploads.'invc_'.$this->id.'_'.date('YmdHis').$usec.'.'.$ext;
				file_put_contents($webroot.$filename, $file);
				$this->invoice_url = $filename;

			}
			if ($_FILES && $_FILES['DoctorPayment'] && $_FILES['DoctorPayment']['tmp_name'] && $_FILES['DoctorPayment']['type']['receiptBase64data']){
				$receipt = UploadedFile::getInstance($this, 'receiptBase64data');

				$webroot = Yii::getAlias('@webroot');
				$prev_file = $this->receipt_url;
				if ($prev_file){
					// Delete previous file
					if (file_exists($webroot.$prev_file))
						unlink($webroot.$prev_file);
				}
				$ext = $receipt->extension;

				$uploads = Yii::$app->params['uploadsDir'];
				if (!is_dir($webroot.$uploads))
					mkdir($webroot.$uploads);

				list($usec, $sec) = explode(' ', microtime());
				$filename = $uploads.'rcpt_'.$this->id.'_'.date('YmdHis').$usec.'.'.$ext;

				$this->receipt_name = $receipt->name;
				$receipt->saveAs($webroot.$filename);
				$this->receipt_url = $filename;
			}

			return parent::beforeSave($insert);
		}

		public function getDownloadInvoiceUrl(){
			$file = Yii::getAlias('@doctors').'/web/'.$this->invoice_url;
			return $file;
		}

		public function getDownloadReceiptUrl(){
			$file = Yii::getAlias('@enterprise').'/web/'.$this->receipt_url;
			return $file;
		}

		public function afterSave($insert, $changedAttributes){
			if ($insert){
				$notification = new Notification([
						'target_id' => $this->doctor->user->id,
						'text' => Json::encode([
								'en' => 'Remedyk has created a new payment order for you.',
								'es' => 'Remedyk ha creado una nueva orden de pago para usted.',
						]),
						'fa_icon_class' => 'fa fa-money text-requested',
						'target_url' => '/doctor-payments/index',
				]);
				$notification->save();
			}
			elseif (isset($changedAttributes['status'])){
				switch ($this->status){
					case self::STATUS_PENDING_PAYMENT:
						foreach ($this->commissions as $commission) {
							$commission->status = Commission::STATUS_PENDING_PAYMENT;
							$commission->save();
						}
						break;
					case self::STATUS_PAID:
						foreach ($this->commissions as $commission) {
							$commission->status = Commission::STATUS_PAID;
							$commission->save();
						}

						$notification = new Notification([
						'target_id' => $this->doctor->user->id,
						'text' => Json::encode([
						'en' => 'Remedyk has sent your payment.',
						'es' => 'Remedyk le ha enviado su pago.',
						]),
						'fa_icon_class' => 'fa fa-money text-confirmed',
						'target_url' => '/doctor-payments/index',
						]);
						$notification->save();
						break;
					case self::STATUS_CANCELLED:
						foreach ($this->commissions as $commission) {
							$commission->status = Commission::STATUS_CANCELLED;
							$commission->save();
						}

						$notification = new Notification([
						'target_id' => $this->doctor->user->id,
						'text' => Json::encode([
						'en' => 'Remedyk has cancelled your payment.',
						'es' => 'Remedyk ha cancelado su pago.',
						]),
						'fa_icon_class' => 'fa fa-money text-cancelled',
						'target_url' => '/doctor-payments/index',
						]);
						$notification->save();
						break;
				}
			}
			return parent::afterSave($insert, $changedAttributes);
		}
	}
