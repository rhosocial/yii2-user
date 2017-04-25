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

/* @var $this yii\web\View */
/* @var $model rhosocial\user\forms\RegisterForm */

$this->title = Yii::t('user', 'Register');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $result = \rhosocial\user\widgets\RegisterFormWidget::widget(['model' => $model]); ?>
<hr>
<p><?= Yii::t('user', 'If you are already a user, you can login.') ?></p>
<?= \yii\helpers\Html::a(Yii::t('user', 'Login'), [
    '/user/auth/login',
], [
    'class' => 'btn btn-primary',
]) ?>
