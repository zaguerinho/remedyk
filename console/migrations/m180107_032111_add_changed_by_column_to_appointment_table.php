<?php

use yii\db\Migration;

/**
 * Handles adding changed_by to table `appointment`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m180107_032111_add_changed_by_column_to_appointment_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('appointment', 'changed_by', $this->integer());

        // creates index for column `changed_by`
        $this->createIndex(
            'idx-appointment-changed_by',
            'appointment',
            'changed_by'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-appointment-changed_by',
            'appointment',
            'changed_by',
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
        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-appointment-changed_by',
            'appointment'
        );

        // drops index for column `changed_by`
        $this->dropIndex(
            'idx-appointment-changed_by',
            'appointment'
        );

        $this->dropColumn('appointment', 'changed_by');
    }
}
