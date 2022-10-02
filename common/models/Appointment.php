<?php

namespace common\models;

use Yii;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Json;

/**
 * This is the model class for table "appointment".
 *
 * @property integer $id
 * @property boolean $is_procedure
 * @property string $date
 * @property string $is_waiting
 * @property boolean $is_done
 * @property string $notes
 * @property boolean $is_active
 * @property string $price
 * @property integer $status
 * @property string $confirmation_datetime
 * @property string $cancel_datetime
 * @property boolean $is_quot
 * @property integer $patient_id
 * @property integer $doctor_id
 * @property integer $office_id
 * @property integer $operating_room_id
 * @property integer $procedure2doctor_id
 * @property integer $currency_id
 * @property string $start_time
 * @property string $end_time
 * @property integer $changed_by
 * @property string $created_at
 * 
 * @property AdditionalService2appointment[] $additionalService2appointments
 * @property Currency $currency
 * @property Doctor $doctor
 * @property Office $office
 * @property OperatingRoom $operatingRoom
 * @property Patient $patient
 * @property Procedure2doctor $procedure2doctor
 * @property ClinicalStory[] $clinicalStories
 * @property Prescription[] $prescriptions
 * @property Commission[] $commissions
 * @property User $changedBy
 */
class Appointment extends \common\models\BaseModel
{
	const STATUS_REQUESTED = 1;
	const STATUS_ACCEPTED = 2;
	const STATUS_CONFIRMED = 3;
	const STATUS_REJECTED = 4;
	const STATUS_CANCELLED = 5;
	const STATUS_OPEN = 6;
	const STATUS_CLOSED = 7;
	const STATUS_CONFIRMED_BY_DOCTOR = 8;
	
	const LOCATION_TYPE_OFFICE = 'office';
	const LOCATION_TYPE_OPERATING_ROOM = 'poerating_room';
	
	public $date_time_start, $location_id, $is_in_wait_list;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'appointment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_procedure', 'is_done', 'is_active', 'is_quot'], 'boolean'],
            [['date', 'is_waiting', 'confirmation_datetime', 'cancel_datetime', 'start_time', 'end_time', 'date_time_start', 'created_at', 'location_id', 'is_in_wait_list'], 'safe'],
            [['notes'], 'string'],
            [['price', 'patient_id', 'doctor_id'], 'required'],
            [['status', 'patient_id', 'doctor_id', 'office_id', 'operating_room_id', 'procedure2doctor_id', 'currency_id', 'changed_by'], 'integer'],
        	['status', 'in', 'range' => [self::STATUS_REQUESTED, self::STATUS_ACCEPTED, self::STATUS_CONFIRMED, self::STATUS_REJECTED, self::STATUS_CANCELLED, self::STATUS_OPEN, self::STATUS_CLOSED, self::STATUS_CONFIRMED_BY_DOCTOR]],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::className(), 'targetAttribute' => ['currency_id' => 'id']],
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctor::className(), 'targetAttribute' => ['doctor_id' => 'id']],
            [['office_id'], 'exist', 'skipOnError' => true, 'targetClass' => Office::className(), 'targetAttribute' => ['office_id' => 'id']],
            [['operating_room_id'], 'exist', 'skipOnError' => true, 'targetClass' => OperatingRoom::className(), 'targetAttribute' => ['operating_room_id' => 'id']],
            [['patient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Patient::className(), 'targetAttribute' => ['patient_id' => 'id']],
        	[['changed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['changed_by' => 'id']],
            [['procedure2doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Procedure2doctor::className(), 'targetAttribute' => ['procedure2doctor_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'is_procedure' => Yii::t('app', 'Is Procedure'),
            'date' => Yii::t('app', 'Date'),
            'is_waiting' => Yii::t('app', 'Is Waiting'),
            'is_done' => Yii::t('app', 'Is Done'),
            'notes' => Yii::t('app', 'Notes'),
            'is_active' => Yii::t('app', 'Is Active'),
            'price' => Yii::t('app', 'Price'),
            'status' => Yii::t('app', 'Status'),
            'confirmation_datetime' => Yii::t('app', 'Confirmation Datetime'),
            'cancel_datetime' => Yii::t('app', 'Cancel Datetime'),
            'is_quot' => Yii::t('app', 'Is Quot'),
            'patient_id' => Yii::t('app', 'Patient'),
            'doctor_id' => Yii::t('app', 'Doctor'),
            'office_id' => Yii::t('app', 'Office'),
            'operating_room_id' => Yii::t('app', 'Operating Room'),
            'procedure2doctor_id' => Yii::t('app', 'Procedure'),
            'currency_id' => Yii::t('app', 'Currency'),
        	'start_time' => Yii::t('app', 'From'),
        	'end_time' => Yii::t('app', 'To'),
        	'changed_by' => Yii::t('app', 'Changed By'),
        	'is_in_wait_list' => Yii::t('app', 'Notify me if there is a better schedule time')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdditionalService2appointments()
    {
        return $this->hasMany(AdditionalService2appointment::className(), ['appointment_id' => 'id'])->inverseOf('appointment');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(Currency::className(), ['id' => 'currency_id'])->inverseOf('appointments');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id'])->inverseOf('appointments');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffice()
    {
        return $this->hasOne(Office::className(), ['id' => 'office_id'])->inverseOf('appointments');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperatingRoom()
    {
        return $this->hasOne(OperatingRoom::className(), ['id' => 'operating_room_id'])->inverseOf('appointments');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatient()
    {
        return $this->hasOne(Patient::className(), ['id' => 'patient_id'])->inverseOf('appointments');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcedure2doctor()
    {
        return $this->hasOne(Procedure2doctor::className(), ['id' => 'procedure2doctor_id'])->inverseOf('appointments');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClinicalStories()
    {
        return $this->hasMany(ClinicalStory::className(), ['appointment_id' => 'id'])->inverseOf('appointment');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrescriptions()
    {
        return $this->hasMany(Prescription::className(), ['appointment_id' => 'id'])->inverseOf('appointment');
    }
    
    /**
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getCommissions(){
    	return $this->hasMany(Commission::className(), ['appointment_id' => 'id'])->inverseOf('appointment');
    }
    
    public function getChangedBy(){
    	return $this->hasOne(User::className(), ['id' => 'changed_by'])->inverseOf('changedAppointments');
    }
    
    public function afterFind(){
    	if ($this->office_id && !$this->operating_room_id){
    		$this->location_id = Appointment::LOCATION_TYPE_OFFICE.'-'.$this->office_id;
    	}
    	if ($this->operating_room_id && !$this->office_id){
    		$this->location_id = Appointment::LOCATION_TYPE_OPERATING_ROOM.'-'.$this->operating_room_id;
    	}
    	if ($this->is_waiting){
    		$this->is_in_wait_list = true;
    	}
    	else {
    		$this->is_in_wait_list = false;
    	}
    	return parent::afterFind();
    }
    
    public function beforeSave($insert){
    	$this->changed_by = Yii::$app->user->identity->id;
    	
    	if ($this->location_id){
    		$parts = explode('-', $this->location_id);
    		$location_type = $parts[0];
    		$location_id = $parts[1];
    		switch ($location_type) {
    			case Appointment::LOCATION_TYPE_OFFICE:
    				$this->office_id = $location_id;
    				$this->operating_room_id = null;
    				break;
    			case Appointment::LOCATION_TYPE_OPERATING_ROOM:
    				$this->office_id = null;
    				$this->operating_room_id = $location_id;
    				break;
    		}
    	}
    	else {
    		$this->office_id = null;;
    		$this->operating_room_id = null;
    	}
    	
    	if ($this->is_in_wait_list){
    		$this->is_waiting = date('Y-m-d');
    	}
    	else {
    		$this->is_waiting = null;
    	}
    	
    	$this->is_procedure = $this->procedure2doctor_id ? true : false; 
    	
    	
    	if (!$this->created_at){
    		$this->created_at = date('Y-m-d');
    	}
    	
    	if ($this->date_time_start){
    		$this->date = date('Y-m-d', strtotime($this->date_time_start));
    		$this->start_time = date('H:i:s', strtotime($this->date_time_start));
    	}
    	
    	$this->calculateEndTime();
    	$failed = false;
    	if (!$this->doctor->isAvailableAtRange($this->date, $this->start_time, $this->end_time, $this->office_id)){
    		$collissionAppointment = $this->doctor->getAppointmentAtRange($this->date, $this->start_time, $this->end_time);
    		if (!$collissionAppointment || $collissionAppointment->id != $this->id){
	    		$this->addError('date', Yii::t('app', 'Doctor is not available at this time'));
	    		$failed = true;
    		}
    	}    	
    	
    	$appointment_anticipation = $this->doctor->appointment_anticipation ? $this->doctor->appointment_anticipation : 0;
    	
    	if (date('Y-m-d', strtotime($this->date. " -{$appointment_anticipation} days")) < date('Y-m-d', strtotime($this->created_at)) ){
    		$this->addError('date', Yii::t('app', 'Appointment must be set with').' '.$appointment_anticipation.' '.Yii::t('app', 'days of anticipation'));
    		$failed = true;
    	}
    	
    	
    	
    	if ($failed)
    		return false;
    	
    	return parent::beforeSave($insert);
    	
    }
    
    public function sendRescheduleToWaitList(){
    	$next = Appointment::find()->where(['doctor_id' => $this->doctor_id, 'status' => [Appointment::STATUS_REQUESTED, Appointment::STATUS_ACCEPTED]])
    	->andWhere(['>', 'date', $this->date])
    	->andWhere(['not', ['is_waiting' => null]])
    	->orderBy(['is_waiting' => 'asc'])->one();
    	if ($next){
	    	$patient = $next->patient;
	    	$next->date = $this->date;
	    	$next->start_time = $this->start_time;
	    	$next->end_time = $this->end_time;
	    	$next->changed_by = $patient->user->id;
	    	$next->status = Appointment::STATUS_REQUESTED;
	    	if ($next->save()){
	    	
		    	$notification = new Notification([
		    			'target_id' => $patient->user->id,
		    			'text' => Json::encode([
		    					'en' => 'Your appointment with the doctor '.$this->doctor->user->name.' has been rescheduled to a better date/time.',
		    					'es' => 'Su cita con el doctor '.$this->doctor->user->name.' ha sido reagendada a una fecha/hora mejor.'
		    			]),
		    			'target_url' => '/appointments/index',
		    			'fa_icon_class' => 'fa fa-calendar text-requested'
		    	]);
		    	$notification->save();
	    	}
    	}
    }
    
    public function afterSave($insert, $changedAttributes){
    	
    	$doctor = $this->doctor;
    	$patient = $this->patient;
    	$user = User::getUserIdentity();
    	
    	
    	if ($insert){
    		
    		if ($user->id == $doctor->user->id){
    			$target_id = $patient->user->id;
    			$text = Json::encode([
    					'en' => 'The doctor '.$doctor->user->name.' has requested an appointment.',
    					'es' => 'El doctor '.$doctor->user->name.' le ha solicitado una cita'
    			]);
    			$target_url = '/appointments/index?id='.$this->id;
    		}
    		else {
    			$target_id = $doctor->user->id;
    			$text = Json::encode([
    					'en' => 'The patient '.$patient->user->name.' has requested an appointment.',
    					'es' => 'El paciente '.$patient->user->name.' le ha solicitado una cita.'
    			]);
    			$target_url = '/site/index?confirmed=false&requested=true&accepted=false&rejected=false&cancelled=false&id='.$this->id;
    		}
    		$notification = new Notification([
    				'target_id' => $target_id,
    				'text' => $text,
    				'target_url' => $target_url,
    				'fa_icon_class' => 'fa fa-calendar text-requested'
    		]);
    		$notification->save();
    	}
    	elseif (isset($changedAttributes['status'])){
    		switch ($this->status){
    			case Appointment::STATUS_ACCEPTED:
    				if ($user->id == $doctor->user->id){
    					$target_id = $patient->user->id;
    					$text = Json::encode([
    							'en' => 'The doctor '.$doctor->user->name.' has accepted your appointment.',
    							'es' => 'El doctor '.$patient->user->name.' ha aceptado su cita.'
    					]);
    					$target_url = '/appointments/index?id='.$this->id;
    				}
    				else {
    					$target_id = $doctor->user->id;
    					$text = Json::encode([
    							'en' => 'The patient '.$patient->user->name.' has accepted your appointment.',
    							'es' => 'El paciente '.$patient->user->name.' ha aceptado su cita.'
    					]);
    					$target_url = '/site/index?confirmed=false&requested=false&accepted=true&rejected=false&cancelled=false&id='.$this->id;
    				}
    				$fa_icon_class = 'fa fa-calendar text-accepted';
    				
    				$notification = new Notification([
    						'target_id' => $target_id,
    						'text' => $text,
    						'target_url' => $target_url,
    						'fa_icon_class' => $fa_icon_class
    				]);
    				$notification->save();
    				break;
    			case Appointment::STATUS_CANCELLED:
    				if ($user->id == $doctor->user->id){
    					$target_id = $patient->user->id;
    					$text = Json::encode([
    							'en' => 'The doctor '.$doctor->user->name.' has cancelled your appointment.',
    							'es' => 'El doctor '.$patient->user->name.' ha cancelado su cita.'
    					]);
    					$target_url = '/appointments/index?id='.$this->id;
    				}
    				else {
    					$target_id = $doctor->user->id;
    					$text = Json::encode([
    							'en' => 'The patient '.$patient->user->name.' has cancelled your appointment.',
    							'es' => 'El paciente '.$patient->user->name.' ha cancelado su cita.'
    					]);
    					$target_url = '/site/index?confirmed=false&requested=false&accepted=false&rejected=false&cancelled=true&id='.$this->id;
    				}
    				$fa_icon_class = 'fa fa-calendar text-cancelled';
    			
    				$notification = new Notification([
    						'target_id' => $target_id,
    						'text' => $text,
    						'target_url' => $target_url,
    						'fa_icon_class' => $fa_icon_class
    				]);
    				$notification->save();
    				if ($user->id == $patient->user->id){
    					$this->sendRescheduleToWaitList();
    				}
    				break;
    			case Appointment::STATUS_REJECTED:
    				if ($user->id == $doctor->user->id){
    					$target_id = $patient->user->id;
    					$text = Json::encode([
    							'en' => 'The doctor '.$doctor->user->name.' has rejected your appointment.',
    							'es' => 'El doctor '.$patient->user->name.' ha rechazado su cita.'
    					]);
    					$target_url = '/appointments/index?id='.$this->id;
    				}
    				else {
    					$target_id = $doctor->user->id;
    					$text = Json::encode([
    							'en' => 'The patient '.$patient->user->name.' has rejected your appointment.',
    							'es' => 'El paciente '.$patient->user->name.' ha rechazado su cita.'
    					]);
    					$target_url = '/site/index?confirmed=false&requested=false&accepted=false&rejected=true&cancelled=false&id='.$this->id;
    				}
    				$fa_icon_class = 'fa fa-calendar text-rejected';
    				
    				$notification = new Notification([
    						'target_id' => $target_id,
    						'text' => $text,
    						'target_url' => $target_url,
    						'fa_icon_class' => $fa_icon_class
    				]);
    				$notification->save();
    				break;
    			case Appointment::STATUS_CONFIRMED:
    				if ($user->id == $doctor->user->id){
    					$target_id = $patient->user->id;
    					$text = Json::encode([
    							'en' => 'The doctor '.$doctor->user->name.' has confirmed your appointment.',
    							'es' => 'El doctor '.$patient->user->name.' ha confirmado su cita.'
    					]);
    					$target_url = '/appointments/index?id='.$this->id;
    				}
    				else {
    					$target_id = $doctor->user->id;
    					$text = Json::encode([
    							'en' => 'The patient '.$patient->user->name.' has confirmed your appointment.',
    							'es' => 'El paciente '.$patient->user->name.' ha confirmado su cita.'
    					]);
    					$target_url = '/site/index?id='.$this->id;
    				}
    				$fa_icon_class = 'fa fa-calendar text-confirmed';
    				
    				$notification = new Notification([
    						'target_id' => $target_id,
    						'text' => $text,
    						'target_url' => $target_url,
    						'fa_icon_class' => $fa_icon_class
    				]);
    				$notification->save();
    				break;
    			case Appointment::STATUS_CONFIRMED_BY_DOCTOR:
    				$target_id = $patient->user->id;
    				$text = Json::encode([
    						'en' => 'The doctor '.$doctor->user->name.' has confirmed your appointment. Please complete the confirmation with your payment.',
    						'es' => 'El doctor '.$patient->user->name.' ha confirmado su cita. Por favor complete la confirmación con su pago.'
    				]);
    				$target_url = '/appointments/index?id='.$this->id;
    				
    				$fa_icon_class = 'fa fa-calendar text-confirmed';
    				$notification = new Notification([
    						'target_id' => $target_id,
    						'text' => $text,
    						'target_url' => $target_url,
    						'fa_icon_class' => $fa_icon_class
    				]);
    				$notification->save();
    				break;
    		}
    	}
    	elseif (isset($changedAttributes['date']) || isset($changedAttributes['start_time']) || isset($changedAttributes['price']) || isset($changedAttributes['currency_id'])){
    			if ($user->id == $doctor->user->id){
    				$target_id = $patient->user->id;
    				$text = Json::encode([
    						'en' => 'The doctor '.$doctor->user->name.' has requested to change the date/time/price of the appointment.',
    						'es' => 'El doctor '.$patient->user->name.' ha solicitado cambiar la fecha/hora/precio de su cita.'
    				]);
    				$target_url = '/appointments/index?id='.$this->id;
    			}
    			else {
    				$target_id = $doctor->user->id;
    				$text = Json::encode([
    						'en' => 'The patient '.$patient->user->name.' has requested to change the date/time of the appointment.',
    						'es' => 'El paciente '.$patient->user->name.' ha solicitado cambiar la fecha/hora de la cita.'
    				]);
    				$target_url = '/site/index?id='.$this->id;
    			}
    			$fa_icon_class = 'fa fa-calendar text-requested';
    			
    			$notification = new Notification([
    					'target_id' => $target_id,
    					'text' => $text,
    					'target_url' => $target_url,
    					'fa_icon_class' => $fa_icon_class
    			]);
    			$notification->save();
    		}
    	
    	return parent::afterSave($insert, $changedAttributes);
    }
    public function calculateEndTime(){
    	$duration = $this->doctor->appointment_duration;
    	$hours = date('H', strtotime($duration));
    	$minutes = date('i', strtotime($duration));
    	$this->end_time = date('H:i:s', strtotime($this->start_time." +{$hours} hours +{$minutes} minutes"));
    }
}
