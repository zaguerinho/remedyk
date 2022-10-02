<?php

use yii\db\Migration;

/**
 * Handles the creation of table `clinical_story_type`.
 */
class m180318_002528_create_clinical_story_type_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('clinical_story_type', [
            'id' => $this->primaryKey(),
            'name' => 'jsonb',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('clinical_story_type');
    }
}
