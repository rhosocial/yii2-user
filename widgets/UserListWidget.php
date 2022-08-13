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

namespace rhosocial\user\widgets;

use rhosocial\user\grid\ActionColumn;
use Yii;
use yii\base\Widget;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

class UserListWidget extends Widget
{
    /**
     * @var ActiveDataProvider
     */
    public $dataProvider;
    /**
     * @var array|null|string ActionColumn class configuration. Null if you do not need it.
     * 'default' if you want to use the default configuration.
     * Note: If you want to use your own ActionColumn class configuration, please do not
     * forget to attach the 'class' key.
     */
    public $actionColumn;

    /**
     * @var array|null Additional columns' configuration arrays.
     * It will be appended after the existed columns.
     * If you do not need additional columns, please set null.
     */
    public $additionalColumns;

    const ACTION_COLUMN_DEFAULT = 'default';

    /**
     * @var array Column visibles.
     */
    public $visible = [
        'guid' => false,
    ];

    /**
     * @var boolean|array Tips.
     * If you do not want to show tips, please set false.
     * If you want to show default tips, please set true.
     * If you want to show tips including default ones and your owns, please set an array contains all tip text.
     */
    public $tips = true;

    /**
     * Initialize attributes.
     */
    public function init()
    {
        if (empty($this->dataProvider)) {
            throw new ServerErrorHttpException('Invalid User Provider.');
        }
        if (is_string($this->actionColumn) && strtolower($this->actionColumn) == self::ACTION_COLUMN_DEFAULT) {
            $this->actionColumn = [
                'class' => ActionColumn::class,
                'header' => Yii::t('user', 'Action'),
                'useIcon' => false,
                'urlCreator' => function ($action, $model, $key, $index, ActionColumn $column) {
                    /* @var $model User */
                    if ($action == 'view') {
                        return Url::to(['view', 'id' => $model->id]);
                    } elseif ($action == 'update') {
                        return Url::to(['update', 'id' => $model->id]);
                    } elseif ($action == 'delete') {
                        return Url::to(['deregister', 'id' => $model->id]);
                    }
                    return '#';
                },
                'visibleButtons' => [
                    'view' => Yii::$app->user->can('viewUser'),
                    'update' => Yii::$app->user->can('updateUser'),
                    'delete' => Yii::$app->user->can('deleteUser'),
                ],
            ];
        }
        parent::init();
    }

    /**
     * Run action.
     * @return string
     */
    public function run()
    {
        return $this->render('user-list', [
            'dataProvider' => $this->dataProvider,
            'additionalColumns' => $this->additionalColumns,
            'actionColumn' => $this->actionColumn,
            'visible' => $this->visible,
            'tips' => $this->tips,
        ]);
    }
}
