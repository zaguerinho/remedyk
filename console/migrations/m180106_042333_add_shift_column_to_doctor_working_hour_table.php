<?php

use yii\db\Migration;

/**
 * Handles adding shift to table `doctor_working_hour`.
 */
class m180106_042333_add_shift_column_to_doctor_working_hour_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('doctor_working_hour', 'shift', $this->integer());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('doctor_working_hour', 'shift');
    }
}
