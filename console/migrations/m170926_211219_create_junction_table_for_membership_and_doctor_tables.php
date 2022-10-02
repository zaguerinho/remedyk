<?php

use yii\db\Migration;

/**
 * Handles the creation of table `membership2doctor`.
 * Has foreign keys to the tables:
 *
 * - `membership`
 * - `doctor`
 */
class m170926_211219_create_junction_table_for_membership_and_doctor_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('membership2doctor', [
            'membership_id' => $this->integer(),
            'doctor_id' => $this->integer(),
            'paid_on' => $this->date(),
            'active' => $this->boolean(),
            'contract_date' => $this->date(),
            'PRIMARY KEY(membership_id, doctor_id)',
        ]);

        // creates index for column `membership_id`
        $this->createIndex(
            'idx-membership2doctor-membership_id',
            'membership2doctor',
            'membership_id'
        );

        // add foreign key for table `membership`
        $this->addForeignKey(
            'fk-membership2doctor-membership_id',
            'membership2doctor',
            'membership_id',
            'membership',
            'id',
            'CASCADE'
        );

        // creates index for column `doctor_id`
        $this->createIndex(
            'idx-membership2doctor-doctor_id',
            'membership2doctor',
            'doctor_id'
        );

        // add foreign key for table `doctor`
        $this->addForeignKey(
            'fk-membership2doctor-doctor_id',
            'membership2doctor',
            'doctor_id',
            'doctor',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `membership`
        $this->dropForeignKey(
            'fk-membership2doctor-membership_id',
            'membership2doctor'
        );

        // drops index for column `membership_id`
        $this->dropIndex(
            'idx-membership2doctor-membership_id',
            'membership2doctor'
        );

        // drops foreign key for table `doctor`
        $this->dropForeignKey(
            'fk-membership2doctor-doctor_id',
            'membership2doctor'
        );

        // drops index for column `doctor_id`
        $this->dropIndex(
            'idx-membership2doctor-doctor_id',
            'membership2doctor'
        );

        $this->dropTable('membership2doctor');
    }
}
