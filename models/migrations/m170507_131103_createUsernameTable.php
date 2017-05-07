<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.me/
 * @copyright Copyright (c) 2016 - 2017 vistart
 * @license https://vistart.me/license/
 */
namespace rhosocial\user\models\migrations;

use rhosocial\user\migrations\Migration;
use rhosocial\user\models\Username;
use rhosocial\user\User;

/**
 * Class m170507_131103_createUsernameTable
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class m170507_131103_createUsernameTable extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName == 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT='Username'";
            $this->createTable(Username::tableName(), [
                'guid' => $this->varbinary(16)->notNull()->comment('User GUID'),
                'content' => $this->varchar(32)->charset('utf8mb4')->collate('utf8mb4_unicode_ci')->defaultValue('')->notNull()->comment('Username'),
                'ip' => $this->varbinary(16)->defaultValue(0)->notNull()->comment('IP Address'),
                'ip_type' => $this->smallInteger()->defaultValue(4)->notNull()->comment('IP Address Type'),
                'created_at' => $this->dateTime()->notNull()->defaultValue('1970-01-01 00:00:00')->comment('Created At'),
                'updated_at' => $this->dateTime()->notNull()->defaultValue('1970-01-01 00:00:00')->comment('Updated At'),
            ], $tableOptions);
        }
        $this->addPrimaryKey('user_username_guid_pk', Username::tableName(), 'guid');
        $this->addForeignKey('user_username_fk', Username::tableName(), 'guid', User::tableName(), 'guid', 'CASCADE', 'CASCADE');
        $this->createIndex('username_unique', Username::tableName(), 'content', true);
    }

    public function down()
    {
        $this->dropTable(Username::tableName());
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
