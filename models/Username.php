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

namespace rhosocial\user\models;

use rhosocial\base\models\models\BaseBlameableModel;
use rhosocial\user\User;
use Yii;

/**
 * Class Username
 * @package rhosocial\user\models
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class Username extends BaseBlameableModel
{
    public $hostClass = User::class;

    public $idAttribute = false;

    public $createdByAttribute = 'guid';
    public $updatedByAttribute = false;

    public $contentAttributeRule = ['string', 'max' => 32, 'min' => 2];

    public static $regex = '/^\w{2,32}$/';

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge([
            [$this->contentAttribute, 'match', 'not' => true, 'pattern' => '/^\d+$/', 'message' => Yii::t('user', 'The username can not be a pure number.')],
            [$this->contentAttribute, 'unique', 'message'=> Yii::t('user', 'The username should be unique.')],
        ], parent::rules());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getContent();
    }
}
