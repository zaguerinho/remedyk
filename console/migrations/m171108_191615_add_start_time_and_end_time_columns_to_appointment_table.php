<?php

use yii\db\Migration;

/**
 * Handles adding start_time_and_end_time to table `appointment`.
 */
class m171108_191615_add_start_time_and_end_time_columns_to_appointment_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('appointment', 'start_time', $this->time());
        $this->addColumn('appointment', 'end_time', $this->time());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('appointment', 'start_time');
        $this->dropColumn('appointment', 'end_time');
    }
}
