<?php

use yii\db\Migration;

class m170830_174603_add_relations extends Migration
{
    public function safeUp()
    {
    	$tableOptions = null;
    	if ($this->db->driverName === 'mysql') {
    		// http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
    		$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    	}
    	
    	/* Patient - User */
    	$this->addColumn('patient', 'user_id', $this->integer());
    	$this->createIndex(
    			'idx-patient-user_id',
    			'patient',
    			'user_id');
    	$this->addForeignKey(
    			'fk-patient-user_id',
    			'patient',
    			'user_id',
    			'{{%user}}',
    			'id',
    			'CASCADE');
    	
    	/* Patient - Address */
    	$this->addColumn('patient', 'address_id', $this->integer());
    	$this->createIndex(
    			'idx-patient-address_id',
    			'patient',
    			'address_id');
    	$this->addForeignKey(
    			'fk-patient-address_id',
    			'patient',
    			'address_id',
    			'address',
    			'id',
    			'CASCADE');
    	
    	/* Patient - Tax Data */
    	$this->addColumn('patient', 'tax_data_id', $this->integer());
    	$this->createIndex(
    			'idx-patient-tax_data_id',
    			'patient',
    			'tax_data_id');
    	$this->addForeignKey(
    			'fk-patient-tax_data_id',
    			'patient',
    			'tax_data_id',
    			'tax_data',
    			'id',
    			'CASCADE');
    	
    	/* Patient - Referred By */
    	$this->addColumn('patient', 'referred_by', $this->integer());
    	$this->createIndex(
    			'idx-patient-referred_by',
    			'patient',
    			'referred_by');
    	$this->addForeignKey(
    			'fk-patient-referred_by',
    			'patient',
    			'referred_by',
    			'{{%user}}',
    			'id',
    			'CASCADE');
    	
    	/* Doctor - User */
    	$this->addColumn('doctor', 'user_id', $this->integer());
    	$this->createIndex(
    			'idx-doctor-user_id',
    			'doctor',
    			'user_id');
    	$this->addForeignKey(
    			'fk-doctor-user_id',
    			'doctor',
    			'user_id',
    			'{{%user}}',
    			'id',
    			'CASCADE');
    	
    	/* Doctor - Address */
    	$this->addColumn('doctor', 'postal_address_id', $this->integer());
    	$this->createIndex(
    			'idx-doctor-postal_address_id',
    			'doctor',
    			'postal_address_id');
    	$this->addForeignKey(
    			'fk-doctor-postal_address_id',
    			'doctor',
    			'postal_address_id',
    			'address',
    			'id',
    			'CASCADE');
    	
    	/* Doctor - Tax Data */
    	$this->addColumn('doctor', 'tax_data_id', $this->integer());
    	$this->createIndex(
    			'idx-doctor-tax_data_id',
    			'doctor',
    			'tax_data_id');
    	$this->addForeignKey(
    			'fk-doctor-tax_data_id',
    			'doctor',
    			'tax_data_id',
    			'tax_data',
    			'id',
    			'CASCADE');
    	
    	/* Doctor - Currency */
    	$this->addColumn('doctor', 'currency_id', $this->integer());
    	$this->createIndex(
    			'idx-doctor-currency_id',
    			'doctor',
    			'currency_id');
    	$this->addForeignKey(
    			'fk-doctor-currency_id',
    			'doctor',
    			'currency_id',
    			'currency',
    			'id',
    			'CASCADE');
    	
    	/* Doctor Picture - Doctor */
    	$this->addColumn('doctor_picture', 'doctor_id', $this->integer());
    	$this->createIndex(
    			'idx-doctor_picture-doctor_id',
    			'doctor_picture',
    			'doctor_id');
    	$this->addForeignKey(
    			'fk-doctor_picture-doctor_id',
    			'doctor_picture',
    			'doctor_id',
    			'doctor',
    			'id',
    			'CASCADE');
    	
    	/* Doctor Video - Doctor */
    	$this->addColumn('doctor_video', 'doctor_id', $this->integer());
    	$this->createIndex(
    			'idx-doctor_video-doctor_id',
    			'doctor_video',
    			'doctor_id');
    	$this->addForeignKey(
    			'fk-doctor_video-doctor_id',
    			'doctor_video',
    			'doctor_id',
    			'doctor',
    			'id',
    			'CASCADE');
    	
    	/* Doctor Working Hour - Doctor */
    	$this->addColumn('doctor_working_hour', 'doctor_id', $this->integer());
    	$this->createIndex(
    			'idx-doctor_working_hour-doctor_id',
    			'doctor_working_hour',
    			'doctor_id');
    	$this->addForeignKey(
    			'fk-doctor_working_hour-doctor_id',
    			'doctor_working_hour',
    			'doctor_id',
    			'doctor',
    			'id',
    			'CASCADE');
    	
    	/* Doctor Working Hour - Office */
    	$this->addColumn('doctor_working_hour', 'office_id', $this->integer());
    	$this->createIndex(
    			'idx-doctor_working_hour-office_id',
    			'doctor_working_hour',
    			'office_id');
    	$this->addForeignKey(
    			'fk-doctor_working_hour-office_id',
    			'doctor_working_hour',
    			'office_id',
    			'office',
    			'id',
    			'CASCADE');
    	
    	/* Procedure - Doctor */
    	$this->createTable('procedure2doctor', [
    			'id' => $this->primaryKey(),
    			'procedure_id' => $this->integer(),
    			'doctor_id' => $this->integer(),
    			'currency_id' => $this->integer(),
    			'price' => $this->decimal(10, 2)
    	], $tableOptions);
    	$this->createIndex(
    			'idx-procedure2doctor-doctor_id',
    			'procedure2doctor',
    			'doctor_id');
    	$this->addForeignKey(
    			'fk-procedure2doctor-doctor_id',
    			'procedure2doctor',
    			'doctor_id',
    			'doctor',
    			'id',
    			'CASCADE');
    	$this->createIndex(
    			'idx-procedure2doctor-procedure_id',
    			'procedure2doctor',
    			'procedure_id');
    	$this->addForeignKey(
    			'fk-procedure2doctor-procedure_id',
    			'procedure2doctor',
    			'procedure_id',
    			'procedure',
    			'id',
    			'CASCADE');
    	$this->createIndex(
    			'idx-procedure2doctor-currency_id',
    			'procedure2doctor',
    			'currency_id');
    	$this->addForeignKey(
    			'fk-procedure2doctor-currency_id',
    			'procedure2doctor',
    			'currency_id',
    			'currency',
    			'id',
    			'CASCADE');
    	
    	/* Appointment - Patient */
    	$this->addColumn('appointment', 'patient_id', $this->integer());
    	$this->createIndex(
    			'idx-appointment-patient_id',
    			'appointment',
    			'patient_id');
    	$this->addForeignKey(
    			'fk-appointment-patient_id',
    			'appointment',
    			'patient_id',
    			'patient',
    			'id',
    			'CASCADE');
    	
    	/* Appointment - Doctor */
    	$this->addColumn('appointment', 'doctor_id', $this->integer());
    	$this->createIndex(
    			'idx-appointment-doctor_id',
    			'appointment',
    			'doctor_id');
    	$this->addForeignKey(
    			'fk-appointment-doctor_id',
    			'appointment',
    			'doctor_id',
    			'doctor',
    			'id',
    			'CASCADE');
    	
    	/* Appointment - Office */
    	$this->addColumn('appointment', 'office_id', $this->integer());
    	$this->createIndex(
    			'idx-appointment-office_id',
    			'appointment',
    			'office_id');
    	$this->addForeignKey(
    			'fk-appointment-office_id',
    			'appointment',
    			'office_id',
    			'office',
    			'id',
    			'CASCADE');
    	
    	/* Appointment - Operating Room */
    	$this->addColumn('appointment', 'operating_room_id', $this->integer());
    	$this->createIndex(
    			'idx-appointment-operating_room_id',
    			'appointment',
    			'operating_room_id');
    	$this->addForeignKey(
    			'fk-appointment-operating_room_id',
    			'appointment',
    			'operating_room_id',
    			'operating_room',
    			'id',
    			'CASCADE');
    	
    	/* Appointment - Doctor Procedure */
    	$this->addColumn('appointment', 'procedure2doctor_id', $this->integer());
    	$this->createIndex(
    			'idx-appointment-procedure2doctor_id',
    			'appointment',
    			'procedure2doctor_id');
    	$this->addForeignKey(
    			'fk-appointment-procedure2doctor_id',
    			'appointment',
    			'procedure2doctor_id',
    			'procedure2doctor',
    			'id',
    			'CASCADE');
    	
    	/* Appointment - Currency */
    	$this->addColumn('appointment', 'currency_id', $this->integer());
    	$this->createIndex(
    			'idx-appointment-currency_id',
    			'appointment',
    			'currency_id');
    	$this->addForeignKey(
    			'fk-appointment-currency_id',
    			'appointment',
    			'currency_id',
    			'currency',
    			'id',
    			'CASCADE');
    	
    	/* Clinical Story - Patient */
    	$this->addColumn('clinical_story', 'patient_id', $this->integer());
    	$this->createIndex(
    			'idx-clinical_story-patient_id',
    			'clinical_story',
    			'patient_id');
    	$this->addForeignKey(
    			'fk-clinical_story-patient_id',
    			'clinical_story',
    			'patient_id',
    			'patient',
    			'id',
    			'CASCADE');
    	
    	/* Clinical Story - Doctor */
    	$this->addColumn('clinical_story', 'doctor_id', $this->integer());
    	$this->createIndex(
    			'idx-clinical_story-doctor_id',
    			'clinical_story',
    			'doctor_id');
    	$this->addForeignKey(
    			'fk-clinical_story-doctor_id',
    			'clinical_story',
    			'doctor_id',
    			'doctor',
    			'id',
    			'CASCADE');
    	
    	/* Clinical Story - Appointment */
    	$this->addColumn('clinical_story', 'appointment_id', $this->integer());
    	$this->createIndex(
    			'idx-clinical_story-appointment_id',
    			'clinical_story',
    			'appointment_id');
    	$this->addForeignKey(
    			'fk-clinical_story-appointment_id',
    			'clinical_story',
    			'appointment_id',
    			'appointment',
    			'id',
    			'CASCADE');
    	
    	/* Clinical Story Attachment - Clinical Story */
    	$this->addColumn('clinical_story_attachment', 'clinical_story_id', $this->integer());
    	$this->createIndex(
    			'idx-clinical_story_attachment-clinical_story_id',
    			'clinical_story_attachment',
    			'clinical_story_id');
    	$this->addForeignKey(
    			'fk-clinical_story_attachment-clinical_story_id',
    			'clinical_story_attachment',
    			'clinical_story_id',
    			'clinical_story',
    			'id',
    			'CASCADE');
    	
    	/* Prescription - Patient */
    	$this->addColumn('prescription', 'patient_id', $this->integer());
    	$this->createIndex(
    			'idx-prescription-patient_id',
    			'prescription',
    			'patient_id');
    	$this->addForeignKey(
    			'fk-prescription-patient_id',
    			'prescription',
    			'patient_id',
    			'patient',
    			'id',
    			'CASCADE');
    	
    	/* Prescription - Doctor */
    	$this->addColumn('prescription', 'doctor_id', $this->integer());
    	$this->createIndex(
    			'idx-prescription-doctor_id',
    			'prescription',
    			'doctor_id');
    	$this->addForeignKey(
    			'fk-prescription-doctor_id',
    			'prescription',
    			'doctor_id',
    			'doctor',
    			'id',
    			'CASCADE');
    	
    	/* Prescription - Appointment */
    	$this->addColumn('prescription', 'appointment_id', $this->integer());
    	$this->createIndex(
    			'idx-prescription-appointment_id',
    			'prescription',
    			'appointment_id');
    	$this->addForeignKey(
    			'fk-prescription-appointment_id',
    			'prescription',
    			'appointment_id',
    			'appointment',
    			'id',
    			'CASCADE');
    	
    	/* Prescription Detail - Prescription */
    	$this->addColumn('prescription_detail', 'prescription_id', $this->integer());
    	$this->createIndex(
    			'idx-prescription_detail-prescription_id',
    			'prescription_detail',
    			'prescription_id');
    	$this->addForeignKey(
    			'fk-prescription_detail-prescription_id',
    			'prescription_detail',
    			'prescription_id',
    			'prescription',
    			'id',
    			'CASCADE');
    	
    	/* Operating Room - Doctor */
    	$this->addColumn('operating_room', 'doctor_id', $this->integer());
    	$this->createIndex(
    			'idx-operating_room-doctor_id',
    			'operating_room',
    			'doctor_id');
    	$this->addForeignKey(
    			'fk-operating_room-doctor_id',
    			'operating_room',
    			'doctor_id',
    			'doctor',
    			'id',
    			'CASCADE');
    	
    	/* Operating Room - Address */
    	$this->addColumn('operating_room', 'address_id', $this->integer());
    	$this->createIndex(
    			'idx-operating_room-address_id',
    			'operating_room',
    			'address_id');
    	$this->addForeignKey(
    			'fk-operating_room-address_id',
    			'operating_room',
    			'address_id',
    			'address',
    			'id',
    			'CASCADE');
    	
    	/* Office - Doctor */
    	$this->addColumn('office', 'doctor_id', $this->integer());
    	$this->createIndex(
    			'idx-office-doctor_id',
    			'office',
    			'doctor_id');
    	$this->addForeignKey(
    			'fk-office-doctor_id',
    			'office',
    			'doctor_id',
    			'doctor',
    			'id',
    			'CASCADE');
    	
    	/* Office - Address */
    	$this->addColumn('office', 'address_id', $this->integer());
    	$this->createIndex(
    			'idx-office-address_id',
    			'office',
    			'address_id');
    	$this->addForeignKey(
    			'fk-office-address_id',
    			'office',
    			'address_id',
    			'address',
    			'id',
    			'CASCADE');
    	
    	/* Tax data - Address */
    	$this->addColumn('tax_data', 'address_id', $this->integer());
    	$this->createIndex(
    			'idx-tax_data-address_id',
    			'tax_data',
    			'address_id');
    	$this->addForeignKey(
    			'fk-tax_data-address_id',
    			'tax_data',
    			'address_id',
    			'address',
    			'id',
    			'CASCADE');
    	
    	/* Tax data - Tax Regime */
    	$this->addColumn('tax_data', 'tax_regime_id', $this->integer());
    	$this->createIndex(
    			'idx-tax_data-tax_regime_id',
    			'tax_data',
    			'tax_regime_id');
    	$this->addForeignKey(
    			'fk-tax_data-tax_regime_id',
    			'tax_data',
    			'tax_regime_id',
    			'tax_regime',
    			'id',
    			'CASCADE');
    	
    	/* Configuration - Configuration Category */
    	$this->addColumn('configuration', 'configuration_category_id', $this->integer());
    	$this->createIndex(
    			'idx-configuration-configuration_category_id',
    			'configuration',
    			'configuration_category_id');
    	$this->addForeignKey(
    			'fk-configuration-configuration_category_id',
    			'configuration',
    			'configuration_category_id',
    			'configuration_category',
    			'id',
    			'CASCADE');
    	
    	/* Search History - Patient */
    	$this->addColumn('search_history', 'patient_id', $this->integer());
    	$this->createIndex(
    			'idx-search_history-patient_id',
    			'search_history',
    			'patient_id');
    	$this->addForeignKey(
    			'fk-search_history-patient_id',
    			'search_history',
    			'patient_id',
    			'patient',
    			'id',
    			'CASCADE');
    	
    	/* Comment - From */
    	$this->addColumn('comment', 'from_id', $this->integer());
    	$this->createIndex(
    			'idx-comment-from_id',
    			'comment',
    			'from_id');
    	$this->addForeignKey(
    			'fk-comment-from_id',
    			'comment',
    			'from_id',
    			'{{%user}}',
    			'id',
    			'CASCADE');
    	
    	/* Comment - Target */
    	$this->addColumn('comment', 'target_id', $this->integer());
    	$this->createIndex(
    			'idx-comment-target_id',
    			'comment',
    			'target_id');
    	$this->addForeignKey(
    			'fk-comment-target_id',
    			'comment',
    			'target_id',
    			'{{%user}}',
    			'id',
    			'CASCADE');
    
    	/* Comment - Partent Comment */
    	$this->addColumn('comment', 'parent_comment_id', $this->integer());
    	$this->createIndex(
    			'idx-comment-parent_comment_id',
    			'comment',
    			'parent_comment_id');
    	$this->addForeignKey(
    			'fk-comment-parent_comment_id',
    			'comment',
    			'parent_comment_id',
    			'comment',
    			'id',
    			'CASCADE');
    	
    	/* Comment - Approved By */
    	$this->addColumn('comment', 'approved_by', $this->integer());
    	$this->createIndex(
    			'idx-comment-approved_by',
    			'comment',
    			'approved_by');
    	$this->addForeignKey(
    			'fk-comment-approved_by',
    			'comment',
    			'approved_by',
    			'{{%user}}',
    			'id',
    			'CASCADE');
    	
    	/* Comment - Banned By */
    	$this->addColumn('comment', 'banned_by', $this->integer());
    	$this->createIndex(
    			'idx-comment-banned_by',
    			'comment',
    			'banned_by');
    	$this->addForeignKey(
    			'fk-comment-banned_by',
    			'comment',
    			'banned_by',
    			'{{%user}}',
    			'id',
    			'CASCADE');
    	
    	/* Message - From */
    	$this->addColumn('message', 'from_id', $this->integer());
    	$this->createIndex(
    			'idx-message-from_id',
    			'message',
    			'from_id');
    	$this->addForeignKey(
    			'fk-message-from_id',
    			'message',
    			'from_id',
    			'{{%user}}',
    			'id',
    			'CASCADE');
    	
    	/* Message - To */
    	$this->addColumn('message', 'to_id', $this->integer());
    	$this->createIndex(
    			'idx-message-to_id',
    			'message',
    			'to_id');
    	$this->addForeignKey(
    			'fk-message-to_id',
    			'message',
    			'to_id',
    			'{{%user}}',
    			'id',
    			'CASCADE');
    	
    	/* Notification - Target */
    	$this->addColumn('notification', 'target_id', $this->integer());
    	$this->createIndex(
    			'idx-notification-target_id',
    			'notification',
    			'target_id');
    	$this->addForeignKey(
    			'fk-notification-target_id',
    			'notification',
    			'target_id',
    			'{{%user}}',
    			'id',
    			'CASCADE');
    	
    	/* Qualification - Doctor */
    	$this->addColumn('qualification', 'doctor_id', $this->integer());
    	$this->createIndex(
    			'idx-qualification-doctor_id',
    			'qualification',
    			'doctor_id');
    	$this->addForeignKey(
    			'fk-qualification-doctor_id',
    			'qualification',
    			'doctor_id',
    			'doctor',
    			'id',
    			'CASCADE');
    	
    	/* Qualification - Patient */
    	$this->addColumn('qualification', 'patient_id', $this->integer());
    	$this->createIndex(
    			'idx-qualification-patient_id',
    			'qualification',
    			'patient_id');
    	$this->addForeignKey(
    			'fk-qualification-patient_id',
    			'qualification',
    			'patient_id',
    			'patient',
    			'id',
    			'CASCADE');
    	
    	/* Specialty - Doctor */
    	$this->createTable('specialty2doctor', [
    			'id' => $this->primaryKey(),
    			'specialty_id' => $this->integer(),
    			'doctor_id' => $this->integer(),
    			'is_active' => $this->boolean()->defaultValue(true),
    			'is_main' => $this->boolean()->defaultValue(false)
    	], $tableOptions);
    	$this->createIndex(
    			'idx-specialty2doctor-specialty_id',
    			'specialty2doctor',
    			'specialty_id');
    	$this->addForeignKey(
    			'fk-specialty2doctor-specialty_id',
    			'specialty2doctor',
    			'specialty_id',
    			'specialty',
    			'id',
    			'CASCADE');
    	$this->createIndex(
    			'idx-specialty2doctor-doctor_id',
    			'specialty2doctor',
    			'doctor_id');
    	$this->addForeignKey(
    			'fk-specialty2doctor-doctor_id',
    			'specialty2doctor',
    			'doctor_id',
    			'doctor',
    			'id',
    			'CASCADE');
    	
    	/* Procedure - Specialty */
    	$this->createTable('procedure2specialty', [
    			'id' => $this->primaryKey(),
    			'procedure_id' => $this->integer(),
    			'specialty_id' => $this->integer(),
    			'is_active' => $this->boolean()->defaultValue(true),
    	], $tableOptions);
    	$this->createIndex(
    			'idx-procedure2specialty-procedure_id',
    			'procedure2specialty',
    			'procedure_id');
    	$this->addForeignKey(
    			'fk-procedure2specialty-procedure_id',
    			'procedure2specialty',
    			'procedure_id',
    			'procedure',
    			'id',
    			'CASCADE');
    	$this->createIndex(
    			'idx-procedure2specialty-specialty_id',
    			'procedure2specialty',
    			'specialty_id');
    	$this->addForeignKey(
    			'fk-procedure2specialty-specialty_id',
    			'procedure2specialty',
    			'specialty_id',
    			'specialty',
    			'id',
    			'CASCADE');
    	
    	/* Additional Service - Appointment */
    	$this->createTable('additional_service2appointment', [
    			'id' => $this->primaryKey(),
    			'additional_service_id' => $this->integer(),
    			'appointment_id' => $this->integer(),
    			'price_assigned_by' => $this->integer(),
    			'notes' => $this->text(),
    			'price' => $this->decimal(10, 2)
    	], $tableOptions);
    	$this->createIndex(
    			'idx-additional_service2appointment-additional_service_id',
    			'additional_service2appointment',
    			'additional_service_id');
    	$this->addForeignKey(
    			'fk-additional_service2appointment-additional_service_id',
    			'additional_service2appointment',
    			'additional_service_id',
    			'additional_service',
    			'id',
    			'CASCADE');
    	$this->createIndex(
    			'idx-additional_service2appointment-appointment_id',
    			'additional_service2appointment',
    			'appointment_id');
    	$this->addForeignKey(
    			'fk-additional_service2appointment-appointment_id',
    			'additional_service2appointment',
    			'appointment_id',
    			'appointment',
    			'id',
    			'CASCADE');
    	$this->createIndex(
    			'idx-additional_service2appointment-price_assigned_by',
    			'additional_service2appointment',
    			'price_assigned_by');
    	$this->addForeignKey(
    			'fk-additional_service2appointment-price_assigned_by',
    			'additional_service2appointment',
    			'price_assigned_by',
    			'{{%user}}',
    			'id',
    			'CASCADE');
    	
    }

    public function safeDown()
    {
    	/* Procedure - Specialty */
    	$this->dropForeignKey(
    			'fk-additional_service2appointment-price_assigned_by',
    			'additional_service2appointment'
    			);
    	$this->dropIndex(
    			'idx-additional_service2appointment-price_assigned_by',
    			'additional_service2appointment'
    			);
    	$this->dropForeignKey(
    			'fk-additional_service2appointment-appointment_id',
    			'additional_service2appointment'
    			);
    	$this->dropIndex(
    			'idx-additional_service2appointment-appointment_id',
    			'additional_service2appointment'
    			);
    	$this->dropForeignKey(
    			'fk-additional_service2appointment-additional_service_id',
    			'additional_service2appointment'
    			);
    	$this->dropIndex(
    			'idx-additional_service2appointment-additional_service_id',
    			'additional_service2appointment'
    			);
    	$this->dropTable('additional_service2appointment');
    	
    	
    	/* Procedure - Specialty */
    	$this->dropForeignKey(
    			'fk-procedure2specialty-specialty_id',
    			'procedure2specialty'
    			);
    	$this->dropIndex(
    			'idx-procedure2specialty-specialty_id',
    			'procedure2specialty'
    			);
    	$this->dropForeignKey(
    			'fk-procedure2specialty-procedure_id',
    			'procedure2specialty'
    			);
    	$this->dropIndex(
    			'idx-procedure2specialty-procedure_id',
    			'procedure2specialty'
    			);
    	$this->dropTable('procedure2specialty');
    	
    	/* Specialty - Doctor */
    	$this->dropForeignKey(
    			'fk-specialty2doctor-doctor_id',
    			'specialty2doctor'
    			);
    	$this->dropIndex(
    			'idx-specialty2doctor-doctor_id',
    			'specialty2doctor'
    			);
    	$this->dropForeignKey(
    			'fk-specialty2doctor-specialty_id',
    			'specialty2doctor'
    			);
    	$this->dropIndex(
    			'idx-specialty2doctor-specialty_id',
    			'specialty2doctor'
    			);
    	$this->dropTable('specialty2doctor');
    	
    	
    	/* Qualification - Patient */
    	$this->dropForeignKey(
    			'fk-qualification-patient_id',
    			'qualification'
    			);
    	$this->dropIndex(
    			'idx-qualification-patient_id',
    			'qualification'
    			);
    	$this->dropColumn('qualification', 'patient_id');
    	
    	/* Qualification - Doctor */
    	$this->dropForeignKey(
    			'fk-qualification-doctor_id',
    			'qualification'
    			);
    	$this->dropIndex(
    			'idx-qualification-doctor_id',
    			'qualification'
    			);
    	$this->dropColumn('qualification', 'doctor_id');
    	
    	/* Notification - Target */
    	$this->dropForeignKey(
    			'fk-notification-target_id',
    			'notification'
    			);
    	$this->dropIndex(
    			'idx-notification-target_id',
    			'notification'
    			);
    	$this->dropColumn('notification', 'target_id');
    	
    	/* Message - To */
    	$this->dropForeignKey(
    			'fk-message-to_id',
    			'message'
    			);
    	$this->dropIndex(
    			'idx-message-to_id',
    			'message'
    			);
    	$this->dropColumn('message', 'to_id');
    	
    	/* Message - From */
    	$this->dropForeignKey(
    			'fk-message-from_id',
    			'message'
    			);
    	$this->dropIndex(
    			'idx-message-from_id',
    			'message'
    			);
    	$this->dropColumn('message', 'from_id');
    	
    	/* Comment - Banned By */
    	$this->dropForeignKey(
    			'fk-comment-banned_by',
    			'comment'
    			);
    	$this->dropIndex(
    			'idx-comment-banned_by',
    			'comment'
    			);
    	$this->dropColumn('comment', 'banned_by');
    	
    	/* Comment - Approved By */
    	$this->dropForeignKey(
    			'fk-comment-approved_by',
    			'comment'
    			);
    	$this->dropIndex(
    			'idx-comment-approved_by',
    			'comment'
    			);
    	$this->dropColumn('comment', 'approved_by');
    	
    	/* Comment - Parent Comment */
    	$this->dropForeignKey(
    			'fk-comment-parent_comment_id',
    			'comment'
    			);
    	$this->dropIndex(
    			'idx-comment-parent_comment_id',
    			'comment'
    			);
    	$this->dropColumn('comment', 'parent_comment_id');
    	
    	/* Comment - From */
    	$this->dropForeignKey(
    			'fk-comment-target_id',
    			'comment'
    			);
    	$this->dropIndex(
    			'idx-comment-target_id',
    			'comment'
    			);
    	$this->dropColumn('comment', 'target_id');
    	
    	/* Comment - From */
    	$this->dropForeignKey(
    			'fk-comment-from_id',
    			'comment'
    			);
    	$this->dropIndex(
    			'idx-comment-from_id',
    			'comment'
    			);
    	$this->dropColumn('comment', 'from_id');
    	
    	/* Search History - Patient */
    	$this->dropForeignKey(
    			'fk-search_history-patient_id',
    			'search_history'
    			);
    	$this->dropIndex(
    			'idx-search_history-patient_id',
    			'search_history'
    			);
    	$this->dropColumn('search_history', 'patient_id');
    	
    	/* Configuration - Configuration Category */
    	$this->dropForeignKey(
    			'fk-configuration-configuration_category_id',
    			'configuration'
    			);
    	$this->dropIndex(
    			'idx-configuration-configuration_category_id',
    			'configuration'
    			);
    	$this->dropColumn('configuration', 'configuration_category_id');
    	
    	/* Tax Data - Tax Regime */
    	$this->dropForeignKey(
    			'fk-tax_data-tax_regime_id',
    			'tax_data'
    			);
    	$this->dropIndex(
    			'idx-tax_data-tax_regime_id',
    			'tax_data'
    			);
    	$this->dropColumn('tax_data', 'tax_regime_id');
    	
    	/* Tax Data - Address */
    	$this->dropForeignKey(
    			'fk-tax_data-address_id',
    			'tax_data'
    			);
    	$this->dropIndex(
    			'idx-tax_data-address_id',
    			'tax_data'
    			);
    	$this->dropColumn('tax_data', 'address_id');
    	
    	/* Office - Address */
    	$this->dropForeignKey(
    			'fk-office-address_id',
    			'office'
    			);
    	$this->dropIndex(
    			'idx-office-address_id',
    			'office'
    			);
    	$this->dropColumn('office', 'address_id');
    	
    	/* Offic - Doctor */
    	$this->dropForeignKey(
    			'fk-office-doctor_id',
    			'office'
    			);
    	$this->dropIndex(
    			'idx-office-doctor_id',
    			'office'
    			);
    	$this->dropColumn('office', 'doctor_id');
    	
    	/* Operating Room - Address */
    	$this->dropForeignKey(
    			'fk-operating_room-address_id',
    			'operating_room'
    			);
    	$this->dropIndex(
    			'idx-operating_room-address_id',
    			'operating_room'
    			);
    	$this->dropColumn('operating_room', 'address_id');
    	
    	/* Operating Room - Doctor */
    	$this->dropForeignKey(
    			'fk-operating_room-doctor_id',
    			'operating_room'
    			);
    	$this->dropIndex(
    			'idx-operating_room-doctor_id',
    			'operating_room'
    			);
    	$this->dropColumn('operating_room', 'doctor_id');
    	
    	/* Prescription Detail - Prescription */
    	$this->dropForeignKey(
    			'fk-prescription_detail-prescription_id',
    			'prescription_detail'
    			);
    	$this->dropIndex(
    			'idx-prescription_detail-prescription_id',
    			'prescription_detail'
    			);
    	$this->dropColumn('prescription_detail', 'prescription_id');
    	
    	/* Prescription - Appointment */
    	$this->dropForeignKey(
    			'fk-prescription-appointment_id',
    			'prescription'
    			);
    	$this->dropIndex(
    			'idx-prescription-appointment_id',
    			'prescription'
    			);
    	$this->dropColumn('prescription', 'appointment_id');
    	
    	/* Prescription - Doctor */
    	$this->dropForeignKey(
    			'fk-prescription-doctor_id',
    			'prescription'
    			);
    	$this->dropIndex(
    			'idx-prescription-doctor_id',
    			'prescription'
    			);
    	$this->dropColumn('prescription', 'doctor_id');
    	
    	/* Prescription - Patient */
    	$this->dropForeignKey(
    			'fk-prescription-patient_id',
    			'prescription'
    			);
    	$this->dropIndex(
    			'idx-prescription-patient_id',
    			'prescription'
    			);
    	$this->dropColumn('prescription', 'patient_id');
    	
    	/* Clinical Story Attachment - Clinical Story */
    	$this->dropForeignKey(
    			'fk-clinical_story_attachment-clinical_story_id',
    			'clinical_story_attachment'
    			);
    	$this->dropIndex(
    			'idx-clinical_story_attachment-clinical_story_id',
    			'clinical_story_attachment'
    			);
    	$this->dropColumn('clinical_story_attachment', 'clinical_story_id');
    	
    	/* Clinical Story - Appointment */
    	$this->dropForeignKey(
    			'fk-clinical_story-appointment_id',
    			'clinical_story'
    			);
    	$this->dropIndex(
    			'idx-clinical_story-appointment_id',
    			'clinical_story'
    			);
    	$this->dropColumn('clinical_story', 'appointment_id');
    	
    	/* Clinical Story - Doctor */
    	$this->dropForeignKey(
    			'fk-clinical_story-doctor_id',
    			'clinical_story'
    			);
    	$this->dropIndex(
    			'idx-clinical_story-doctor_id',
    			'clinical_story'
    			);
    	$this->dropColumn('clinical_story', 'doctor_id');
    	
    	/* Clinical Story - Patient */
    	$this->dropForeignKey(
    			'fk-clinical_story-patient_id',
    			'clinical_story'
    			);
    	$this->dropIndex(
    			'idx-clinical_story-patient_id',
    			'clinical_story'
    			);
    	$this->dropColumn('clinical_story', 'patient_id');
    	
    	/* Appointment - Currency */
    	$this->dropForeignKey(
    			'fk-appointment-currency_id',
    			'appointment'
    			);
    	$this->dropIndex(
    			'idx-appointment-currency_id',
    			'appointment'
    			);
    	$this->dropColumn('appointment', 'currency_id');
    	
    	/* Appointment - Doctor Procedure */
    	$this->dropForeignKey(
    			'fk-appointment-procedure2doctor_id',
    			'appointment'
    			);
    	$this->dropIndex(
    			'idx-appointment-procedure2doctor_id',
    			'appointment'
    			);
    	$this->dropColumn('appointment', 'procedure2doctor_id');
    	
    	/* Appointment - Operating Room */
    	$this->dropForeignKey(
    			'fk-appointment-operating_room_id',
    			'appointment'
    			);
    	$this->dropIndex(
    			'idx-appointment-operating_room_id',
    			'appointment'
    			);
    	$this->dropColumn('appointment', 'operating_room_id');
    	
    	/* Appointment - Office */
    	$this->dropForeignKey(
    			'fk-appointment-office_id',
    			'appointment'
    			);
    	$this->dropIndex(
    			'idx-appointment-office_id',
    			'appointment'
    			);
    	$this->dropColumn('appointment', 'office_id');
    	
    	/* Appointment - Doctor */
    	$this->dropForeignKey(
    			'fk-appointment-doctor_id',
    			'appointment'
    			);
    	$this->dropIndex(
    			'idx-appointment-doctor_id',
    			'appointment'
    			);
    	$this->dropColumn('appointment', 'doctor_id');
    	
    	/* Appointment - Patient */
    	$this->dropForeignKey(
    			'fk-appointment-patient_id',
    			'appointment'
    			);
    	$this->dropIndex(
    			'idx-appointment-patient_id',
    			'appointment'
    			);
    	$this->dropColumn('appointment', 'patient_id');
    	
    	/* Procedure - Doctor */
    	$this->dropForeignKey(
    			'fk-procedure2doctor-currency_id',
    			'procedure2doctor'
    			);
    	$this->dropIndex(
    			'idx-procedure2doctor-currency_id',
    			'procedure2doctor'
    			);
    	$this->dropForeignKey(
    			'fk-procedure2doctor-procedure_id',
    			'procedure2doctor'
    			);
    	$this->dropIndex(
    			'idx-procedure2doctor-procedure_id',
    			'procedure2doctor'
    			);
    	$this->dropForeignKey(
    			'fk-procedure2doctor-doctor_id',
    			'procedure2doctor'
    			);
    	$this->dropIndex(
    			'idx-procedure2doctor-doctor_id',
    			'procedure2doctor'
    			);
    	$this->dropTable('procedure2doctor');

    	
    	/* Doctor Working Hour - Office */
    	$this->dropForeignKey(
    			'fk-doctor_working_hour-office_id',
    			'doctor_working_hour'
    			);
    	$this->dropIndex(
    			'idx-doctor_working_hour-office_id',
    			'doctor_working_hour'
    			);
    	$this->dropColumn('doctor_working_hour', 'office_id');
    	
    	/* Doctor Working Hour - Doctor */
    	$this->dropForeignKey(
    			'fk-doctor_working_hour-doctor_id',
    			'doctor_working_hour'
    			);
    	$this->dropIndex(
    			'idx-doctor_working_hour-doctor_id',
    			'doctor_working_hour'
    			);
    	$this->dropColumn('doctor_working_hour', 'doctor_id');
    	
    	/* Doctor Video - Doctor */
    	$this->dropForeignKey(
    			'fk-doctor_video-doctor_id',
    			'doctor_video'
    			);
    	$this->dropIndex(
    			'idx-doctor_video-doctor_id',
    			'doctor_video'
    			);
    	$this->dropColumn('doctor_video', 'doctor_id');
    	
    	/* Doctor Picture - Doctor */
    	$this->dropForeignKey(
    			'fk-doctor_picture-doctor_id',
    			'doctor_picture'
    			);
    	$this->dropIndex(
    			'idx-doctor_picture-doctor_id',
    			'doctor_picture'
    			);
    	$this->dropColumn('doctor_picture', 'doctor_id');
    	
    	/* Doctor - Currency */
    	$this->dropForeignKey(
    			'fk-doctor-currency_id',
    			'doctor'
    			);
    	$this->dropIndex(
    			'idx-doctor-currency_id',
    			'doctor'
    			);
    	$this->dropColumn('doctor', 'currency_id');
    	
    	/* Doctor - Tax Data */
    	$this->dropForeignKey(
    			'fk-doctor-tax_data_id',
    			'doctor'
    			);
    	$this->dropIndex(
    			'idx-doctor-tax_data_id',
    			'doctor'
    			);
    	$this->dropColumn('doctor', 'tax_data_id');
    	
    	/* Doctor - Address */
    	$this->dropForeignKey(
    			'fk-doctor-postal_address_id',
    			'doctor'
    			);
    	$this->dropIndex(
    			'idx-doctor-postal_address_id',
    			'doctor'
    			);
    	$this->dropColumn('doctor', 'postal_address_id');
    	
    	/* Doctor - User */
    	$this->dropForeignKey(
    			'fk-doctor-user_id',
    			'doctor'
    			);
    	$this->dropIndex(
    			'idx-doctor-user_id',
    			'doctor'
    			);
    	$this->dropColumn('doctor', 'user_id');
    	
    	/* Patient - Referred By */
    	$this->dropForeignKey(
    			'fk-patient-referred_by',
    			'patient'
    			);
    	$this->dropIndex(
    			'idx-patient-referred_by',
    			'patient'
    			);
    	$this->dropColumn('patient', 'referred_by');
    	
    	/* Patient - Tax Data */
    	$this->dropForeignKey(
    			'fk-patient-tax_data_id',
    			'patient'
    			);
    	$this->dropIndex(
    			'idx-patient-tax_data_id',
    			'patient'
    			);
    	$this->dropColumn('patient', 'tax_data_id');
    	  	
    	/* Patient - Address */
    	$this->dropForeignKey(
    			'fk-patient-address_id',
    			'patient'
    			);
    	$this->dropIndex(
    			'idx-patient-address_id',
    			'patient'
    			);
    	$this->dropColumn('patient', 'address_id');
    	
    	/* Patient - User */
    	$this->dropForeignKey(
    			'fk-patient-user_id',
    			'patient'
    			);
    	$this->dropIndex(
    			'idx-patient-user_id',
    			'patient'
    			);
    	$this->dropColumn('patient', 'user_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170830_174603_add_relations cannot be reverted.\n";

        return false;
    }
    */
}
