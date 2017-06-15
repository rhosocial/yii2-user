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

namespace rhosocial\user\tests\data;

use rhosocial\user\tests\data\models\invitation\Registration;
use rhosocial\user\tests\data\models\Username;

/**
 * Description of User
 *
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class User extends \rhosocial\user\User
{
    public $profileClass = Profile::class;
    public $passwordHistoryClass = PasswordHistory::class;
    public $usernameClass = Username::class;
    public $invitationRegistrationClass = Registration::class;
}
