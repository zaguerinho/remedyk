<?php

use yii\db\Migration;

/**
 * Handles adding conekta_customer_id to table `user`.
 */
class m171227_220453_add_conekta_customer_id_column_to_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('user', 'conekta_customer_id', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('user', 'conekta_customer_id');
    }
}
