<?php

use yii\db\Migration;

/**
 * Handles the creation of table `doctor_payment`.
 * Has foreign keys to the tables:
 *
 * - `doctor`
 * - `user`
 */
class m171130_181359_create_doctor_payment_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('doctor_payment', [
            'id' => $this->primaryKey(),
            'invoice_url' => $this->string(),
            'invoice_name' => $this->string(),
            'paid_on' => $this->dateTime(),
            'status' => $this->integer(),
            'amount' => $this->decimal(10,2),
            'doctor_id' => $this->integer(),
            'user_id' => $this->integer(),
            'notes' => $this->text(),
            'receipt_url' => $this->string(),
            'receipt_name' => $this->string(),
        ]);

        // creates index for column `doctor_id`
        $this->createIndex(
            'idx-doctor_payment-doctor_id',
            'doctor_payment',
            'doctor_id'
        );

        // add foreign key for table `doctor`
        $this->addForeignKey(
            'fk-doctor_payment-doctor_id',
            'doctor_payment',
            'doctor_id',
            'doctor',
            'id',
            'CASCADE'
        );

        // creates index for column `user_id`
        $this->createIndex(
            'idx-doctor_payment-user_id',
            'doctor_payment',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-doctor_payment-user_id',
            'doctor_payment',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `doctor`
        $this->dropForeignKey(
            'fk-doctor_payment-doctor_id',
            'doctor_payment'
        );

        // drops index for column `doctor_id`
        $this->dropIndex(
            'idx-doctor_payment-doctor_id',
            'doctor_payment'
        );

        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-doctor_payment-user_id',
            'doctor_payment'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-doctor_payment-user_id',
            'doctor_payment'
        );

        $this->dropTable('doctor_payment');
    }
}
