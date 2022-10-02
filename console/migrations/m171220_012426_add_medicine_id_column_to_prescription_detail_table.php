<?php

use yii\db\Migration;

/**
 * Handles adding medicine_id to table `prescription_detail`.
 * Has foreign keys to the tables:
 *
 * - `medicine`
 */
class m171220_012426_add_medicine_id_column_to_prescription_detail_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('prescription_detail', 'medicine_id', $this->integer());

        // creates index for column `medicine_id`
        $this->createIndex(
            'idx-prescription_detail-medicine_id',
            'prescription_detail',
            'medicine_id'
        );

        // add foreign key for table `medicine`
        $this->addForeignKey(
            'fk-prescription_detail-medicine_id',
            'prescription_detail',
            'medicine_id',
            'medicine',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `medicine`
        $this->dropForeignKey(
            'fk-prescription_detail-medicine_id',
            'prescription_detail'
        );

        // drops index for column `medicine_id`
        $this->dropIndex(
            'idx-prescription_detail-medicine_id',
            'prescription_detail'
        );

        $this->dropColumn('prescription_detail', 'medicine_id');
    }
}
