<?php

use yii\db\Migration;

/**
 * Handles adding commission_percent to table `membership`.
 */
class m171225_193812_add_commission_percent_column_to_membership_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('membership', 'commission_percent', $this->decimal(5,4));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('membership', 'commission_percent');
    }
}
