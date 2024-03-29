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

namespace rhosocial\user\rbac;

use rhosocial\user\models\User;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class Assignment extends \yii\rbac\Assignment
{
    /**
     * @var string|User user ID (see [[\rhosocial\user\models\User::guid]])
     */
    public $userGuid;

    /**
     * @var int|null UNIX timestamp representing the assignment failure time. `null` represents that this assignment is
     * never expired.
     */
    public $failedAt;

    public function init()
    {
        if ($this->failedAt !== null && $this->failedAt < strtotime(date('Y-m-d H:i:s'))) {
            return \Yii::$app->db->createCommand()
                ->delete(\Yii::$app->authManager->assignmentTable, [
                    'user_guid' => (string) $this->userGuid,
                    'item_name' => $this->roleName
                ])->execute() > 0;
        }
    }
}
