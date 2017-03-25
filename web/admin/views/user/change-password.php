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
/* @var $this yii\web\View */
/* @var $model ChangePasswordForm */
$this->title = Yii::t('user', 'Change Password');
$this->params['breadcrumbs'][] = $this->title;
echo ChangePasswordFormWidget::widget(['model' => $model]);
