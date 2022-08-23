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


/**
 * Invitation registration is not enabled for this user.
 */
class UserNotInvitationRegistration extends \rhosocial\user\models\User
{
    public $invitationRegistrationClass = false;
}
