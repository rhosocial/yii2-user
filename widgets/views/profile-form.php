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

use rhosocial\base\helpers\Timezone;
use rhosocial\user\models\Profile;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $model Profile */
/* @var $this yii\web\View */
$css = <<<EOT
div.required label.control-label:after {
    content: " *";
    color: red;
}
EOT;
$this->registerCss($css);
$profileClass = Yii::$app->user->identity->profileClass;
?>
<div class="site-login">
    <p><?= Yii::t('user', 'Please fill out the following fields to update profile:') ?></p>

    <?php $form = ActiveForm::begin([
        'id' => 'profile-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

        <?= $form->field($model, 'nickname')->textInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'first_name')->textInput() ?>

        <?= $form->field($model, 'last_name')->textInput() ?>

        <?= $form->field($model, 'gender')->dropDownList($profileClass::getGenderDescs()) ?>

        <?= $form->field($model, 'timezone')->dropDownList(Timezone::generateList()) ?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton(Yii::t('user', 'Update'), ['class' => 'btn btn-primary', 'name' => 'update-button']) ?>
                <?= Html::resetButton(Yii::t('user', 'Reset'), ['class' => 'btn btn-default', 'name' => 'reset-button']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>
