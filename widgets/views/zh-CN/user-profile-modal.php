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
use yii\bootstrap5\Modal;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $toggleButton array */
/* @var $user User */
$profile = $user->profile;
$profileClass = get_class($profile);
Modal::begin([
    'id' => 'user-profile-modal-' . $user->getID(),
    'header' => $profile->last_name . $profile->first_name,
    'toggleButton' => empty($toggleButton) ? [
        'tag' => 'a',
        'label' => $user->getID(),
    ] : $toggleButton,
]);
?>
<?= DetailView::widget([
    'model' => $user,
    'attributes' => [
        'id',
        [
            'label' => Yii::t('user', 'Gender'),
            'value' => $profileClass::getGenderDesc($profile->gender),
        ],
        'createdAt:datetime',
    ],
]) ?>
<p>
    <?= $profile->individual_sign ?>
</p>
<?php Modal::end() ?>
