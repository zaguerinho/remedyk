<?php

use yii\db\Migration;

class m171206_055652_insert_specialties_and_procedures extends Migration
{
    public function safeUp()
    {
		$this->delete('procedure2specialty', 'TRUE');
		$this->delete('specialty', 'TRUE');
		$this->delete('procedure', 'TRUE');
		
		$this->execute(file_get_contents(__DIR__ . '/data/specialty.sql'));
		$this->execute(file_get_contents(__DIR__ . '/data/procedure.sql'));
		$this->execute(file_get_contents(__DIR__ . '/data/procedure2specialty.sql'));
		
    }

    public function safeDown()
    {
    	$this->delete('procedure2specialty');
    	$this->delete('specialty');
    	$this->delete('procedure');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171206_055652_insert_specialties_and_procedures cannot be reverted.\n";

        return false;
    }
    */
}
