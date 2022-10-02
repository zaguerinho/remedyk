<?php

use yii\db\Migration;

/**
 * Handles adding currency_id to table `membership`.
 * Has foreign keys to the tables:
 *
 * - `currency`
 */
class m171225_204424_add_currency_id_column_to_membership_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('membership', 'currency_id', $this->integer());

        // creates index for column `currency_id`
        $this->createIndex(
            'idx-membership-currency_id',
            'membership',
            'currency_id'
        );

        // add foreign key for table `currency`
        $this->addForeignKey(
            'fk-membership-currency_id',
            'membership',
            'currency_id',
            'currency',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `currency`
        $this->dropForeignKey(
            'fk-membership-currency_id',
            'membership'
        );

        // drops index for column `currency_id`
        $this->dropIndex(
            'idx-membership-currency_id',
            'membership'
        );

        $this->dropColumn('membership', 'currency_id');
    }
}
