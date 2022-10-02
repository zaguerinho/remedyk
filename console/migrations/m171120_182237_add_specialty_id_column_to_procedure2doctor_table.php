<?php

use yii\db\Migration;

/**
 * Handles adding specialty_id to table `procedure2doctor`.
 * Has foreign keys to the tables:
 *
 * - `specialty`
 */
class m171120_182237_add_specialty_id_column_to_procedure2doctor_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('procedure2doctor', 'specialty_id', $this->integer());

        // creates index for column `specialty_id`
        $this->createIndex(
            'idx-procedure2doctor-specialty_id',
            'procedure2doctor',
            'specialty_id'
        );

        // add foreign key for table `specialty`
        $this->addForeignKey(
            'fk-procedure2doctor-specialty_id',
            'procedure2doctor',
            'specialty_id',
            'specialty',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `specialty`
        $this->dropForeignKey(
            'fk-procedure2doctor-specialty_id',
            'procedure2doctor'
        );

        // drops index for column `specialty_id`
        $this->dropIndex(
            'idx-procedure2doctor-specialty_id',
            'procedure2doctor'
        );

        $this->dropColumn('procedure2doctor', 'specialty_id');
    }
}
