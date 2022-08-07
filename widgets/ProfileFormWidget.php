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

namespace rhosocial\user\widgets;

use rhosocial\user\models\Profile;
use yii\base\Widget;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class ProfileFormWidget extends Widget
{
    /**
     * @var Profile 
     */
    public $model;

    public function init()
    {
        if (is_null($this->model) || !($this->model instanceof Profile)) {
            $this->model = Yii::$app->user->identity->createProfile(['scenario' => Profile::SCENARIO_UPDATE]);
        }
        parent::init();
    }

    public function run()
    {
        return $this->render('profile-form', ['model' => $this->model]);
    }
}
