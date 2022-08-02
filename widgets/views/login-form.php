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

use rhosocial\user\forms\LoginForm;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $model LoginForm */
/* @var $this yii\web\View */
/* @var $tip string */
$css = <<<EOT
div.required label.col-form-label:after {
    content: " *";
    color: red;
}
EOT;
$this->registerCss($css);
?>
<div class="site-login">
    <?php if (!empty($tip)) : ?>
        <p><?= $tip ?></p>
    <?php endif; ?>
    <div class="col-lg-offset-2 col-lg-10">
        <p><?= Yii::t('user', 'Please fill out the following fields to login:') ?></p>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
    ]); ?>
    <?php
    $placeholder = Yii::t('user', 'User ID');
    if (array_key_exists(\rhosocial\user\components\User::LOGIN_BY_USERNAME, Yii::$app->user->getLoginPriority())) {
        $placeholder .= ' / ' . Yii::t('user', 'Username');
    }
    ?>
        <?= $form->field($model, 'id')->textInput(['autofocus' => true, 'placeholder' => $placeholder]) ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <?= $form->field($model, 'rememberMe')->checkbox([
            'template' => "<div class=\"col-lg-offset-2 col-lg-10\">{input} {label}</div>\n<div class=\"col-lg-offset-2 col-lg-10\">{hint}</div>",
        ]) ?>

        <div class="form-group">
            <div class="col-lg-offset-2 col-lg-10">
                <?= Html::submitButton(Yii::t('user', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>
