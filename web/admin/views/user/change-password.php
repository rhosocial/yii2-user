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
use rhosocial\user\widgets\ChangePasswordFormWidget;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model ChangePasswordForm */
$this->title = Yii::t('user', 'Change Password');
$this->params['breadcrumbs'][] = $this->title;
echo ChangePasswordFormWidget::widget(['model' => $model]);
?>
<div class="row">
    <div class="col-md-3">
        <?= Html::a(Yii::t('user', 'Back to User List'), [
            'index',
        ], ['class' => 'btn btn-primary']) ?>
    </div>
</div>
