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

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class UserController extends Controller
{
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [ // Disallow all unauthorized users to access this controller.
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [ // Disallow non-admin user to access this controller.
                        'allow' => false,
                        'matchCallback' => function ($rule, $action) {
                            return !Yii::$app->authManager->checkAccess(Yii::$app->user->identity, 'admin');
                        },
                        'denyCallback' => function ($rule, $action) {
                            throw new UnauthorizedHttpException('You are not an administrator and have no access to this page.');
                        },
                    ],
                    [ // Disallow admin user to access deregister action directly, only `POST` accepted.
                        'actions' => ['deregister'],
                        'allow' => false,
                        'matchCallback' => function ($rule, $action) {
                            return strtoupper(Yii::$app->request->getMethod()) != 'POST';
                        },
                        'denyCallback' => function ($rule, $action) {
                            throw new MethodNotAllowedHttpException('You cannot access this page directly.');
                        },
                    ],
                    [ // Allow admin user to access other views.
                      // This is a final rule, if you want to add other rules, please put it before this rule.
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $class = Yii::$app->user->identityClass;
        if (!class_exists($class)) {
            return $this->render('index', ['dataProvider' => null]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $class::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionRegisterNewUser()
    {
        return $this->render('register-new-user');
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
            throw new ForbiddenHttpException('You cannot deregister yourself.');
        }
        return $this->redirect(['index']);
    }

    public function actionView($id)
    {
        return $this->render('view');
    }

    public function actionUpdate($id)
    {
        return $this->render('update');
    }
}