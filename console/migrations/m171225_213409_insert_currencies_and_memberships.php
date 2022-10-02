<?php

use yii\db\Migration;

/**
 * Class m171225_213409_insert_currencies_and_memberships
 */
class m171225_213409_insert_currencies_and_memberships extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
    	$this->delete('membership', 'TRUE');
		$this->delete('currency', 'TRUE');
			
		$this->execute(file_get_contents(__DIR__.'/data/currency.sql'));
		$this->execute(file_get_contents(__DIR__.'/data/membership.sql'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
    	$this->delete('membership', 'TRUE');
    	$this->delete('currency', 'TRUE');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171225_213409_insert_currencies_and_memberships cannot be reverted.\n";

        return false;
    }
    */
}
