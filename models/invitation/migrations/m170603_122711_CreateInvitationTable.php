<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.me/
 * @copyright Copyright (c) 2016 - 2022 vistart
 * @license https://vistart.me/license/
 */

namespace rhosocial\user\models\invitation\migrations;

use rhosocial\user\models\migrations\Migration;
use rhosocial\user\models\invitation\Invitation;
use rhosocial\user\models\User;

/**
 * Class m170603_122711_CreateInvitationTable
 * @package rhosocial\user\models\invitation\migrations
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class m170603_122711_CreateInvitationTable extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName == 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT='User Invitation'";
            $this->createTable(Invitation::tableName(), [
                'guid' => $this->varbinary(16)->notNull()->comment('GUID'),
                'user_guid' => $this->varbinary(16)->notNull()->comment('User GUID'),
                'content' => $this->integer(11)->notNull()->comment('Invitation Type'),
                'invitee_guid' => $this->varbinary(16)->notNull()->comment('Invitee GUID'),
                'ip' => $this->varbinary(16)->defaultValue(0)->notNull()->comment('IP Address'),
                'ip_type' => $this->smallInteger()->defaultValue(4)->notNull()->comment('IP Address Type'),
                'created_at' => $this->dateTime()->notNull()->defaultValue('1970-01-01 00:00:00')->comment('Created At'),
            ], $tableOptions);
        }
        $this->addPrimaryKey('user_invitation_guid_pk', Invitation::tableName(), 'guid');
        $this->addForeignKey('user_invitation_user_guid_fk', Invitation::tableName(), 'user_guid', User::tableName(), 'guid', 'CASCADE', 'CASCADE');
        $this->createIndex('user_invitation_user_guid_index', Invitation::tableName(), 'user_guid');
        $this->addForeignKey('user_invitation_invitee_fk', Invitation::tableName(), 'invitee_guid', User::tableName(), 'guid', 'CASCADE', 'CASCADE');
        $this->createIndex('user_invitation_invitee_guid_index', Invitation::tableName(), 'invitee_guid');
    }

    public function safeDown()
    {
        $this->dropTable(Invitation::tableName());
    }

    /*
    // Use up/down to run migration code without a transaction
    public function up()
    {
    }

    public function down()
    {
    }
    */
}
