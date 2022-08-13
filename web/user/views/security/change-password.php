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

/* @var $this yii\web\View */
/* @var $model rhosocial\user\forms\ChangePasswordForm */
use rhosocial\user\web\user\controllers\SecurityController;
use yii\bootstrap\Alert;

$this->title = Yii::t('user', 'Change Password');
$this->params['breadcrumbs'][] = $this->title;
if (($result = Yii::$app->session->getFlash(SecurityController::SESSION_KEY_CHANGE_PASSWORD_RESULT)) !== null) {
    $message = Yii::$app->session->getFlash(SecurityController::SESSION_KEY_CHANGE_PASSWORD_MESSAGE);
    if ($result == SecurityController::CHANGE_PASSWORD_SUCCESS) {
        echo Alert::widget([
            'options' => [
                'class' => 'alert-success',
            ],
            'body' => $message
        ]);
    } elseif ($result == SecurityController::CHANGE_PASSWORD_FAILED) {
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
?>
<?= $result = \rhosocial\user\widgets\ChangePasswordFormWidget::widget(['model' => $model]); ?>
