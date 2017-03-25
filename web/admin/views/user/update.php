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
use rhosocial\user\User;
use rhosocial\user\Profile;
/* @var $this yii\web\View */
/* @var $user User */
$this->title = Yii::t('user', 'Update User') . ' (' . $user->getID() . ')';
$this->params['breadcrumbs'][] = Yii::t('user', 'Update User');
