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

namespace rhosocial\user\console\controllers;

use Faker\Factory;
use rhosocial\user\User;
use rhosocial\user\Profile;
use yii\console\Controller;
use yii\console\Exception;
use Yii;
use yii\helpers\Console;

/**
 * The simple operations associated with User.
 *
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class UserController extends Controller
{
    public $userClass;
    
    public $defaultAction = 'show';
    
    /**
     * Check and get valid User.
     * @return User
     * @throws Exception throw if User is not an instance inherited from `\rhosocial\user\User`.
     */
    protected function checkUserClass()
    {
        $userClass = $this->userClass;
        if (!class_exists($userClass)) {
            throw new Exception('User Class Invalid.');
        }
        if (!((new $userClass()) instanceof User)) {
            throw new Exception('User Class(' . $userClass . ') does not inherited from `\rhosocial\user\User`.');
        }
        return $userClass;
    }
    
    /**
     * Get user from database.
     * @param User|string|integer $user User ID.
     * @return User
     * @throws Exception
     */
    protected function getUser($user)
    {
        $userClass = $this->checkUserClass();
        if (is_numeric($user)) {
            $user = $userClass::find()->id($user)->one();
        } elseif (is_string($user) && strlen($user)) {
            $user = $userClass::find()->guid($user)->one();
        }
        if (!$user || $user->getIsNewRecord()) {
            throw new Exception('User Not Registered.');
        }
        return $user;
    }
    
    /**
     * Register new User.
     * @param string $password Password.
     * @param string $nickname If profile contains this property, this parameter is required.
     * @param string $firstName If profile contains this property, this parameter is required.
     * @param string $lastName If profile contains this propery, this parameter is required.
     * @return int
     * @throws Exception
     */
    public function actionRegister($password, $nickname = null, $firstName = null, $lastName = null)
    {
        $userClass = $this->checkUserClass();
        
        $user = new $userClass(['password' => $password]);
        /* @var $user User */
        $profile = $user->createProfile([
            'nickname' => $nickname,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ]);
        /* @var $profile Profile */
        try {
            is_null($profile) ? $user->register(): $user->register([$profile]);
        } catch (\Exception $ex) {
            throw new Exception($ex->getMessage());
        }
        echo "User Registered:\n";
        return $this->actionShow($user);
    }
    
    /**
     * Deregister user.
     * @param User|string|integer $user The user to be deregistered.
     * @return int
     */
    public function actionDeregister($user)
    {
        $user = $this->getUser($user);
        if ($user->deregister()) {
            echo "User (" . $user->getID() . ") Deregistered.\n";
            return static::EXIT_CODE_NORMAL;
        }
        return static::EXIT_CODE_ERROR;
    }
    
    /**
     * Show User Information.
     * @param User|string|integer $user User ID.
     * @param boolean $guid Show GUID?
     * @param boolean $passHash Show PasswordH Hash?
     * @param boolean $accessToken Show Access Token?
     * @param boolean $authKey Show Authentication Key?
     * @return int
     */
    public function actionShow($user, $guid = false, $passHash = false, $accessToken = false, $authKey = false)
    {
        $user = $this->getUser($user);
        echo Yii::t('app', 'User') . " (" . $user->getID() . "), " . Yii::t('app', 'registered at') . " (" . $user->getCreatedAt() . ")"
                . ($user->getCreatedAt() == $user->getUpdatedAt() ? "" : ", " . Yii::t('app', 'last updated at') . " (" . $user->getUpdatedAt() . ")") .".\n";
        if ($guid) {
            echo "GUID: " . $user->getGUID() . "\n";
        }
        if ($passHash) {
            echo "Password Hash: " . $user->{$user->passwordHashAttribute} . "\n";
        }
        if ($accessToken) {
            echo "Access Token: " . $user->getAccessToken() . "\n";
        }
        if ($authKey) {
            echo "Authentication Key: " . $user->getAuthKey() . "\n";
        }
        return static::EXIT_CODE_NORMAL;
    }
    
    /**
     * Show statistics.
     * @param User|string|integer $user User ID.
     * @return int
     */
    public function actionStat($user = null)
    {
        if ($user === null) {
            $count = User::find()->count();
            echo "Total number of user(s): " . $count . "\n";
            if ($count == 0) {
                return static::EXIT_CODE_NORMAL;
            }
            $last = User::find()->orderByCreatedAt(SORT_DESC)->one();
            /* @var $last User */
            echo "Latest user (" . $last->getID() . ") registered at " . $last->getCreatedAt() . "\n";
            return static::EXIT_CODE_NORMAL;
        }
        $user = $this->getUser($user);
        return static::EXIT_CODE_NORMAL;
    }
    
    /**
     * Assign a role to user or revoke a role.
     * @param User|string|integer $user User ID.
     * @param string $operation Only `assign` and `revoke` are acceptable.
     * @param string $role Role name.
     * @return int
     */
    public function actionRole($user, $operation, $role)
    {
        $user = $this->getUser($user);
        $role = Yii::$app->authManager->getRole($role);
        if ($operation == 'assign') {
            try {
                $assignment = Yii::$app->authManager->assign($role, $user);
            } catch (\yii\db\IntegrityException $ex) {
                echo "Failed to assign `" . $role->name . "`.\n";
                echo "Maybe the role has been assigned.\n";
                return static::EXIT_CODE_ERROR;
            }
            if ($assignment) {
                echo "`$role->name`" . " assigned to User (" . $user->getID() . ") successfully.\n";
            } else {
                echo "Failed to assign `" . $role->name . "`.\n";
            }
            return static::EXIT_CODE_NORMAL;
        }
        if ($operation == 'revoke') {
            $assignment = Yii::$app->authManager->revoke($role, $user);
            if ($assignment) {
                echo "`$role->name`" . " revoked from User (" . $user->getID() . ").\n";
            } else {
                echo "Failed to revoke `" . $role->name . "`.\n";
                echo "Maybe the role has not been assigned yet.\n";
            }
            return static::EXIT_CODE_NORMAL;
        }
        echo "Unrecognized operation: $operation.\n";
        echo "The accepted operations are `assign` and `revoke`.\n";
        return static::EXIT_CODE_ERROR;
    }
    
    /**
     * Assign a permission to user or revoke a permission.
     * @param User|string|integer $user User ID.
     * @param string $operation Only `assign` and `revoke` are acceptable.
     * @param string $permission Permission name.
     * @return int
     */
    public function actionPermission($user, $operation, $permission)
    {
        $user = $this->getUser($user);
        $permission = Yii::$app->authManager->getPermission($permission);
        if ($operation == 'assign') {
            try {
                $assignment = Yii::$app->authManager->assign($permission, $user);
            } catch (\yii\db\IntegrityException $ex) {
                echo "Failed to assign `" . $permission->name . "`.\n";
                echo "Maybe the permission has been assigned.\n";
                return static::EXIT_CODE_ERROR;
            }
            if ($assignment) {
                echo "`$permission->name`" . " assigned to User (" . $user->getID() . ") successfully.\n";
            } else {
                echo "Failed to assign `" . $permission->name . "`.\n";
            }
            return static::EXIT_CODE_NORMAL;
        }
        if ($operation == 'revoke') {
            $assignment = Yii::$app->authManager->revoke($permission, $user);
            if ($assignment) {
                echo "`$permission->name`" . " revoked from User (" . $user->getID() . ").\n";
            } else {
                echo "Failed to revoke `" . $permission->name . "`.\n";
                echo "Maybe the permission has not been assigned yet.\n";
            }
            return static::EXIT_CODE_NORMAL;
        }
        echo "Unrecognized operation: $operation.\n";
        echo "The accepted operations are `assign` and `revoke`.\n";
        return static::EXIT_CODE_ERROR;
    }

    /**
     * Validate password.
     * @param User|string|integer $user User ID.
     * @param password $password Password.
     * @return int
     */
    public function actionValidatePassword($user, $password)
    {
        $user = $this->getUser($user);
        $result = $user->validatePassword($password);
        if ($result) {
            echo "Correct.\n";
        } else {
            echo "Incorrect.\n";
        }
        return static::EXIT_CODE_NORMAL;
    }

    /**
     * Change password directly.
     * @param User|string|integer $user User ID.
     * @param string $password Password.
     * @return int
     */
    public function actionPassword($user, $password)
    {
        $user = $this->getUser($user);
        $user->applyForNewPassword();
        $result = $user->resetPassword($password, $user->getPasswordResetToken());
        if ($result) {
            echo "Password changed.\n";
        } else {
            echo "Password not changed.\n";
        }
        return static::EXIT_CODE_NORMAL;
    }

    /**
     * Confirm password in history.
     * This command will list all matching passwords in reverse order.
     * @param User|string|integer $user User ID.
     * @param string $password Password.
     * @return int
     */
    public function actionConfirmPasswordHistory($user, $password)
    {
        $user = $this->getUser($user);
        $passwordHistory = $user->passwordHistories;
        $passwordInHistory = 0;
        foreach ($passwordHistory as $pass) {
            if ($pass->validatePassword($password)) {
                $passwordInHistory++;
                echo "This password was created at " . $pass->getCreatedAt() . ".\n";
            }
        }
        if ($passwordInHistory) {
            echo "$passwordInHistory matched.\n";
            return static::EXIT_CODE_NORMAL;
        }
        echo "No password matched.\n";
        return static::EXIT_CODE_ERROR;
    }

    /**
     * Register users for testing.
     * @param int $total
     * @param string password
     * @return int
     * @throws Exception
     */
    public function actionAddTestUsers($total = 1000, $password = '123456')
    {
        echo "Registration Start...\n";
        $userClass = $this->checkUserClass();

        $faker = Factory::create(str_replace('-', '_', Yii::$app->language));
        $total = (int)$total;
        $acc = 0;
        $time = time();
        $genders = [Profile::GENDER_MALE, Profile::GENDER_FEMALE, Profile::GENDER_UNSPECIFIED];
        for ($i = 1; $i <= $total; $i++) {
            $user = new $userClass(['password' => $password]);
            $user->source = 'console_test';
            /* @var $user User */
            $gender = $faker->randomElement($genders);
            $profile = null;
            if ($gender == Profile::GENDER_MALE) {
                $profile = $user->createProfile([
                    'nickname' => $faker->titleMale,
                    'first_name' => $faker->firstNameMale,
                    'last_name' => $faker->lastName,
                ]);
            } elseif ($gender == Profile::GENDER_FEMALE) {
                $profile = $user->createProfile([
                    'nickname' => $faker->titleFemale,
                    'first_name' => $faker->firstNameFemale,
                    'last_name' => $faker->lastName,
                ]);
            } else {
                $profile = $user->createProfile([
                    'nickname' => $faker->title,
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                ]);
            }
            $profile->gender = $gender;
            /* @var $profile Profile */
            try {
                is_null($profile) ? $user->register() : $user->register([$profile]);
            } catch (\Exception $ex) {
                echo $ex->getMessage() . "\n";
                continue;
            }
            $acc++;
            if ($acc % 10 == 0) {
                $percent = (float)$i / $total * 100;
                echo "10 users registered($percent% finished).\n";
            }
        }
        $consumed = time() - $time;
        echo "Totally $acc users registered.\n";
        echo "Registration finished($consumed seconds consumed).\n";
        return static::EXIT_CODE_NORMAL;
    }

    /**
     * Deregister all users for testing.
     * @return int
     */
    public function actionRemoveAllTestUsers()
    {
        echo "Deregistration Start...\n";

        $userClass = $this->checkUserClass();
        $acc = 0;
        $time = time();
        foreach ($userClass::find()->andWhere(['source' => 'console_test'])->each() as $user) {
            try {
                $user->deregister();
            } catch (\Exception $ex) {
                echo $ex->getMessage() . "\n";
                continue;
            }
            $acc++;
            if ($acc % 10 == 0) {
                echo "10 users deregistered.\n";
            }
        }
        $consumed = time() - $time;
        echo "Totally $acc users deregistered.\n";
        echo "Deregistration finished($consumed seconds consumed).\n";
        return static::EXIT_CODE_NORMAL;
    }
}
