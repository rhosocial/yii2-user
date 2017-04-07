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

namespace rhosocial\user\web\admin\controllers;

use rhosocial\user\User;
use rhosocial\user\UserProfileSearch;
use rhosocial\user\Profile;
use rhosocial\user\forms\ChangePasswordForm;
use rhosocial\user\forms\RegisterForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class UserController extends Controller
{
    public $layout = 'user';
    const RESULT_SUCCESS = 'success';
    const RESULT_FAILED = 'failed';
    const SESSION_KEY_MESSAGE = 'session_key_message';
    const SESSION_KEY_RESULT = 'session_key_result';

    public $registerSuccessMessage;
    public $registerFailedMessage;

    public $deregisterSuccessMessage;
    public $deregisterFailedMessage;
    
    public $updateSuccessMessage;
    public $updateFailedMessage;

    /**
     * @var string UseProfileSearch Class. 
     */
    public $userProfileSearchClass = UserProfileSearch::class;

    protected function initMessages()
    {
        if (!is_string($this->registerSuccessMessage)) {
            $this->registerSuccessMessage = Yii::t('user' ,'User Registered.');
        }
        if (!is_string($this->registerFailedMessage)) {
            $this->registerFailedMessage = Yii::t('user', 'Register Failed.');
        }
        if (!is_string($this->deregisterSuccessMessage)) {
            $this->deregisterSuccessMessage = Yii::t('user', 'User Deregistered.');
        }
        if (!is_string($this->deregisterFailedMessage)) {
            $this->deregisterFailedMessage = Yii::t('user', 'Failed to Deregister User.');
        }
        if (!is_string($this->updateSuccessMessage)) {
            $this->updateSuccessMessage = Yii::t('user', 'Updated.');
        }
        if (!is_string($this->updateFailedMessage)) {
            $this->updateFailedMessage = Yii::t('user', 'Failed to Update.');
        }
    }

    public function init()
    {
        $this->initMessages();
        parent::init();
    }

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [ // Disallow all unauthorized users to access this controller.
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [ // Allow the user who has the `viewUser` permission to access the `index` action.
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['viewUser'],
                    ],
                    [ // Disallow other non-admin users to access this controller.
                        'allow' => false,
                        'matchCallback' => function ($rule, $action) {
                            return !Yii::$app->authManager->checkAccess(Yii::$app->user->identity, 'admin');
                        },
                        'denyCallback' => function ($rule, $action) {
                            throw new UnauthorizedHttpException(Yii::t('user', 'You are not an administrator and have no access to this page.'));
                        },
                    ],
                    [ // Disallow admin users to access deregister action directly, only `POST` accepted.
                        'actions' => ['deregister'],
                        'allow' => false,
                        'matchCallback' => function ($rule, $action) {
                            return strtoupper(Yii::$app->request->getMethod()) != 'POST';
                        },
                        'denyCallback' => function ($rule, $action) {
                            throw new MethodNotAllowedHttpException(Yii::t('user', 'You cannot access this page directly.'));
                        },
                    ],
                    [ // Allow admin user to access other views.
                      // This is a final rule, if you want to add other rules, please put it before this rule.
                        'allow' => true,
                        'roles' => ['admin'], // Administrator can access this controller.
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'deregister' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        if (!class_exists($this->userProfileSearchClass)) {
            throw new ServerErrorHttpException('Unknown User Profile View.');
        }
        $class = $this->userProfileSearchClass;
        $searchModel = new $class();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        
        return $this->render('index', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel]);
    }

    public function actionRegisterNewUser()
    {
        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post())) {
            try {
                if (($result = $model->register()) === true) {
                    Yii::$app->session->setFlash(self::SESSION_KEY_RESULT, self::RESULT_SUCCESS);
                    Yii::$app->session->setFlash(self::SESSION_KEY_MESSAGE, '(' . $model->model->getID() . ') ' . $this->registerSuccessMessage);
                    return $this->redirect(['index']);
                }
                if ($result instanceof \Exception) {
                    throw $result;
                }
            } catch (\Exception $ex) {
                Yii::error($ex->getMessage(), __METHOD__);
                Yii::$app->session->setFlash(self::SESSION_KEY_RESULT, self::RESULT_FAILED);
                Yii::$app->session->setFlash(self::SESSION_KEY_MESSAGE, $ex->getMessage());
            }
        }
        return $this->render('register-new-user', ['model' => $model]);
    }

    /**
     * Get user by ID.
     * @param string $id User ID.
     * @return User
     * @throws BadRequestHttpException throw if user not found.
     */
    protected function getUser($id)
    {
        $class = Yii::$app->user->identityClass;
        if (!class_exists($class)) {
            return null;
        }
        $user = $class::find()->id($id)->one();
        if (empty($user) || !($user instanceof User)) {
            throw new BadRequestHttpException(Yii::t('user', 'User Not Found.'));
        }
        return $user;
    }

    /**
     * Deregister User.
     * @param string $id User ID.
     * @return string
     */
    public function actionDeregister($id)
    {
        $id = (int)$id;
        if (Yii::$app->user->identity->getID() == $id) {
            throw new ForbiddenHttpException(Yii::t('user', 'You cannot deregister yourself.'));
        }
        $user = $this->getUser($id);
        try {
            $result = $user->deregister();
            if ($result instanceof \Exception) {
                throw $result;
            }
        } catch (\Exception $ex) {
            throw new ServerErrorHttpException($ex->getMessage());
        }
        if ($result !== true) {
            throw new ServerErrorHttpException(Yii::t('user', 'Failed to deregister user.'));
        }
        Yii::$app->session->setFlash(self::SESSION_KEY_RESULT, self::RESULT_SUCCESS);
        Yii::$app->session->setFlash(self::SESSION_KEY_MESSAGE, '(' . $user->getID() . ') ' . $this->deregisterSuccessMessage);
        return $this->redirect(['index']);
    }

    public function actionView($id)
    {
        $user = $this->getUser($id);
        return $this->render('view', ['user' => $user]);
    }

    public function actionUpdate($id)
    {
        $user = $this->getUser($id);
        $model = $user->profile;
        if (empty($model)) {
            $model = $user->createProfile();
        }
        $model->scenario = Profile::SCENARIO_UPDATE;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->getGUID() != $user->getGUID()) {
                throw new BadRequestHttpException(Yii::t('user', 'Please do not forge parameters.'));
            }
            if ($model->save()) {
                Yii::$app->session->setFlash(self::SESSION_KEY_RESULT, self::RESULT_SUCCESS);
                Yii::$app->session->setFlash(self::SESSION_KEY_MESSAGE, '(' . $user->getID() . ') ' . $this->updateSuccessMessage);
                return $this->redirect(['update', 'id' => $id]);
            }
            Yii::$app->session->setFlash(self::SESSION_KEY_RESULT, self::RESULT_FAILED);
            Yii::$app->session->setFlash(self::SESSION_KEY_MESSAGE, '(' . $user->getID() . ') ' . $this->updateFailedMessage);
        }
        return $this->render('update', ['user' => $user, 'model' => $model]);
    }

    public function actionChangePassword($id)
    {
        $user = $this->getUser($id);
        $model = new ChangePasswordForm(['user' => $user, 'scenario' => ChangePasswordForm::SCENARIO_ADMIN]);
        if ($model->load(Yii::$app->request->post())){
            if ($model->changePassword()) {
                Yii::$app->session->setFlash(self::SESSION_KEY_RESULT, self::RESULT_SUCCESS);
                Yii::$app->session->setFlash(self::SESSION_KEY_MESSAGE, '(' . $user->getID() . ') ' . $this->updateSuccessMessage);
                return $this->redirect(['index', 'id' => $id]);
            } else {
                Yii::$app->session->setFlash(self::SESSION_KEY_RESULT, self::RESULT_FAILED);
                Yii::$app->session->setFlash(self::SESSION_KEY_MESSAGE, '(' . $user->getID() . ') ' . $this->updateFailedMessage);
            }
        }
        return $this->render('change-password', ['model' => $model]);
    }
}
