<?php

use yii\db\Migration;

/**
 * Handles adding clinical_story_type_id to table `clinical_story`.
 * Has foreign keys to the tables:
 *
 * - `clinical_story_type`
 */
class m180318_002657_add_clinical_story_type_id_column_to_clinical_story_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('clinical_story', 'clinical_story_type_id', $this->integer());

        // creates index for column `clinical_story_type_id`
        $this->createIndex(
            'idx-clinical_story-clinical_story_type_id',
            'clinical_story',
            'clinical_story_type_id'
        );

        // add foreign key for table `clinical_story_type`
        $this->addForeignKey(
            'fk-clinical_story-clinical_story_type_id',
            'clinical_story',
            'clinical_story_type_id',
            'clinical_story_type',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `clinical_story_type`
        $this->dropForeignKey(
            'fk-clinical_story-clinical_story_type_id',
            'clinical_story'
        );

        // drops index for column `clinical_story_type_id`
        $this->dropIndex(
            'idx-clinical_story-clinical_story_type_id',
            'clinical_story'
        );

        $this->dropColumn('clinical_story', 'clinical_story_type_id');
    }
}
