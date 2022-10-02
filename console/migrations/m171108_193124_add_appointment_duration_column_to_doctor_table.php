<?php

use yii\db\Migration;

/**
 * Handles adding appointment_duration to table `doctor`.
 */
class m171108_193124_add_appointment_duration_column_to_doctor_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('doctor', 'appointment_duration', $this->time());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('doctor', 'appointment_duration');
    }
}
