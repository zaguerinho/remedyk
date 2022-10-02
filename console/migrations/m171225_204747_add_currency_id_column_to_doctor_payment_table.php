<?php

use yii\db\Migration;

/**
 * Handles adding currency_id to table `doctor_payment`.
 * Has foreign keys to the tables:
 *
 * - `currency`
 */
class m171225_204747_add_currency_id_column_to_doctor_payment_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('doctor_payment', 'currency_id', $this->integer());

        // creates index for column `currency_id`
        $this->createIndex(
            'idx-doctor_payment-currency_id',
            'doctor_payment',
            'currency_id'
        );

        // add foreign key for table `currency`
        $this->addForeignKey(
            'fk-doctor_payment-currency_id',
            'doctor_payment',
            'currency_id',
            'currency',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `currency`
        $this->dropForeignKey(
            'fk-doctor_payment-currency_id',
            'doctor_payment'
        );

        // drops index for column `currency_id`
        $this->dropIndex(
            'idx-doctor_payment-currency_id',
            'doctor_payment'
        );

        $this->dropColumn('doctor_payment', 'currency_id');
    }
}
