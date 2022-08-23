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
use rhosocial\user\models\exceptions\UserNotActiveException;
use rhosocial\user\models\invitation\Invitation;
use rhosocial\user\models\invitation\InvitationCode;
use rhosocial\user\models\invitation\exceptions\InvitationCodeNotFoundException;
use rhosocial\user\models\invitation\exceptions\InvitationCodeNotEnabledException;
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

    public $invitationRegistrationCodeClass = false;

    /**
     * Check whether this user enables the invitation from registration feature or not.
     * @return bool
     */
    public function hasEnabledInvitationRegistration()
    {
        return $this->invitationRegistrationClass !== false && is_string($this->invitationRegistrationClass) && class_exists($this->invitationRegistrationClass);
    }

    /**
     * Check whether this user enables the invitation code from registration feature or not.
     * @return bool
     */
    public function hasEnabledInvitationRegistrationCode() {
        return $this->invitationRegistrationCodeClass !== false && is_string($this->invitationRegistrationCodeClass) && class_exists($this->invitationRegistrationCodeClass);
    }

    /**
     * Check if the inviter is valid.
     * The inviter must exist in the database and be active.
     * @param User $inviter
     * @return bool true if [[$inviter]] is valid.
     * @throws InvalidArgumentException throws if [[$inviter]] is null or new one.
     * @throws IntegrityException throws if [[$inviter]] cannot be refreshed.
     * @throws UserNotActiveException throws if [[$inviter]] is not active.
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
            throw new UserNotActiveException("The inviter is not currently an active user and cannot be as an inviter.");
        }
        return true;
    }

    /**
     * Check if the Invitation Registration Code is valid.
     * @param string|InvitationCode|null $code the invitation code to be checked.
     * @return bool
     * @throws InvitationCodeNotEnabledException throws if Invitation Code has not been enabled.
     * @throws InvalidArgumentException throws if [[$code]] is null or new record.
     * @throws InvitationCodeNotFoundException throws if [[$code]] does not exist.
     */
    protected function checkInvitationRegistrationCode(string|InvitationCode $code = null) {
        if (!$this->hasEnabledInvitationRegistrationCode()) {
            throw new InvitationCodeNotEnabledException("Invitation Registration Code has not been enabled yet.");
        }
        if (!$code) {
            throw new InvalidArgumentException("The invitation registration code is empty.");
        }
        $class = $this->invitationRegistrationCodeClass;
        if (!is_string($code) || !$class::find()->where(['code' => $code])->exists()) {
            throw new InvitationCodeNotFoundException("The invitation registration code does not exist.");
        }
        if ($code instanceof InvitationCode && $code->getIsNewRecord()) {
            throw new InvalidArgumentException("The invitation registration code cannot be a new record.");
        }
        return true;
    }

    /**
     * Get issuer of the invitation registration code.
     * @param string|InvitationCode $code
     * @return BaseUserQuery
     */
    protected function getInvitationRegistrationCodeIssuer(string|InvitationCode $code) {
        $this->checkInvitationRegistrationCode($code);
        if (is_string($code)) {
            $class = $this->invitationRegistrationCodeClass;
            $code = $class::find()->where(['code' => $code])->one();
        }
        /* @var $code InvitationCode */
        return $code->getIssuer();
    }

    /**
     * Register by invitation.
     * If an exception occurs during the registration process, all operations that have taken effect will be rolled.
     * @param array $associatedModels
     * @param array $authRoles
     * @param User $inviter The inviting user must be a valid user, that is, the user has a record in the database and
     * has the right to invite registration.
     * @param string|InvitationCode $code
     * @return bool true if registration succeeded.
     * @throws \Exception
     * @throws InvalidConfigException throws if invitation registration is not enabled.
     * @throws InvalidArgumentException throws if [[$inviter]] is null or new one.
     * @throws IntegrityException throws if [[$inviter]] cannot be refreshed or invitation active record failed to save.
     * @throws UserNotActiveException throws if [[$inviter]] is not active.
     */
    public function registerByInvitation(array $associatedModels = [], array $authRoles = [], User $inviter = null, $code = null)
    {
        if (!$this->hasEnabledInvitationRegistration()) {
            throw new InvalidConfigException("Invitation registration is not enabled.");
        }
        $transaction = Yii::$app->db->beginTransaction();
        $isInviterValid = $inviter === null ? false : $this->checkInviter($inviter);
        Yii::info("is Inviter valid? $isInviterValid", __METHOD__);
        if (!$isInviterValid) {
            try {
                $isInvitationCodeValid = $this->checkInvitationRegistrationCode($code);
            } catch (InvitationCodeNotEnabledException|InvitationCodeNotFoundException|InvalidArgumentException $ex) {
                $transaction->rollBack();
                throw new InvalidArgumentException("Inviter and Invitation Registration Code both invalid.");
            } catch (\Exception $ex) {
                $transaction->rollBack();
                throw $ex;
            }
        }
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
        if (!$this->hasEnabledInvitationRegistration()) {
            return null;
        }
        return $this->create($this->invitationRegistrationClass, ['invitee' => $invitee]);
    }

    /**
     * Create a invitation registration code instance.
     * @return InvitationCode|null
     */
    public function createInvitationRegistrationCode() {
        if (!$this->hasEnabledInvitationRegistrationCode()) {
            return null;
        }
        $class = $this->invitationRegistrationCodeClass;
        return $this->create($this->invitationRegistrationCodeClass, ['content' => $class::INVITATION_REGISTRATION]);
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
     * @return BaseBlameableQuery|null Null if invitation registration code disabled.
     */
    public function getInvitationRegistrationCodes() {
        if (!$this->hasEnabledInvitationRegistrationCode()) {
            return null;
        }
        $class = $this->invitationRegistrationCodeClass;
        $noInit = $class::buildNoInitModel();
        /* @var $noInit InvitationCode */
        return $this->hasMany($class, [$noInit->createdByAttribute => $this->guidAttribute]);
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

    /**
     * Get invitations query for which the current code is used.
     * If you want to get which code the current invitation has used for, you can use:
     * ```php
     * $invitation = $this->invitationRegistrationCodeInvitation;
     * ```
     * If you just want to know attach more conditions, you can use:
     * ```php
     * $isInvitationCodeUsedUp = $this->getInvitationRegistrationCodeInvitation()->exists();
     * ```
     * @return BaseBlameableQuery|null
     */
    public function getInvitationRegistrationCodeInvitation() {
        if (!$this->hasEnabledInvitationRegistrationCode()) {
            return null;
        }
        return $this->hasOne($this->invitationRegistrationClass, ['invitation_code_guid' => 'guid'])->via('invitationRegistrationCodes');
    }

    /**
     * Issue invitation registration code.
     * If you want to get the invitation registration code just issued, you must put "issueInvitationRegistrationCode()"
     * and "getLatestInvitationRegistrationCode()" in the same transaction in order to ensure that. For example:
     * ```php
     * $transaction = $this->getDb->beginTransaction();
     * try {
     *     if (!$this->issueInvitationRegistrationCode()) {
     *         throw new IntegrityException("Failed to issue the invitation registration code.");
     *     }
     *     $code = $this->getLatestInvitationRegistrationCode();
     *     $transaction->commit();
     * } catch (\Exception $ex) {
     *     $transaction->rollBack();
     * }
     * ```
     * @param string|null $code predefined code. If you want to use randomly generated code, please leave null.
     * @return bool
     * @throws InvalidArgumentException throws if this user is new record.
     * @throws InvitationCodeNotEnabledException throws if invitation registration code not enabled.
     */
    public function issueInvitationRegistrationCode(string $code = null) {
        if ($this->getIsNewRecord()) {
            throw new InvalidArgumentException("New user cannot issue invitation registration code.");
        }
        if (!method_exists($this, "hasEnabledInvitationRegistrationCode") || !$this->hasEnabledInvitationRegistrationCode()) {
            throw new InvitationCodeNotEnabledException("Invitation Registration Code has not been enabled yet.");
        }
        $invitationRegistrationCode = $this->createInvitationRegistrationCode();
        /* @var $invitationRegistrationCode RegistrationCode */
        if ($code != null && is_string($code)) {
            $invitationRegistrationCode->code = $code;
        }
        if (!$invitationRegistrationCode->validate()) {
            Yii::error($invitationRegistrationCode->getErrorSummary(false)[0], __METHOD__);
        }
        return $invitationRegistrationCode->save();
    }

    /**
     * Issue a batch of invitation registration codes. If any one of the invitation registration codes fails to be issued,
     * all successfully issued invitation registration codes will be withdrawn.
     * If you want to get the invitation registration codes just issued, you must put "issueInvitationRegistrationCodes()"
     * and "getLatestInvitationRegistrationCodes()" in the same transaction in order to ensure that. For example:
     * ```php
     * $number = 10;
     * $transaction = $this->getDb->beginTransaction();
     * try {
     *     this->issueInvitationRegistrationCodes($number);
     *     $codes = $this->getLatestInvitationRegistrationCodes()->limit($number)->all();
     *     $transaction->commit();
     * } catch (\Exception $ex) {
     *     $transaction->rollBack();
     * }
     * ```
     * @param int $number the number of issued in batch, default to 10.
     * @return bool true if all codes succeed to be issued.
     * @throws IntegrityException throws if anyone of codes fails to be issued.
     * @throws InvitationCodeNotEnabledException throws if invitation registration code not enabled.
     * @throws \yii\db\Exception throws if database error occured.
     */
    public function issueInvitationRegistrationCodes(int $number = 10) {
        if ($number < 1) {
            throw new InvalidArgumentException("The number of issued must be greater than 0.");
        }
        if ($this->getIsNewRecord()) {
            throw new InvalidArgumentException("New user cannot issue invitation registration code.");
        }
        if (!method_exists($this, "hasEnabledInvitationRegistrationCode") || !$this->hasEnabledInvitationRegistrationCode()) {
            throw new InvitationCodeNotEnabledException("Invitation Registration Code has not been enabled yet.");
        }
        $invitationRegistrationCodes = [];
        for ($i = 0; $i < $number; $i++) {
            $invitationRegistrationCodes[] = $this->createInvitationRegistrationCode();
        }
        $transaction = $this->getDb()->beginTransaction();
        try {
            foreach ($invitationRegistrationCodes as $key => $code) {
                if (!$code->save()) {
                    throw new IntegrityException("Failed to issue $key-th code.");
                }
            }
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
        return true;
    }

    /**
     * Get latest invitation registration code.
     * @return array|RegistrationCode|null
     */
    public function getLatestInvitationRegistrationCode() {
        return $this->getInvitationRegistrationCodes()->orderByCreatedAt(SORT_DESC)->one();
    }

    /**
     * Get latest invitation registration code query.
     * @return BaseBlameableQuery|null
     */
    public function getLatestInvitationRegistrationCodes() {
        return $this->getInvitationRegistrationCodes()->orderByCreatedAt(SORT_DESC);
    }
}
