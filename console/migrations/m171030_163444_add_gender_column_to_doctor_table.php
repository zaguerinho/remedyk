<?php

use yii\db\Migration;

/**
 * Handles adding gender to table `doctor`.
 */
class m171030_163444_add_gender_column_to_doctor_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('doctor', 'gender', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('doctor', 'gender');
    }
}
