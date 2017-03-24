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
    const SESSION_KEY_CHANGE_PASSWORD_RESULT = 'session_key_change_password_result';
    const SESSION_KEY_CHANGE_PASSWORD_MESSAGE = 'session_key_change_password_message';
    const CHANGE_PASSWORD_SUCCESS = 'success';
    const CHANGE_PASSWORD_FAILED = 'failed';

    public $changePasswordSuccessMessage;

    public $changePasswordFailedMessage;

    public $layout = 'security';

    protected function initMessages()
    {
        if (empty($this->changePasswordFailedMessage)) {
            $this->changePasswordFailedMessage = Yii::t('user', 'Password Not Changed.');
        }
        if (empty($this->changePasswordSuccessMessage)) {
            $this->changePasswordSuccessMessage = Yii::t('user', 'Password Changed.');
        }
    }

    public function init()
    {
        $this->initMessages();
        parent::init();
    }

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
                Yii::$app->session->setFlash(self::SESSION_KEY_CHANGE_PASSWORD_RESULT, self::CHANGE_PASSWORD_SUCCESS);
                Yii::$app->session->setFlash(self::SESSION_KEY_CHANGE_PASSWORD_MESSAGE, $this->changePasswordSuccessMessage);
                return $this->redirect(['change-password']);
            }
            Yii::$app->session->setFlash(self::SESSION_KEY_CHANGE_PASSWORD_RESULT, self::CHANGE_PASSWORD_FAILED);
            Yii::$app->session->setFlash(self::SESSION_KEY_CHANGE_PASSWORD_MESSAGE, $this->changePasswordFailedMessage);
            $model->clearAttributes();
        }
        return $this->render('change-password', ['model' => $model]);
    }

    public function actionLoginLog()
    {
        return $this->render('login-log');
    }
}
