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

namespace rhosocial\user\rbac\permissions;

use rhosocial\user\rbac\rules\UpdateUserRule;
use yii\rbac\Permission;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class UpdateUser extends Permission
{
    /**
     * @inheritdoc
     */
    public $name = 'updateUser';
    
    /**
     * @inheritdoc
     */
    public $description = 'Update a user';
    
    public function init()
    {
        parent::init();
        $this->ruleName = empty($this->ruleName) ? (new UpdateUserRule())->name : $this->ruleName;
    }
}
