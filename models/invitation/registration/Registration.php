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

namespace rhosocial\user\models\invitation\registration;

use rhosocial\user\models\invitation\Invitation;
use rhosocial\user\User;
use yii\base\InvalidConfigException;

/**
 * Class Registration
 * @package rhosocial\user\models\invitation\registration
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class Registration extends Invitation
{
    const INVITATION_REGISTRATION = 0x01;
    public $allowRepeated = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->getIsNewRecord()) {
            $this->content = self::INVITATION_REGISTRATION;
        } elseif ($this->content != self::INVITATION_REGISTRATION) {
            throw new InvalidConfigException("This invitation is not being used for registration.");
        }
    }

    /**
     * @return BaseBlameableQuery
     */
    public static function find()
    {
        return parent::find()->content(self::INVITATION_REGISTRATION);
    }

    /**
     * @param User|string $invitee
     * @return BaseBlameableQuery
     */
    public static function findByInvitee($invitee)
    {
        return parent::findByInvitee($invitee)->content(self::INVITATION_REGISTRATION);
    }
}
