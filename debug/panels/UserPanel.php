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

namespace rhosocial\user\debug\panels;

use Yii;
use yii\data\ArrayDataProvider;
use yii\debug\Panel;
use yii\db\ActiveRecord;

/**
 * Debugger panel that collects and displays user data.
 *
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class UserPanel extends Panel
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'User';
    }

    /**
     * @inheritdoc
     */
    public function getSummary()
    {
        return Yii::$app->view->render('@rhosocial/user/debug/views/user/summary', ['panel' => $this]);
    }

    /**
     * @inheritdoc
     */
    public function getDetail()
    {
        return Yii::$app->view->render('@rhosocial/user/debug/views/user/detail', ['panel' => $this]);
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        $data = Yii::$app->user->identity;

        if (!isset($data)) {
            return ;
        }

        $authManager = Yii::$app->getAuthManager();

        $rolesProvider = null;
        $permissionsProvider = null;

        if ($authManager) {
            $rolesProvider = new ArrayDataProvider([
                'allModels' => $authManager->getRolesByUser($data),
            ]);

            $permissionsProvider = new ArrayDataProvider([
                'allModels' => $authManager->getPermissionsByUser($data),

            ]);
        }

        if (!class_exists($data->loginLogClass)) {
            $loginLogProvider = 'Login Log Class Not Specified.';
        } else {
            $loginLogProvider = new ArrayDataProvider([
                'allModels' => $data->getLoginLogs(),
            ]);
        }

        $attributes = array_keys(get_object_vars($data));
        if ($data instanceof ActiveRecord) {
            $attributes = array_keys($data->getAttributes());
        }
        
        return [
            'identity' => $data,
            'attributes' => $attributes,
            'rolesProvider' => $rolesProvider,
            'permissionsProvider' => $permissionsProvider,
            'loginLogProvider' => $loginLogProvider,
        ];
    }
}
