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
use rhosocial\user\web\user\Module;
use yii\bootstrap\Alert;
/* @var $this yii\web\View */
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('user', 'User'),
];
$this->params['breadcrumbs'] = array_reverse($this->params['breadcrumbs']);
$this->beginContent('@app/views/layouts/main.php');
if (($result = Yii::$app->session->getFlash(Module::SESSION_KEY_RESULT)) !== null) {
    $message = Yii::$app->session->getFlash(Module::SESSION_KEY_MESSAGE);
    if ($result == Module::RESULT_SUCCESS) {
        echo Alert::widget([
            'options' => [
                'class' => 'alert-success',
            ],
            'body' => $message
        ]);
    } elseif ($result == Module::RESULT_FAILED) {
        echo Alert::widget([
            'options' => [
                'class' => 'alert-danger',
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
