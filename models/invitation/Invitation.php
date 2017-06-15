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

namespace rhosocial\user\models\invitation;

use rhosocial\base\models\models\BaseBlameableModel;
use rhosocial\base\models\queries\BaseBlameableQuery;
use rhosocial\user\User;

/**
 * Class Invitation
 * This class is used to record each invitation of the current user. If you want to use the invitation code, please
 * refer to the [[InvitationCode]] class.
 * You can not use this class directly, but you need to declare and use specific invitation scenes, see [[Registration]].
 *
 * @property integer $content Invitation Type. The custom value range should be greater than or equal to 0x80.
 * Once this value is determined, no modification is recommended.
 * @property User|string $invitee Invited person.
 * @property string $invitee_guid The GUID of invited person.
 * Once this value is determined, no modification is recommended.
 *
 * @package rhosocial\user\models\invitation
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
abstract class Invitation extends BaseBlameableModel
{
    public $hostClass = User::class;
    public $idAttribute = false;
    public $updatedAtAttribute = false;
    /**
     * @var array The content field in the data table should be an integer.
     */
    public $contentAttributeRule = ['integer'];
    /**
     * @var bool Whether to allow to send invitation to the same invitee repeatedly.
     */
    public $allowRepeated = true;

    /**
     * @return array
     */
    public function getInviteeRules()
    {
        $rules = [
            ['invitee_guid', 'required'],
            ['invitee_guid', 'string'],
        ];
        if (!$this->allowRepeated) {
            $rules[] = [
                [$this->createdByAttribute, $this->contentAttribute, 'invitee_guid'], 'unique', 'targetAttribute' => [
                    $this->createdByAttribute, $this->contentAttribute, 'invitee_guid',
                ]
            ];
        }
        return $rules;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), $this->getInviteeRules());
    }

    /**
     * @param User|string $invitee
     * @return string
     */
    public function setInvitee($invitee)
    {
        return $this->invitee_guid = (string)$invitee;
    }

    /**
     * @return User
     */
    public function getInvitee()
    {
        $userClass = $this->hostClass;
        return $userClass::findOne($this->invitee_guid);
    }

    /**
     * @param User|string|array $invitee
     * @return BaseBlameableQuery
     */
    public static function findByInvitee($invitee)
    {
        if (!is_array($invitee)) {
            $i[0] = $invitee;
            $invitee = $i;
        }
        foreach ($invitee as $key => $i) {
            $invitee[$key] = (string)$i;
        }
        return static::find()->andWhere(['invitee_guid' => $invitee]);
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%user_invitation}}';
    }
}
