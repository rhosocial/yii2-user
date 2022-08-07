<?php

namespace rhosocial\user\tests\data\models\user;

/**
 * The User model disables the GUID and sets the id to auto-increment.
 */
class UserIdAI extends User
{
    public $guidAttribute = false;
    public $idAttributeType = 2;
}