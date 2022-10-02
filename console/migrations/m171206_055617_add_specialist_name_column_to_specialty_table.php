<?php

use yii\db\Migration;

/**
 * Handles adding specialist_name to table `specialty`.
 */
class m171206_055617_add_specialist_name_column_to_specialty_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('specialty', 'specialist_name', 'jsonb');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('specialty', 'specialist_name');
    }
}
