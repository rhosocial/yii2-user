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

namespace rhosocial\user\web\user\controllers;

use yii\web\Controller;

/**
 * Settings Controller.
 * The controller is only used as a demonstration, you need to develop and cover
 * the controller and view yourself.
 * The specific apporach is as follow:
 *
 * First, you need to implement `SettingController` and corresponding view(s), for example:
```php
namespace app\controllers;

use yii\web\Controller;

class SettingController extends Controller
{
    public $layout = '//main'; // If you want to use your own layout, please assign a absolute path.
    public function actionIndex()
    {
        return $this->render('//setting/index'); // If you want to use your own view, please pass a absolute path.
    }
    ...
}
```
 * Then, you need to specify your own controller in the module's controllerMap property:
```
    'modules' => [
        'user' => [
            'class' => 'rhosocial\user\web\user\Module,
            'controllerMap' => [
                'setting' => [
                    'class' => 'app\controllers\SettingController',
                ],
            ],
        ],
    ],
```
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class SettingController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
