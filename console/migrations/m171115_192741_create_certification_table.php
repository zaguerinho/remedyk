<?php

use yii\db\Migration;

/**
 * Handles the creation of table `certification`.
 */
class m171115_192741_create_certification_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('certification', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('certification');
    }
}
