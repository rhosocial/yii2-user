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
use rhosocial\user\web\admin\controllers\UserController;
use yii\bootstrap\Alert;
/* @var $this yii\web\View */
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('user', 'Admin'),
    'url' => ['user/index'],
];
$this->params['breadcrumbs'] = array_reverse($this->params['breadcrumbs']);
$this->beginContent('@app/views/layouts/main.php');
if (
    (($result = Yii::$app->session->getFlash(UserController::SESSION_KEY_REGISTER_RESULT)) !== null && ($message = Yii::$app->session->getFlash(UserController::SESSION_KEY_REGISTER_MESSAGE)) !== null) ||
    (($result = Yii::$app->session->getFlash(UserController::SESSION_KEY_DEREGISTER_RESULT)) !== null && ($message = Yii::$app->ssssion->getFlash(UserController::SESSION_KEY_DEREGISTER_MESSAGE)) !== null)    
        ) {
    if ($result == UserController::RESULT_SUCCESS) {
        echo Alert::widget([
            'options' => [
                'class' => 'alert-success',
            ],
            'body' => $message
        ]);
    } elseif ($result == UserController::RESULT_FAILED) {
        echo Alert::widget([
            'options' => [
                'class' => 'alert-failed',
            ],
            'body' => $message
        ]);
    } elseif ($message !== null) {
        echo Alert::widget([
            'options' => [
                'class' => 'alert-info',
            ],
            'body' => $message
        ]);
    }
}
echo $content;
$this->endContent();
