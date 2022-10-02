<?php

use yii\db\Migration;

/**
 * Handles adding conekta_order_id to table `commission`.
 */
class m180108_204929_add_conekta_order_id_column_to_commission_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('commission', 'conekta_order_id', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('commission', 'conekta_order_id');
    }
}
