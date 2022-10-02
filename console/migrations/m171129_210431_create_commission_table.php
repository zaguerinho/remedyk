<?php

use yii\db\Migration;

/**
 * Handles the creation of table `commission`.
 * Has foreign keys to the tables:
 *
 * - `appointment`
 */
class m171129_210431_create_commission_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('commission', [
            'id' => $this->primaryKey(),
            'appointment_id' => $this->integer(),
            'amount' => $this->decimal(10,2),
            'percent' => $this->decimal(10,2),
            'paid_on' => $this->date(),
            'status' => $this->integer(),
        ]);

        // creates index for column `appointment_id`
        $this->createIndex(
            'idx-commission-appointment_id',
            'commission',
            'appointment_id'
        );

        // add foreign key for table `appointment`
        $this->addForeignKey(
            'fk-commission-appointment_id',
            'commission',
            'appointment_id',
            'appointment',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `appointment`
        $this->dropForeignKey(
            'fk-commission-appointment_id',
            'commission'
        );

        // drops index for column `appointment_id`
        $this->dropIndex(
            'idx-commission-appointment_id',
            'commission'
        );

        $this->dropTable('commission');
    }
}
