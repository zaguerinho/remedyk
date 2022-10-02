<?php

use yii\db\Migration;

class m171031_204127_enable_postgis_support extends Migration
{
    public function safeUp()
    {
		$this->execute('CREATE EXTENSION postgis;');
    }

    public function safeDown()
    {
    	$this->execute('DROP EXTENSION postgis;');
    }

}
