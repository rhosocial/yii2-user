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

namespace rhosocial\user\tests\data\models\invitation;

use rhosocial\user\tests\data\models\user\User;

/**
 * Class Registration
 * @package rhosocial\user\tests\data\models\invitation
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class Registration extends \rhosocial\user\models\invitation\registration\Registration
{
    public $hostClass = User::class;
    public $invitationCodeClass = RegistrationCode::class;
}
