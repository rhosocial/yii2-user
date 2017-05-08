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

use rhosocial\user\Profile;
use rhosocial\user\forms\RegisterForm;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $model RegisterForm */
/* @var $this yii\web\View */
$css = <<<EOT
div.required label.control-label:after {
    content: " *";
    color: red;
}
EOT;
$this->registerCss($css);
?>
<div class="site-login">
    <div class="row">
        <div class="col-lg-offset-2 col-lg-8">
            <p><?= Yii::t('user', 'Please fill out the following fields to register:') ?></p>
        </div>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'register-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-5\">{input}</div>\n<div class=\"col-lg-5\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'nickname')->textInput(['autofocus' => true]) ?>

    <?php if (is_string($model->username)): ?>
        <?= $form->field($model, 'username', ['enableAjaxValidation' => true])->textInput() ?>
    <?php endif; ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <?= $form->field($model, 'password_repeat')->passwordInput() ?>

        <?= $form->field($model, 'first_name')->textInput() ?>

        <?= $form->field($model, 'last_name')->textInput() ?>

        <?= $form->field($model, 'gender')->dropDownList(Profile::getGenderDescs()) ?>

        <?= $form->field($model, 'continue')->checkbox([
            'template' => "<div class=\"col-lg-offset-2 col-lg-10\">{input} {label}</div>\n<div class=\"col-lg-offset-2 col-lg-10\">{hint}</div>",
        ]) ?>

        <div class="form-group">
            <div class="col-lg-offset-2 col-lg-10">
                <?= Html::submitButton(Yii::t('user', 'Register'), ['class' => 'btn btn-primary', 'name' => 'register-button']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>
