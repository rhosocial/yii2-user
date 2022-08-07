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

namespace rhosocial\user\rbac;

/**
 * 
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class Item extends \yii\rbac\Item
{
    /**
     * @var int Color in RGB. -1 means transparent (no color).
     */
    public $color = -1;
}