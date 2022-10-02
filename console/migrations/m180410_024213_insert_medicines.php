<?php

use yii\db\Migration;

/**
 * Class m180410_024213_insert_medicines
 */
class m180410_024213_insert_medicines extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
    	$this->delete('medicine', 'TRUE');
    	$this->execute(file_get_contents(__DIR__ . '/data/medicine.sql'));
    	$this->update('medicine', ['stores_equivalent_ids' => '{"en":"", "es":""}'], '1=1');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
    	$this->delete('medicine');
    }

}
