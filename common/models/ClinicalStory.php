<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "clinical_story".
 *
 * @property integer $id
 * @property string $summary
 * @property string $registered_on
 * @property integer $patient_id
 * @property integer $doctor_id
 * @property integer $appointment_id
 * @property integer $clinical_story_type_id
 *
 * @property Appointment $appointment
 * @property Doctor $doctor
 * @property Patient $patient
 * @property ClinicalStoryAttachment[] $clinicalStoryAttachments
 * @property ClinicalStoryType $clinicalStoryType
 */
class ClinicalStory extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'clinical_story';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['summary'], 'string'],
            [['registered_on'], 'safe'],
            [['patient_id', 'doctor_id', 'appointment_id', 'clinical_story_type_id'], 'integer'],
            [['appointment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Appointment::className(), 'targetAttribute' => ['appointment_id' => 'id']],
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctor::className(), 'targetAttribute' => ['doctor_id' => 'id']],
            [['patient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Patient::className(), 'targetAttribute' => ['patient_id' => 'id']],
        	[['clinical_story_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClinicalStoryType::className(), 'targetAttribute' => ['clinical_story_type_id' => 'id']],
        		
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'summary' => Yii::t('app', 'Summary'),
            'registered_on' => Yii::t('app', 'Registered On'),
            'patient_id' => Yii::t('app', 'Patient ID'),
            'doctor_id' => Yii::t('app', 'Doctor ID'),
            'appointment_id' => Yii::t('app', 'Appointment ID'),
        	'clinical_story_type_id' => Yii::t('app', 'Record Type'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppointment()
    {
        return $this->hasOne(Appointment::className(), ['id' => 'appointment_id'])->inverseOf('clinicalStories');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id'])->inverseOf('clinicalStories');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatient()
    {
        return $this->hasOne(Patient::className(), ['id' => 'patient_id'])->inverseOf('clinicalStories');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClinicalStoryAttachments()
    {
        return $this->hasMany(ClinicalStoryAttachment::className(), ['clinical_story_id' => 'id'])->inverseOf('clinicalStory');
    }
    
    public function getClinicalStoryType(){
    	return $this->hasOne(ClinicalStoryType::className(), ['id' => 'clinical_story_type_id'])->inverseOf('clinicalStories');
    }
}
