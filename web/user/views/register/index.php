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
$this->params['breadcrumbs'] = [$this->title];
?>
<div class="row">
    <div class="col-lg-8 col-md-10">
        <?= $result = \rhosocial\user\widgets\RegisterFormWidget::widget(['model' => $model]); ?>
    </div>
    <div class="col-lg-4 col-md-2">
        <p>
            <?= Yii::t('user', 'We will assign an ID for you after registration.') ?>
        </p>
        <p><?= Yii::t('user', 'If you are already a user, you can login.') ?></p>
        <?= \yii\helpers\Html::a(Yii::t('user', 'Login'), [
            '/user/auth/login',
        ], [
            'class' => 'btn btn-primary',
        ]) ?>
    </div>
</div>
