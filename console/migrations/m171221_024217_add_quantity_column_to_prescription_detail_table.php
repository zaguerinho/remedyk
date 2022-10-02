<?php

use yii\db\Migration;

/**
 * Handles adding quantity to table `prescription_detail`.
 */
class m171221_024217_add_quantity_column_to_prescription_detail_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('prescription_detail', 'quantity', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('prescription_detail', 'quantity');
    }
}
