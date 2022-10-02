<?php

use yii\db\Migration;

/**
 * Handles the creation of table `certification2doctor`.
 * Has foreign keys to the tables:
 *
 * - `doctor`
 * - `certificate`
 */
class m171115_192829_create_certification2doctor_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('certification2doctor', [
            'id' => $this->primaryKey(),
            'doctor_id' => $this->integer(),
            'certification_id' => $this->integer(),
        ]);

        // creates index for column `doctor_id`
        $this->createIndex(
            'idx-certification2doctor-doctor_id',
            'certification2doctor',
            'doctor_id'
        );

        // add foreign key for table `doctor`
        $this->addForeignKey(
            'fk-certification2doctor-doctor_id',
            'certification2doctor',
            'doctor_id',
            'doctor',
            'id',
            'CASCADE'
        );

        // creates index for column `certificate_id`
        $this->createIndex(
            'idx-certification2doctor-certification_id',
            'certification2doctor',
            'certification_id'
        );

        // add foreign key for table `certificate`
        $this->addForeignKey(
            'fk-certification2doctor-certification_id',
            'certification2doctor',
            'certification_id',
            'certification',
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
            'fk-certification2doctor-doctor_id',
            'certification2doctor'
        );

        // drops index for column `doctor_id`
        $this->dropIndex(
            'idx-certification2doctor-doctor_id',
            'certification2doctor'
        );

        // drops foreign key for table `certificate`
        $this->dropForeignKey(
            'fk-certification2doctor-certificate_id',
            'certification2doctor'
        );

        // drops index for column `certificate_id`
        $this->dropIndex(
            'idx-certification2doctor-certificate_id',
            'certification2doctor'
        );

        $this->dropTable('certification2doctor');
    }
}
