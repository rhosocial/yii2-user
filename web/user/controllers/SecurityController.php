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

namespace rhosocial\user\web\user\controllers;

use rhosocial\user\forms\ChangePasswordForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class SecurityController extends Controller
{
    const SESSION_KEY_CHANGE_PASSWORD_MESSAGE = 'session_key_change_password_message';

    public $changePasswordSuccessMessage = 'Password Changed.';

    public $changePasswordFailedMessage = 'Password Not Changed.';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'change-password', 'login-log'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionChangePassword()
    {
        $model = new ChangePasswordForm(['user' => Yii::$app->user->identity]);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->changePassword()) {
                Yii::$app->session->setFlash(self::SESSION_KEY_CHANGE_PASSWORD_MESSAGE, $this->changePasswordSuccessMessage);
                return $this->redirect(['change-password']);
            }
            $model->clearAttributes();
        }
        return $this->render('change-password', ['model' => $model]);
    }

    public function actionLoginLog()
    {
        return $this->render('login-log');
    }
}
