<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cars}}`.
 */
class m260328_011701_createCarsTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%cars}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
            'price' => $this->integer()->notNull(),
            'photo_url' => $this->string(255)->notNull(),
            'contacts' => $this->string(255)->notNull(),
        ]);
        $this->createTable('{{%car_options}}', [
            'id' => $this->primaryKey(),
            'car_id' => $this->integer()->notNull(),
            'brand' => $this->string(255),
            'model' => $this->string(255),
            'year' => $this->integer(),
            'body' => $this->string(255),
            'mileage' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk_car_options_car_id',
            '{{%car_options}}',
            'car_id',
            '{{%cars}}',
            'id',
            'CASCADE',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_car_options_car_id', '{{%car_options}}');
        $this->dropTable('{{%car_options}}');
        $this->dropTable('{{%cars}}');
    }
}
