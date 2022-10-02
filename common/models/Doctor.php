<?php

namespace common\models;

use Yii;
use yii\web\UploadedFile;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;

/**
 * This is the model class for table "doctor".
 *
 * @property integer $id
 * @property string $license_number
 * @property string $resume
 * @property string $notes
 * @property integer $bank_data
 * @property string $appointment_price
 * @property integer $appointment_anticipation
 * @property integer $user_id
 * @property integer $postal_address_id
 * @property integer $tax_data_id
 * @property integer $currency_id
 * @property string $gender
 * @property string $appointment_duration
 *
 * @property Appointment[] $appointments
 * @property ClinicalStory[] $clinicalStories
 * @property Address $postalAddress
 * @property Currency $currency
 * @property TaxData $taxData
 * @property User $user
 * @property DoctorPicture[] $doctorPictures
 * @property DoctorVideo[] $doctorVideos
 * @property DoctorWorkingHour[] $doctorWorkingHours
 * @property Membership2doctor[] $membership2doctors
 * @property Membership[] $membersships
 * @property Office[] $offices
 * @property OperatingRoom[] $operatingRooms
 * @property Prescription[] $prescriptions
 * @property Procedure2doctor[] $procedure2doctors
 * @property Qualification[] $qualifications
 * @property Specialty2doctor[] $specialty2doctors
 * @property Specialty[] $specialties
 * @property Procedure[] $procedures
 * @property Patient[] $patients
 * @property Certification[] $certifications
 * @property Certification2doctor[] $certification2doctors
 * @property DoctorPayment[] $doctorPayments
 */
class Doctor extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'doctor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['resume', 'notes', 'gender', 'appointment_duration'], 'string'],
            [['bank_data', 'appointment_anticipation', 'user_id', 'postal_address_id', 'tax_data_id', 'currency_id'], 'integer'],
            [['appointment_price'], 'number'],
            [['license_number'], 'string', 'max' => 255],
            [['postal_address_id'], 'exist', 'skipOnError' => true, 'targetClass' => Address::className(), 'targetAttribute' => ['postal_address_id' => 'id']],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::className(), 'targetAttribute' => ['currency_id' => 'id']],
            [['tax_data_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaxData::className(), 'targetAttribute' => ['tax_data_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'license_number' => Yii::t('app', 'License Number'),
            'resume' => Yii::t('app', 'Resume'),
            'notes' => Yii::t('app', 'Notes'),
            'bank_data' => Yii::t('app', 'Bank Data'),
            'appointment_price' => Yii::t('app', 'Appointment Rate'),
            'appointment_anticipation' => Yii::t('app', 'Days in Advance to Get an Appointment'),
            'user_id' => Yii::t('app', 'User'),
            'postal_address_id' => Yii::t('app', 'Postal Address'),
        	'gender' => Yii::t('app', 'Gender'),
            'tax_data_id' => Yii::t('app', 'Tax Data'),
            'currency_id' => Yii::t('app', 'Currency'),
        	'appointment_duration' => Yii::t('app', 'Duration of Appointment')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppointments()
    {
        return $this->hasMany(Appointment::className(), ['doctor_id' => 'id'])->inverseOf('doctor');
    }
    
    /**
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getPatients(){
    	return $this->hasMany(Patient::className(), ['id' => 'patient_id'])->viaTable('appointment', ['doctor_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClinicalStories()
    {
        return $this->hasMany(ClinicalStory::className(), ['doctor_id' => 'id'])->inverseOf('doctor');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPostalAddress()
    {
        return $this->hasOne(Address::className(), ['id' => 'postal_address_id'])->inverseOf('doctors');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(Currency::className(), ['id' => 'currency_id'])->inverseOf('doctors');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxData()
    {
        return $this->hasOne(TaxData::className(), ['id' => 'tax_data_id'])->inverseOf('doctors');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])->inverseOf('doctor');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorPictures()
    {
        return $this->hasMany(DoctorPicture::className(), ['doctor_id' => 'id'])->inverseOf('doctor');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorVideos()
    {
        return $this->hasMany(DoctorVideo::className(), ['doctor_id' => 'id'])->inverseOf('doctor');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorWorkingHours()
    {
        return $this->hasMany(DoctorWorkingHour::className(), ['doctor_id' => 'id'])->inverseOf('doctor');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembership2doctors()
    {
        return $this->hasMany(Membership2doctor::className(), ['doctor_id' => 'id'])->inverseOf('doctor');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMemberships()
    {
        return $this->hasMany(Membership::className(), ['id' => 'membership_id'])->viaTable('membership2doctor', ['doctor_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffices()
    {
        return $this->hasMany(Office::className(), ['doctor_id' => 'id'])->inverseOf('doctor');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperatingRooms()
    {
        return $this->hasMany(OperatingRoom::className(), ['doctor_id' => 'id'])->inverseOf('doctor');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrescriptions()
    {
        return $this->hasMany(Prescription::className(), ['doctor_id' => 'id'])->inverseOf('doctor');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcedure2doctors()
    {
        return $this->hasMany(Procedure2doctor::className(), ['doctor_id' => 'id'])->inverseOf('doctor');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcedures()
    {
    	return $this->hasMany(Procedure::className(), ['id' => 'procedure_id'])->viaTable('procedure2doctor', ['doctor_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQualifications()
    {
        return $this->hasMany(Qualification::className(), ['doctor_id' => 'id'])->inverseOf('doctor');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecialty2doctors()
    {
        return $this->hasMany(Specialty2doctor::className(), ['doctor_id' => 'id'])->inverseOf('doctor');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecialties()
    {
    	return $this->hasMany(Specialty::className(), ['id' => 'specialty_id'])->viaTable('specialty2doctor', ['doctor_id' => 'id']);
    }
    
    /**
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getCertification2doctors(){
    	return $this->hasMany(Certification2doctor::className(), ['doctor_id' => 'id'])->inverseOf('doctor');
    }
    
    /**
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getCertifications(){
    	return $this->hasMany(Certification::className(), ['id' => 'certification_id'])->viaTable('certification2doctor', ['doctor_id' => 'id']);
    }
    
    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorPayments(){
    	return $this->hasMany(DoctorPayment::className(), ['doctor_id' => 'id'])->inverseOf('doctor');
    }
    
    
    public function getPicture(){
    	$doctorDomain = Yii::$app->params['doctorsDomain'];
    	$picture = isset($this->user->picture) ? $doctorDomain.$this->user->picture : $doctorDomain.'/images/doctor_default.png';
    	return $picture;
    }
    
    public function setPicture(){	
    	return $this->user->setProfilePicture();
    }
    
    public function getRating(){
    	$average = Qualification::find()->where(['doctor_id' => $this->id, 'is_active' => true])->average('rate');
    	return $average ? (round(($average) * 2) / 2) : 0.0;
    }
    
    public function getMembership(){
    	if (!$memberships = $this->memberships){
    		$defaultMembership = Membership::findOne(['price' => 0]);
    		$default_membership_id = $defaultMembership ? $defaultMembership->id : 1;
    		$membership = new Membership2doctor(['membership_id' => $default_membership_id, 'doctor_id' => $this->id]);
    		$membership->save();
    		$this->refresh();
    		$memberships = $this->memberships;
    	}
    	return $memberships[0];
    	
    }
    
    public function getUnavailableHours($month, $year, $office_id=null){
    	$unabailableHours = [];
    	$first_day = 1;
    	$last_day = date('d', strtotime($year.'-'.$month.'-'.$first_day.' +1 month -1 day'));
    	for ($i = $first_day; $i <= $last_day; $i++){
    		$unabailableHours[$i] = [['00:00:00', '23:59:59']];
    	}
    	
    	
    	
    	$workingHoursQuery = DoctorWorkingHour::find()->where([
    			'doctor_id' => $this->id,
    			'is_active' => true,
    			'is_enabled' => true,
    			'is_working_hour' => true
    	]);
    	
    	if ($office_id != null){
    		$workingHoursQuery->andWhere([
    				'or', ['office_id' => $office_id], ['office_id' => null]
    		]);
    	}
    	else {
    		$office_id = null;
    	}
    	$workingHours = $workingHoursQuery->all();
    	foreach ($workingHours as $workingHour){
    		/* @var \common\models\DoctorWorkingHour $workingHour */
    		for ($i = $first_day; $i <= $last_day; $i++){
    			if ($workingHour->appliesToDate($year.'-'.$month.'-'.$i)){
    				$unabailableHours[$i] = $this->splitTimes($unabailableHours[$i], $workingHour);
    			}
    		}
    	}
    	
    	$notWorkingHoursQuery = DoctorWorkingHour::find()->where([
    			'doctor_id' => $this->id,
    			'is_active' => true,
    			'is_enabled' => true,
    			'is_working_hour' => false,
    			'office_id' => $office_id
    	]);
    	
    	
    	$notWorkingHours = $notWorkingHoursQuery->all();
    	
    	foreach ($notWorkingHours as $notWorkingHour){
    		/* @var \common\models\DoctorWorkingHour $notWorkingHour */
    		$day = $notWorkingHour->month_day;
    		for ($i = $first_day; $i <= $last_day; $i++){
    			
    			if ($notWorkingHour->appliesToDate($year.'-'.$month.'-'.$i)){
    				$unabailableHours[$i] = $this->insertAndMergeTimes($unabailableHours[$i], $notWorkingHour);
    			}
    		}
    	}
    	
    	return $unabailableHours;
    }
    
    public function isAvailableAtRange($date, $startTime, $endTime, $office_id=null){
    	$year = date('Y', strtotime($date));
    	$month = date('m', strtotime($date));
    	$month_day = date('d', strtotime($date));
    	
    	$startTime = date('H:i:s', strtotime($startTime));
    	$endTime = date('H:i:s', strtotime($endTime));
    	
    	$notWorkingHours = $this->getUnavailableHours($month, $year, $office_id);
    	
    	foreach ($notWorkingHours[(int)$month_day] as $range){
    		if ($endTime > $range[0] && $startTime < $range[1] ){
    			return false;
    		}
    	}
    	return !$this->hasAppointmentAtRange($date, $startTime, $endTime);
    }
    
    public function isAvailableAt($dateTime, $office_id=null){
    	$date = date('Y-m-d', strtotime($dateTime));    	    
    	$time = date('H:i:s', strtotime($dateTime));
    	
    	return $this->isAvailableAtRange($date, $time, $time, $office_id);    	
    }
    
    
    public function hasAppointmentAtRange($date, $startTime, $endTime){
    	$appointment = $this->getAppointmentAtRange($date, $startTime, $endTime);
    	if ($appointment){
    		return true;
    	}
    	return false;
    }
    
    public function hasAppointmentAt($dateTime){
    	$appointment = $this->getAppointmentAt($dateTime);
    	if ($appointment){
    		return true;
    	}
    	return false;
    }
    
    /**
     * Gets an appointment for the current doctor that 
     * 
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @return \common\models\Appointment|array|NULL
     */
    public function getAppointmentAtRange($date, $startTime, $endTime){
    	$date = date('Y-m-d', strtotime($date));
    	
    	$startTime = date('H:i:s', strtotime($startTime));
    	$endTime = date('H:i:s', strtotime($endTime));
    	
    	$appointment = Appointment::find()->where([
    			'doctor_id' => $this->id,
    			'date' => $date,
    			
    	])->andWhere([
    			'not', ['status' => [Appointment::STATUS_CANCELLED, Appointment::STATUS_CLOSED, Appointment::STATUS_REJECTED]]
    	])->andWhere([
    			'<', 'start_time', $endTime
    	])->andWhere([
    			'>', 'end_time', $startTime
    	])->orderBy(['start_time' => 'asc'])
    	->one();
    	return $appointment;
    }
    
    public function getAppointmentAt($dateTime){
    	$date = date('Y-m-d', strtotime($dateTime));   	
    	$time = date('H:i:s', strtotime($dateTime));
    	    	
    	return $this->getAppointmentAtRange($date, $time, $time);
    }
    
    /**
     * Get first available dates and times for making an appointment
     * @param integer $office_id the id of the desired office to get availability of the doctor (default null to get availability in general at any office)
     * @param integer $quantity The count of available dates and times to Return (default 3)
     * 
     * @return string[] dates and times in the format Y-m-d\TH:i:s of the first available moments
     */
    public function getFirstAvailable($office_id=null, $quantity=3){
    	$days = $this->appointment_anticipation ? $this->appointment_anticipation : 0;
    	$date = date('Y-m-d', strtotime(date('Y-m-d')." +{$days} days"));
    	
    	$finalDate = date('Y', strtotime(date('Y').' +1 year')).'-12-31';
    	
    	$hours = date('H', strtotime($this->appointment_duration));
    	$minutes = date('i', strtotime($this->appointment_duration));
    	$lastStartTime = date('H:i:s', strtotime('23:59:59'. " -{$hours} hours -{$minutes} minutes"));
    	$dateTimes = [];
    	
    	while (count($dateTimes) < $quantity && $date <= $finalDate){
    		$year = date('Y', strtotime($date));
    		$month = date('m', strtotime($date));
    		$month_day = date('d', strtotime($date));
    		
    		$notWorkingHours = $this->getUnavailableHours($month, $year, $office_id);
    		if ($this->doesNotWorkThisMonth($notWorkingHours)){
    			$date = date('Y-m-d', strtotime($date.' + 1 month'));
    		}
    		else {
	    		$testAppointment = new Appointment(['doctor_id' => $this->id, 'start_time' => '00:00:00']);
	    		$testAppointment->calculateEndTime();
	    		
	    		while (count($dateTimes) < $quantity && $testAppointment->start_time <= $lastStartTime){
		    		if ($this->isAvailableAtRange($date, $testAppointment->start_time, $testAppointment->end_time, $office_id)){
		    			$dateTimes[] = $date.'T'.$testAppointment->start_time;
		    			$testAppointment->start_time = $testAppointment->end_time;
		    			$testAppointment->calculateEndTime();
		    		}
		    		else {
		    			$scheduledAppointment = $this->getAppointmentAtRange($date, $testAppointment->start_time, $testAppointment->end_time);
		    			if ($scheduledAppointment){ // Collided with an appointment
		    				$testAppointment->start_time = $scheduledAppointment->end_time;
		    				$testAppointment->calculateEndTime();
		    			}
		    			else { // Collided with a non working hour
		    				foreach ($notWorkingHours[(int)$month_day] as $range){
		    					if ($testAppointment->start_time < $range[1] && $testAppointment->end_time > $range[0]){
		    						$testAppointment->start_time = $range[1];
		    						$testAppointment->calculateEndTime();
		    						break;
		    					}
		    				}
		    			}
		    		}
	    		}
	    		$date = date('Y-m-d', strtotime($date.' + 1 day'));
    		}
    		
    	}
    	return $dateTimes;
    	
    }
    
    private function doesNotWorkThisMonth($notWorkingHours){
    	foreach ($notWorkingHours as $day){
    		if (count($day) != 1 
    			|| $day[0][0] != '00:00:00'
    			|| $day[0][1] != '23:59:59'){
    			return false;
    		}
    	}
    	return true;
    }
    
    private function splitTimes($day, $workingHour){
    	$newDay = [];
    	$working_start_time = $workingHour->start_time ? $workingHour->start_time : '00:00:00';
    	$working_end_time = $workingHour->end_time ? $workingHour->end_time : '23:59:59';
    	
    	foreach ($day as $range){
    		$start_time = $range[0];
    		$end_time = $range[1];
    		$new_end_time = $new_start_time = null;
    		
    		if ($working_start_time >= $start_time && $working_start_time <= $end_time){ // We need to reset the end time
    			$new_end_time = $working_start_time;
    		}
    		
    		if ($working_end_time <= $end_time && $working_end_time >= $start_time){ // We need to reset the start time
    			$new_start_time = $working_end_time;
    		}
    		
    		if ($new_end_time && $new_end_time > $start_time){
    			$newDay[] = [$start_time, $new_end_time];
    		}
    		
    		if ($new_start_time && $new_start_time < $end_time){
    			$newDay[] = [$new_start_time, $end_time];
    		}
    		
    		if (!$new_start_time && !$new_end_time){
    			$newDay[] = [$start_time, $end_time];
    		}
    		
    		
    		
    	}
    	return $newDay;
    }
    
    private function insertAndMergeTimes($day, $notWorkingHour){
    	$newDay = [];
    	$not_working_start_time = $notWorkingHour->start_time ? $notWorkingHour->start_time : '00:00:00';
    	$not_working_end_time = $notWorkingHour->end_time ? $notWorkingHour->end_time : '23:59:59';
    	
    	foreach ($day as $range){
    		$start_time = $range[0];
    		$end_time = $range[1];
    		if ($start_time > $not_working_start_time){
    			$newDay[] = [$not_working_start_time, $not_working_end_time];
    		}
    		$newDay[] = [$start_time, $end_time];
    	}
    	
    	$mergedDay = [];
    	$merged_start_time = $merged_end_time = null;
    	for ($i = 0; $i < count($newDay); $i++){
    		$start_time = $newDay[$i][0];
    		$end_time = $newDay[$i][1];
    		if (!$merged_start_time){
    			$merged_start_time = $start_time;
    		}
    		if ($i+1 < count($newDay) && $newDay[$i+1][0] <= $end_time){
    			$merged_end_time = $newDay[$i+1][1];
    		}
    		elseif($i+1 < count($newDay)){
    			$mergedDay[] = [$merged_start_time, $merged_end_time];
    			$merged_start_time = $newDay[$i+1][0];
    		}
    		elseif($i == count($newDay)-1){
    			$mergedDay[] = [$merged_start_time, $end_time];
    		}
    		
    	}
    	
    	return $mergedDay;
    	
    }
    
    public function getSortedComments($parent_id=null){
    	$comments = Comment::find()->where(['target_id' => $this->user->id, 'parent_comment_id' => $parent_id])->orderBy(['datetime' => 'asc'])->all();
    	return $comments;
    }
   
    
    
}
