<?php

use yii\db\Migration;

/**
 * Class m180321_012238_insert_story_types
 */
class m180321_012238_insert_story_types extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
    	$this->delete('clinical_story_type', 'TRUE');
    	$this->execute(file_get_contents(__DIR__ . '/data/clinical_story_type.sql'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
    	$this->delete('clinical_story_type');
    }
}
