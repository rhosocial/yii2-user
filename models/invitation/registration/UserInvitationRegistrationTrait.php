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

namespace rhosocial\user\models\invitation\registration;

use rhosocial\base\models\queries\BaseBlameableQuery;
use rhosocial\base\models\queries\BaseUserQuery;
use rhosocial\user\models\invitation\Invitation;
use rhosocial\user\models\User;
use Yii;
use yii\base\InvalidParamException;
use yii\db\IntegrityException;

/**
 * Trait UserInvitationRegistrationTrait
 * @property-read Invitation[] invitationRegistrations
 * @property-read User[] invitationRegistrationInvitees
 *
 * @package rhosocial\user\models\invitation\registration
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
trait UserInvitationRegistrationTrait
{
    public $invitationRegistrationClass = Registration::class;
    /**
     * @param array $associatedModels
     * @param array $authRoles
     * @param User $inviter
     * @return boolean
     * @throws \Exception
     * @throws InvalidParamException
     */
    public function registerAccordingToInvitation(array $associatedModels = [], array $authRoles = [], $inviter = null)
    {
        if (!$inviter) {
            return false;
        }
        if ($inviter instanceof User && $inviter->getIsNewRecord()) {
            throw new InvalidParamException("Inviter cannot be a new user.");
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $result = $this->register($associatedModels, $authRoles);
            if ($result instanceof \Exception) {
                throw $result;
            }
            if ($result !== true) {
                throw new IntegrityException("Registration Failed.");
            }
            $invitation = $inviter->createInvitationRegistration($this);
            $result = $invitation->save();
            if (!$result) {
                throw new IntegrityException("Record Invitation Failed:" . $invitation->getFirstError());
            }
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
        return true;
    }

    /**
     * Create a registration invitation instance.
     * @param string|User $invitee The invited person.
     * @return Registration
     */
    public function createInvitationRegistration($invitee)
    {
        return $this->create($this->invitationRegistrationClass, ['invitee' => $invitee]);
    }

    /**
     * Check whether this user enables the invitation from registration feature or not.
     * @return boolean
     */
    public function hasEnabledInvitationRegistration()
    {
        if ($this->invitationRegistrationClass === false || !is_string($this->invitationRegistrationClass) || !class_exists($this->invitationRegistrationClass)) {
            return false;
        }
        return true;
    }

    /**
     * @return BaseBlameableQuery
     */
    public function getInvitationRegistrations()
    {
        if (!$this->hasEnabledInvitationRegistration()) {
            return null;
        }
        $irClass = $this->invitationRegistrationClass;
        $noInit = $irClass::buildNoInitModel();
        /* @var $noInit Registration */
        return $this->hasMany($irClass, [$noInit->createdByAttribute => $this->guidAttribute]);
    }

    /**
     * Get query which the current user is as the inviter.
     * If you want to get which users the current user has invited, you can use:
     * ```php
     * $users = $this->invitationRegistrationInvitees;
     * ```
     * If you just want to know who the current user has most recently invited, you can use:
     * ```php
     * $user = $this->getInvitationRegistrationInvitees()->orderByCreatedAt(SORT_DESC)->one();
     * ```
     * @return BaseUserQuery
     */
    public function getInvitationRegistrationInvitees()
    {
        return $this->hasMany(static::class, [$this->guidAttribute => 'invitee_guid'])->via('invitationRegistrations');
    }
}
