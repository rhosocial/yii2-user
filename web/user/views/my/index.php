<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.me/
 * @copyright Copyright (c) 2016 - 2022 vistart
 * @license https://vistart.me/license/
 */

/* @var $this yii\web\View */
$this->title = Yii::t('user', 'My');
$this->params['breadcrumbs'][] = $this->title;
$markdown = <<<EOT
# Demo

The view is an example that you need to implement the controller and corresponding view(s) yourself and cover it.
EOT;
echo (new \cebe\markdown\GithubMarkdown)->parse($markdown);
