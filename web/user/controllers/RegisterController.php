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

use rhosocial\user\forms\RegisterForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class RegisterController extends Controller
{
    const SESSION_KEY_REGISTER_USER_ID = 'session_key_register_user_id';
    const SESSION_KEY_REGISTER_FAILED_MESSAGE = 'session_key_register_failed_message';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => false,
                        'roles' => ['@'],
                        'denyCallback' => function ($rule, $action) {
                            throw new ForbiddenHttpException(Yii::t('user', 'You must log out current user before registering new one.'));
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {        
        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post())) {
            try {
                if (($result = $model->register()) === true) {
                    Yii::$app->session->setFlash(self::SESSION_KEY_REGISTER_USER_ID, $model->model->getID());
                    return $this->redirect(['success']);
                }
                if ($result instanceof \Exception) {
                    throw $result;
                }
            } catch (\Exception $ex) {
                Yii::error($ex->getMessage(), __METHOD__);
                Yii::$app->session->setFlash(self::SESSION_KEY_REGISTER_FAILED_MESSAGE, $ex->getMessage());
            }
            return $this->redirect(['failed']);
        }
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionSuccess()
    {
        $id = Yii::$app->session->getFlash(self::SESSION_KEY_REGISTER_USER_ID);
        if ($id === null) {
            return $this->redirect(['index']);
        }
        return $this->render('success', ['id' => $id]);
    }

    public function actionFailed()
    {
        $message = Yii::$app->session->getFlash(self::SESSION_KEY_REGISTER_FAILED_MESSAGE);
        if ($message === null) {
            return $this->redirect(['index']);
        }
        return $this->render('failed', ['message' => $message]);
    }
}
