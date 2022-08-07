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

/* @var $panel rhosocial\user\debug\panels\UserPanel */
?>
<div class="yii-debug-toolbar__block">
    <a href="<?= $panel->getUrl() ?>">
        <?php if (Yii::$app->user->isGuest) : ?>
            <span class="yii-debug-toolbar__label">Guest</span>
        <?php else : ?>
            User <span class="yii-debug-toolbar__label yii-debug-toolbar__label_info"><?= Yii::$app->user->id ?></span>
        <?php endif; ?>
    </a>
</div>
