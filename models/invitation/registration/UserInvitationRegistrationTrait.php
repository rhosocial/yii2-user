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
use rhosocial\user\models\exception\NotActiveUserException;
use rhosocial\user\models\invitation\Invitation;
use rhosocial\user\models\User;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
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
     * Check if the inviter is valid.
     * The inviter must exist in the database and be active.
     * @param User $inviter
     * @return bool true if [[$inviter]] is valid.
     * @throws InvalidArgumentException throws if [[$inviter]] is null or new one.
     * @throws IntegrityException throws if [[$inviter]] cannot be refreshed.
     * @throws NotActiveUserException throws if [[$inviter]] is not active.
     */
    protected function checkInviter($inviter = null) {
        if (!$inviter) {
            throw new InvalidArgumentException("Inviter cannot be null.");
        }
        if ($inviter instanceof User && $inviter->getIsNewRecord()) {
            throw new InvalidArgumentException("Inviter cannot be a new user.");
        }
        if (!$inviter->refresh()) {
            throw new IntegrityException("Inviter cannot be refreshed.");
        }
        if ($inviter->status != static::$statusActive) {
            throw new NotActiveUserException("The inviter is not currently an active user and cannot be as an inviter.");
        }
        return true;
    }

    /**
     * Register by invitation.
     * If an exception occurs during the registration process, all operations that have taken effect will be rolled.
     * @param array $associatedModels
     * @param array $authRoles
     * @param User $inviter The inviting user must be a valid user, that is, the user has a record in the database and
     * has the right to invite registration.
     * @return bool true if registration succeeded.
     * @throws \Exception
     * @throws InvalidConfigException throws if invitation registration is not enabled.
     * @throws InvalidArgumentException throws if [[$inviter]] is null or new one.
     * @throws IntegrityException throws if [[$inviter]] cannot be refreshed or invitation active record failed to save.
     * @throws NotActiveUserException throws if [[$inviter]] is not active.
     */
    public function registerByInvitation(array $associatedModels = [], array $authRoles = [], $inviter = null)
    {
        if (!$this->hasEnabledInvitationRegistration()) {
            throw new InvalidConfigException("Invitation registration is not enabled.");
        }
        $transaction = Yii::$app->db->beginTransaction();
        $this->checkInviter($inviter);
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
     * Get invitation registration query which the current user is as the inviter.
     * If you want to get all invitations issued by the current user, you can use:
     * ```php
     * $invitations = $this->invitationRegistrations;
     * ```
     * If you just want to know the latest invitation, you can use:
     * ```php
     * $invitation = $this->getInvitationRegistrations()->orderByCreatedAt(SORT_DESC)->one();
     * ```
     * @return BaseBlameableQuery|null Null if invitation disabled.
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
     * Get invitee query which the current user is as the inviter.
     * If you want to get which users the current user has invited, you can use:
     * ```php
     * $users = $this->invitationRegistrationInvitees;
     * ```
     * If you just want to know who the current user has most recently invited, you can use:
     * ```php
     * $user = $this->getInvitationRegistrationInvitees()->orderByCreatedAt(SORT_DESC)->one();
     * ```
     * @return BaseUserQuery|null Null if invitation disabled.
     */
    public function getInvitationRegistrationInvitees()
    {
        if (!$this->hasEnabledInvitationRegistration()) {
            return null;
        }
        return $this->hasMany(static::class, [$this->guidAttribute => 'invitee_guid'])->via('invitationRegistrations');
    }
}
