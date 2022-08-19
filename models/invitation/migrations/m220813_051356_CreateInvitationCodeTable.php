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

use rhosocial\user\models\invitation\InvitationCode;
use rhosocial\user\models\migrations\Migration;
use rhosocial\user\models\User;

/**
 * Handles the creation of table `{{%user_invitation_code}}`.
 * @package rhosocial\user\models\invitation\migrations
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class m220813_051356_CreateInvitationCodeTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName == 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB COMMENT='User Invitation Code'";
            $this->createTable(InvitationCode::tableName(), [
                'guid' => $this->varbinary(16)->notNull()->comment('GUID'),
                'code' => $this->varchar(16)->notNull()->defaultValue("")->comment('code'), // id.
                'user_guid' => $this->varbinary(16)->notNull()->comment('Issuer GUID'),
                'type' => $this->integer(11)->notNull()->comment('Invitation Type'), // Content.
                'ip' => $this->varbinary(16)->defaultValue(0)->notNull()->comment('IP Address'),
                'ip_type' => $this->smallInteger()->defaultValue(4)->notNull()->comment('IP Address Type'),
                'created_at' => $this->dateTime()->notNull()->defaultValue('1970-01-01 00:00:00')->comment('Created At'),
                'updated_at' => $this->dateTime()->notNull()->defaultValue('1970-01-01 00:00:00')->comment('Updated At'),
                'expired_after' => $this->integer()->notNull()->defaultValue(InvitationCode::$ExpiredAfterDefaultValue)->comment('Expired After'),
            ], $tableOptions);
        }
        $this->addPrimaryKey('user_invitation_code_guid_pk', InvitationCode::tableName(), 'guid');
        $this->createIndex('user_invitation_code_unique_index', InvitationCode::tableName(), 'code', true);
        $this->addForeignKey('user_invitation_code_user_guid_fk', InvitationCode::tableName(), 'user_guid', User::tableName(), 'guid', 'CASCADE', 'CASCADE');
        $this->createIndex('user_invitation_code_user_guid_index', InvitationCode::tableName(), 'user_guid');
        $this->createIndex('user_invitation_code_created_at_index', InvitationCode::tableName(), 'created_at');
        $this->createIndex('user_invitation_code_updated_at_index', InvitationCode::tableName(), 'updated_at');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(InvitationCode::tableName());
    }
}
