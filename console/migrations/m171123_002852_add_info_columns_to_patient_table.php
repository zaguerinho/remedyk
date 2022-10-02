<?php

use yii\db\Migration;

/**
 * Handles adding info to table `patient`.
 */
class m171123_002852_add_info_columns_to_patient_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('patient', 'height', $this->string());
        $this->addColumn('patient', 'weight', $this->decimal(10,2));
        $this->addColumn('patient', 'age', $this->integer());
        $this->addColumn('patient', 'blood_type', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('patient', 'height');
        $this->dropColumn('patient', 'weight');
        $this->dropColumn('patient', 'age');
        $this->dropColumn('patient', 'blood_type');
    }
}
