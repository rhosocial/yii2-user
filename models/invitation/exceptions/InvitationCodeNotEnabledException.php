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

namespace rhosocial\user\models\invitation\exceptions;

/**
 * Invitation Code Not Enabled Exception.
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class InvitationCodeNotEnabledException extends \yii\base\InvalidConfigException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Invitation Code Not Enabled';
    }
}
