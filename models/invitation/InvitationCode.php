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

namespace rhosocial\user\models\invitation;

use rhosocial\base\models\models\BaseBlameableModel;
use rhosocial\base\models\queries\BaseBlameableQuery;
use rhosocial\user\models\User;

/**
 * Class InvitationCode.
 * Invitation codes can only be issued by registered users. The invitation code is globally unique, and all users can
 * uniquely determine the issuer and use of the invitation code through the invitation code.
 * Each user can issue an unlimited number of invitation codes.
 * Each invitation code can correspond to an invitation.
 * The user can not reuse the invitation code that has been used up.
 * @property string $code Invitation Code.
 * @property int $content Invitation Code Type.
 * @package rhosocial\user\models\invitation
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class InvitationCode extends BaseBlameableModel
{
    public $hostClass = User::class;
    public $idAttribute = 'code';
    public $idAttributeType = 1;
    public $idAttributeLength = 16;
    public $idCreatorCombinatedUnique = false;
    public $expiredAfterAttribute = 'expired_after';
    public static $ExpiredAfterDefaultValue = 30 * 86400;
    /**
     * @var array The content field in the data table should be an integer.
     */
    public $contentAttributeRule = ['integer'];

    public static function tableName()
    {
        return '{{%user_invitation_code}}';
    }

    /**
     * Get the issuer of the current invitation code (that is, the owner of the invitation code).
     * @return \rhosocial\base\models\queries\BaseUserQuery
     */
    public function getIssuer() {
        return $this->getHost();
    }
}