<?php

use yii\db\Migration;

/**
 * Handles adding created_at to table `appointment`.
 */
class m180107_043733_add_created_at_column_to_appointment_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('appointment', 'created_at', $this->date());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('appointment', 'created_at');
    }
}
