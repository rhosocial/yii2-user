<?php

namespace rhosocial\user\tests\data\models\user\migrations;

use rhosocial\user\migrations\Migration;
use rhosocial\user\tests\data\models\user\UserIdAI as User;

/**
 * Class m220805_163053_CreateUserIdAI
 */
class m220805_163053_CreateUserIdAI extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='User'";
            $this->createTable(User::tableName(), [
                'id' => $this->integerPk(11)->comment('User ID No.'),
                'pass_hash' => $this->varchar(80)->notNull()->comment('Password Hash'),
                'ip' => $this->varbinary(16)->notNull()->defaultValue(0)->comment('IP Address'),
                'ip_type' => $this->tinyInteger(3)->unsigned()->notNull()->defaultValue(4)->comment('IP Address Type'),
                'created_at' => $this->dateTime()->notNull()->defaultValue('1970-01-01 00:00:00')->comment('Created At'),
                'updated_at' => $this->dateTime()->notNull()->defaultValue('1970-01-01 00:00:00')->comment('Updated At'),
                'auth_key' => $this->varchar(40)->notNull()->comment('Authentication Key'),
                'access_token' => $this->varchar(40)->notNull()->comment('Access Token'),
                'password_reset_token' => $this->varchar(40)->comment('Password Reset Token'),
                'status' => $this->tinyInteger(3)->unsigned()->notNull()->defaultValue(1)->comment('Status'),
                'type' => $this->tinyInteger(3)->unsigned()->notNull()->defaultValue(0)->comment('Type'),
                'source' => $this->varchar(255)->notNull()->defaultValue('')->comment('Source'),
            ], $tableOptions);
        }
        // $this->addPrimaryKey("user_id_pk", User::tableName(), 'id'); // `intergerPk()` method has made this field primary key and auto-incremented.
        $this->createIndex('user_auth_key_unique', User::tableName(), 'auth_key', true);
        $this->createIndex('user_access_token_unique', User::tableName(), 'access_token', true);
        $this->createIndex('user_password_reset_token_unique', User::tableName(), 'password_reset_token', true);
        $this->createIndex('user_created_at_normal', User::tableName(), 'created_at');
        $this->createIndex('user_status_normal', User::tableName(), 'status');
        $this->createIndex('user_type_normal', User::tableName(), 'type');
        $this->createIndex('user_source_normal', User::tableName(), 'source');
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(User::tableName());
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220805_163053_CreateUserIdAI cannot be reverted.\n";

        return false;
    }
    */
}
