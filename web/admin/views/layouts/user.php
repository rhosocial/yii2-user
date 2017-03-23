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
$this->params['breadcrumbs'][] = [
    'label' => 'Admin',
    'url' => ['user/index'],
];
$this->params['breadcrumbs'] = array_reverse($this->params['breadcrumbs']);
$this->beginContent('@app/views/layouts/main.php');
echo $content;
$this->endContent();
