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

use rhosocial\user\forms\ChangePasswordForm;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $model ChangePasswordForm */
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
    <p><?= Yii::t('user', 'Please fill out the following fields to change password:') ?></p>

    <?php $form = ActiveForm::begin([
        'id' => 'change-password-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>
<?php if ($model->scenario == ChangePasswordForm::SCENARIO_DEFAULT): ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
<?php endif; ?>
        <?= $form->field($model, 'new_password')->passwordInput() ?>

        <?= $form->field($model, 'new_password_repeat')->passwordInput() ?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton(Yii::t('user', 'Change Password'), ['class' => 'btn btn-primary', 'name' => 'change-password-button']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>
