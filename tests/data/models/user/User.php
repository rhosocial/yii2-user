<?php

namespace rhosocial\user\tests\data\models\user;

use rhosocial\user\tests\data\models\identifier\Username;
use rhosocial\user\tests\data\models\invitation\Registration;
use rhosocial\user\tests\data\models\security\PasswordHistory;

/**
 * The User model is in the default state, that is, without any modification.
 */
class User extends \rhosocial\user\models\User
{
    public $invitationRegistrationClass = Registration::class;
    public $usernameClass = Username::class;
    public $profileClass = Profile::class;
    public $passwordHistoryClass = PasswordHistory::class;
}