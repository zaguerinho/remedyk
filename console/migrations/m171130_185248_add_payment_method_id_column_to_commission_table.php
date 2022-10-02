<?php

use yii\db\Migration;

/**
 * Handles adding payment_method_id to table `commission`.
 * Has foreign keys to the tables:
 *
 * - `payment_method`
 */
class m171130_185248_add_payment_method_id_column_to_commission_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
    	
    	$this->createTable('payment_method', [
    			'id' => $this->primaryKey(),
    			'name' => 'jsonb',
    	]);
    	
        $this->addColumn('commission', 'payment_method_id', $this->integer());

        // creates index for column `payment_method_id`
        $this->createIndex(
            'idx-commission-payment_method_id',
            'commission',
            'payment_method_id'
        );

        // add foreign key for table `payment_method`
        $this->addForeignKey(
            'fk-commission-payment_method_id',
            'commission',
            'payment_method_id',
            'payment_method',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `payment_method`
        $this->dropForeignKey(
            'fk-commission-payment_method_id',
            'commission'
        );

        // drops index for column `payment_method_id`
        $this->dropIndex(
            'idx-commission-payment_method_id',
            'commission'
        );

        $this->dropColumn('commission', 'payment_method_id');
        
        $this->dropTable('payment_method');
    }
}
