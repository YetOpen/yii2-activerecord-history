<?php

use yii\db\Migration;

/**
 * Class m230612_142900_add_column_app_id_to_table_modelhistory
 */
class m230612_142900_add_column_app_id_to_table_modelhistory extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('modelhistory', 'app_id', $this->string(25));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('modelhistory', 'app_id');
    }
}
