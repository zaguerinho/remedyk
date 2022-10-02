<?php


use yii\db\Migration;

/**
 * Handles adding fa_icon_class to table `notification`.
 */
class m180112_003344_add_fa_icon_class_column_to_notification_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('notification', 'fa_icon_class', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('notification', 'fa_icon_class');
    }
}
