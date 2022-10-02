<?php

use yii\db\Migration;

/**
 * Class m180108_185708_insert_card_payment_method
 */
class m180108_185708_insert_card_payment_method extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
		$this->insert('payment_method', ['id' => 1, 'name' => '{"en":"Credit/Debit Card","es":"Tarjeta de Crédito/Débito"}']);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('payment_method', ['id' => 1]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180108_185708_insert_card_payment_method cannot be reverted.\n";

        return false;
    }
    */
}
