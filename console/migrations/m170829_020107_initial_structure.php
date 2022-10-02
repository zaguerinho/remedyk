<?php

use yii\db\Migration;

class m170829_020107_initial_structure extends Migration
{
    public function safeUp()
    {
    	
    	$tableOptions = null;
    	if ($this->db->driverName === 'mysql') {
    		// http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
    		$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    	}
    	
    	// Extra fields for users
		$this->addColumn('{{%user}}', 'phone', $this->string());
		$this->addColumn('{{%user}}', 'picture', $this->string());
		$this->addColumn('{{%user}}', 'birth_date', $this->date());
		$this->addColumn('{{%user}}', 'first_name', $this->string());
		$this->addColumn('{{%user}}', 'last_name', $this->string());
		
		$this->createTable('patient', [
				'id' => $this->primaryKey(),				
				'gender' => $this->string(),
				'promo_points' => $this->integer()			
		], $tableOptions);
		
		$this->createTable('doctor', [
				'id' => $this->primaryKey(),				
				'license_number' => $this->string(),			
				'resume' => $this->text(),
				'notes' => $this->text(),
				'bank_data' => $this->integer(),
				'appointment_price' => $this->decimal(10, 2),
				'appointment_anticipation' => $this->integer()				
		], $tableOptions);
		
		$this->createTable('doctor_picture', [
				'id' => $this->primaryKey(),
				'url' => $this->string(),
				'name' => $this->string(),
				'mime_type' => $this->string()
		], $tableOptions);
		
		$this->createTable('doctor_video', [
				'id' => $this->primaryKey(),
				'url' => $this->string(),
				'name' => $this->string(),
				'mime_type' => $this->string()
		], $tableOptions);
		
		$this->createTable('doctor_working_hour', [
				'id' => $this->primaryKey(),
				'week_day' => $this->integer(),
				'month_day' => $this->integer(),
				'month' => $this->integer(),
				'year' => $this->integer(),
				'start_time' => $this->time(),
				'end_time' => $this->time(),
				'is_working_hour' => $this->boolean(),
				'is_active' => $this->boolean()->defaultValue(true),
				'is_enabled' => $this->boolean()->defaultValue(false)
		], $tableOptions);
		
		$this->createTable('appointment', [
				'id' => $this->primaryKey(),
				'is_procedure' => $this->boolean(),
				'date' => $this->date(),
				'is_waiting' => $this->date(),
				'is_done' => $this->boolean(),
				'notes' => $this->text(),
				'is_active' => $this->boolean()->defaultValue(true),
				'price' => $this->decimal(10, 2),
				'status' => $this->integer(), // 1 -> Requested, 2 -> Confirmed, 3 -> Paid, 4 -> Happenning, 5 -> Done, 6 -> Rejected, 7 -> Cancelled by Doctor, 8 -> Cancelled by Patient, 9 -> Re-Scheduled
				'confirmation_datetime' => $this->dateTime(),
				'cancel_datetime' => $this->dateTime(),
				'is_quot' => $this->boolean()
		], $tableOptions);
		
		
		$this->createTable('clinical_story', [
				'id' => $this->primaryKey(),
				'summary' => $this->text(),
				'registered_on' => $this->date()->defaultExpression('now()')				
		], $tableOptions);
		
		$this->createTable('clinical_story_attachment', [
				'id' => $this->primaryKey(),
				'url' => $this->string(),
				'name' => $this->string(),
				'mime_type' => $this->string()
		], $tableOptions);		
		
		$this->createTable('prescription', [
				'id' => $this->primaryKey(),
				'datetime' => $this->dateTime()->defaultExpression('now()'),
				'notes' => $this->text(),
				'is_active' =>$this->boolean()->defaultValue(true)								
		], $tableOptions);
		
		$this->createTable('prescription_detail', [
				'id' => $this->primaryKey(),
				'frequency' => $this->string(),
				'lapse' => $this->string(),
				'notes' => $this->text(),
				'grammage' => $this->decimal(10, 2),
				'is_active' => $this->boolean()->defaultValue(true)
		], $tableOptions);
		
		$this->createTable('operating_room', [
				'id' => $this->primaryKey(),
				'name' => $this->string(),
				'is_active' =>$this->boolean()->defaultValue(true)
		], $tableOptions);
		
		$this->createTable('office', [
				'id' => $this->primaryKey(),
				'title' => $this->string(),
				'is_active' => $this->boolean()->defaultValue(true)
		], $tableOptions);
		
		$this->createTable('procedure', [
				'id' => $this->primaryKey(),
				'name' => 'jsonb',
				'is_treatment' => $this->boolean(),
				'is_surgery' => $this->boolean(),
				'is_active' => $this->boolean()->defaultValue(true)
		], $tableOptions);
		
		$this->createTable('specialty', [
				'id' => $this->primaryKey(),
				'name' => 'jsonb',
				'is_active' => $this->boolean()->defaultValue(true)				
		], $tableOptions);
		
		$this->createTable('tax_regime', [
				'id' => $this->primaryKey(),
				'name' => $this->string()
		], $tableOptions);
		
		$this->createTable('tax_data', [
				'id' => $this->primaryKey(),
				'name' => $this->string(),
				'rfc' => $this->string()
						
		], $tableOptions);
		
		$this->createTable('currency', [
				'id' => $this->primaryKey(),
				'name' => 'jsonb',
				'code' => $this->string(),
				'symbol' => $this->string()				
		], $tableOptions);
		
		$this->createTable('medicine', [
				'id' => $this->primaryKey(),
				'stores_equivalent_ids' => 'jsonb',
				'name' => 'jsonb',				
				'is_active' => $this->boolean()->defaultValue(true)				
		], $tableOptions);
		
		$this->createTable('configuration_category', [
				'id' => $this->primaryKey(),
				'name' => 'jsonb'				
		], $tableOptions);
		
		$this->createTable('configuration', [
				'id' => $this->primaryKey(),
				'param_code' => $this->string()->unique(),
				'param_label' => $this->string(),
				'param_value' => $this->string(),
				'app' => $this->integer()				
		], $tableOptions);
		
		$this->createTable('additional_service', [
				'id' => $this->primaryKey(),
				'name' => 'jsonb',
				'price' => $this->decimal(10, 2),
				'is_active' => $this->boolean()->defaultValue(true)				
		], $tableOptions);
		
		$this->createTable('search_history', [
				'id' => $this->primaryKey(),
				'query' => $this->string(),
				'datetime' => $this->dateTime()->defaultExpression('now()') 			
		], $tableOptions);
		
		$this->createTable('comment', [
				'id' => $this->primaryKey(),
				'datetime' => $this->dateTime()->defaultExpression('now()'),				
				'ban_reason' => $this->string(),
				'text' => $this->text()			
		], $tableOptions);
		
		$this->createTable('address', [
				'id' => $this->primaryKey(),
				'route' => $this->string(),// street
				'number' => $this->string(), // Number of House, apartment etcc
				'postal_code' => $this->string(),
				'country' => $this->string(),
				'administrative_area_level_1' => $this->string(),// state o province
				'administrative_area_level_3' => $this->string(),// municipality
				'locality' => $this->string(), // city
				'lat' => $this->string(),
				'lng' => $this->string(),
				'type' => $this->integer(), // 0=> if belong to a person, 1=> if belong to a branch
				'url_gmaps' => $this->string()
		], $tableOptions);
		
		$this->createTable('message', [
				'id' => $this->primaryKey(),
				'message' => $this->text(),
				'seen_at' => $this->dateTime(),
				'sent_at' => $this->dateTime(),
				'readed_at' => $this->dateTime(),
				'is_active' => $this->boolean()->defaultValue(true)
		], $tableOptions);
		
		$this->createTable('notification', [
				'id' => $this->primaryKey(),
				'text' => 'jsonb',
				'target_url' => $this->string(),
				'datetime' => $this->dateTime()->defaultExpression('now()'),
				'seen_at' => $this->dateTime(),
				'visited_at' => $this->dateTime()
		], $tableOptions);
		
		$this->createTable('qualification', [
				'id' => $this->primaryKey(),
				'rate' => $this->integer(),
				'is_active' => $this->boolean()->defaultValue(true)
		], $tableOptions);
    }

    public function safeDown()
    {
       	$this->dropTable('qualification');
       	$this->dropTable('notification');
       	$this->dropTable('message');
       	$this->dropTable('address');
       	$this->dropTable('comment');
       	$this->dropTable('search_history');
       	$this->dropTable('additional_service');  	
       	$this->dropTable('configuration');
       	$this->dropTable('configuration_category');
       	$this->dropTable('medicine');
       	$this->dropTable('currency');
       	$this->dropTable('tax_data');
       	$this->dropTable('tax_regime');
       	$this->dropTable('specialty');
       	$this->dropTable('procedure');
       	$this->dropTable('office');
       	$this->dropTable('operating_room');
       	$this->dropTable('prescription_detail');
       	$this->dropTable('prescription');
       	$this->dropTable('clinical_story_attachment');
       	$this->dropTable('clinical_story');
       	$this->dropTable('appointment');
       	$this->dropTable('doctor_working_hour');
       	$this->dropTable('doctor_video');
       	$this->dropTable('doctor_picture');
       	$this->dropTable('doctor');
       	$this->dropTable('patient');
       	
       	$this->dropColumn('{{%user}}', 'last_name');
       	$this->dropColumn('{{%user}}', 'first_name');
       	$this->dropColumn('{{%user}}', 'birth_date');
       	$this->dropColumn('{{%user}}', 'picture');
       	$this->dropColumn('{{%user}}', 'phone');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170829_020107_initial_structure cannot be reverted.\n";

        return false;
    }
    */
}
