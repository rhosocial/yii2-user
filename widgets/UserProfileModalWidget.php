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

namespace rhosocial\user\widgets;

use yii\base\Widget;

/**
 * Class UserProfileModalWidget
 * @package rhosocial\user\widgets
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class UserProfileModalWidget extends Widget
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var null|array
     */
    public $toggleButton;

    /**
     * @return string
     */
    public function run()
    {
        return $this->render('user-profile-modal', [
            'toggleButton' => $this->toggleButton,
            'user' => $this->user,
        ]);
    }
}
