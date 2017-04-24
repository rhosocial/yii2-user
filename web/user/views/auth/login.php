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
/* @var $model rhosocial\user\forms\LoginForm */

$this->title = Yii::t('user', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $result = \rhosocial\user\widgets\LoginFormWidget::widget(['model' => $model]); ?>
<hr>
<p><?= Yii::t('user', 'If you are not a user, you can register first.') ?></p>
<?= \yii\helpers\Html::a(Yii::t('user', 'Register'), [
    '/user/register/index',
], [
   'class' => 'btn btn-primary',
]) ?>
