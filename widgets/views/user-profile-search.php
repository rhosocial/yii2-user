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

use rhosocial\user\models\UserSearch;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $model UserSearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $formId string */
/* @var $formConfig array */
/* @var $fieldsView string */
$css = <<<EOT
div.required label.control-label:after {
    content: " *";
    color: red;
}
EOT;
$this->registerCss($css);
if (empty($fieldsView)) {
    $fieldsView = 'user-profile-search-fields';
}
?>

<div class="user-search">
    <?php $form = ActiveForm::begin($formConfig); ?>
<div class="row">
    <?= $this->render($fieldsView, ['form' => $form, 'model' => $model]) ?>
</div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('user', 'Search'), ['id' => "$formId-submit", 'class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('user', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
