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

    public $contentAttributeRule = ['string', 'max' => 32];

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getContent();
    }
}
