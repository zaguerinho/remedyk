<?php

use yii\db\Migration;

/**
 * Handles the creation of table `membership`.
 */
class m170926_210332_create_membership_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('membership', [
            'id' => $this->primaryKey(),
            'price' => $this->decimal(10,2),
            'picture_count' => $this->integer(),
            'extra_rank' => $this->integer(),
            'name' => $this->string(),
            'description' => $this->text(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('membership');
    }
}
