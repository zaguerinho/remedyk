<?php

use yii\db\Migration;

/**
 * Handles adding doctor_payment_id to table `commission`.
 * Has foreign keys to the tables:
 *
 * - `commission`
 */
class m171130_181656_add_payment_id_column_to_commission_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('commission', 'doctor_payment_id', $this->integer());

        // creates index for column `doctor_payment_id`
        $this->createIndex(
            'idx-commission-doctor_payment_id',
            'commission',
            'doctor_payment_id'
        );

        // add foreign key for table `commission`
        $this->addForeignKey(
            'fk-commission-doctor_payment_id',
            'commission',
            'doctor_payment_id',
            'doctor_payment',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `commission`
        $this->dropForeignKey(
            'fk-commission-doctor_payment_id',
            'commission'
        );

        // drops index for column `doctor_payment_id`
        $this->dropIndex(
            'idx-commission-doctor_payment_id',
            'commission'
        );

        $this->dropColumn('commission', 'doctor_payment_id');
    }
}
