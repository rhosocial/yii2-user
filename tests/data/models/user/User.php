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

namespace rhosocial\user\tests\data\models\user;

use rhosocial\user\tests\data\models\identifier\Username;
use rhosocial\user\tests\data\models\invitation\Registration;
use rhosocial\user\tests\data\models\invitation\RegistrationCode;
use rhosocial\user\tests\data\models\log\Login;
use rhosocial\user\tests\data\models\security\PasswordHistory;
use yii\web\IdentityInterface;

/**
 * The User model is in the default state, that is, without any modification.
 */
class User extends \rhosocial\user\models\User
{
    public $invitationRegistrationClass = Registration::class;
    public $invitationRegistrationCodeClass = RegistrationCode::class;
    public $usernameClass = Username::class;
    public $profileClass = Profile::class;
    public $passwordHistoryClass = PasswordHistory::class;
    public $loginLogClass = Login::class;
}