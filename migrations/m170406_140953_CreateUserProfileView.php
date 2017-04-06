<?php

/**
 *   _   __ __ _____ _____ ___  ____  _____
 *  | | / // // ___//_  _//   ||  __||_   _|
 *  | |/ // /(__  )  / / / /| || |     | |
 *  |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.me/
 * @copyright Copyright (c) 2016 - 2017 vistart
 * @license https://vistart.me/license/
 */

namespace rhosocial\user\migrations;

use rhosocial\user\User;
use rhosocial\user\Profile;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class m170406_140953_CreateUserProfileView extends Migration
{
    public function up()
    {
        $tablePrefix = $this->db->tablePrefix;
        $userTableName = $tablePrefix . 'user';
        $profileTableName = $tablePrefix . 'profile';
$sql = <<<EOT
CREATE 
VIEW `UserProfileView`AS 
SELECT
`t_user`.guid,
`t_user`.id,
`t_user`.pass_hash,
`t_user`.ip,
`t_user`.ip_type,
`t_user`.created_at,
`t_user`.updated_at,
`t_user`.auth_key,
`t_user`.access_token,
`t_user`.password_reset_token,
`t_user`.`status`,
`t_user`.type,
`t_user`.source,
`t_profile`.nickname,
`t_profile`.first_name,
`t_profile`.last_name,
`t_profile`.gravatar_type,
`t_profile`.gravatar,
`t_profile`.gender,
`t_profile`.timezone,
`t_profile`.individual_sign,
`t_profile`.created_at AS profile_created_at,
`t_profile`.updated_at AS profile_updated_at
FROM
`$userTableName` AS `t_user`
INNER JOIN `$profileTableName` AS `t_profile` ON `t_profile`.guid = `t_user`.guid ;
EOT;
        $this->execute($sql);
    }

    public function down()
    {
        $this->execute("DROP VIEW `UserProfileView`;");
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
