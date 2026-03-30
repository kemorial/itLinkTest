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
            'price' => $this->decimal(10, 2)->notNull(),
            'photo_url' => $this->string(500)->null(),
            'contacts' => $this->string(255)->notNull(),
            'created_at' => $this->dateTime()->notNull(),
        ]);
        $this->createTable('{{%car_options}}', [
            'id' => $this->primaryKey(),
            'car_id' => $this->integer()->notNull(),
            'brand' => $this->string(255)->notNull(),
            'model' => $this->string(255)->notNull(),
            'year' => $this->integer()->notNull(),
            'body' => $this->string(255)->notNull(),
            'mileage' => $this->integer()->notNull(),
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
