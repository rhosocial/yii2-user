# Using the Login Method

We allow users to log in different ways, such as "User ID" and "Username".

We have already implemented login via "User ID" and "Username". Users can log in by
typing "User ID" or "Username" in the login box.

## Implement Your Method

You need to declare a class and implement the [MethodInterface](https://github.com/rhosocial/yii2-user/blob/master/models/LoginMethod/MethodInterface.php) first,
like following:

```php
namespace app\models\LoginMethod;

use rhosocial\user\models\LoginMethod\MethodInterface;

class Email implements MethodInterface
{
    public static function getUser($attribute)
    {
    ...
    }

    public static function validate($attribute)
    {
    ...
    }
}
```

Then you need to impelment `User` component yourself and override the `LoginPriority()` method,
like following:

```php
namespace app\components;

class User extends \rhosocial\user\components\User
{
    const LOGIN_BY_EMAIL = 'email';

    public function getLoginPriority()
    {
        $priority = parent::getLoginPriority();
        $priority[self::LOGIN_BY_EMAIL] = \app\models\LoginMethod\Email::class;
        return $priority;
    }
}
```

Modify the application configuration and change the class definition of `User` component to your own,
like following:

```php
$config = [
    ...
    'components' => [
        ...
        'user' => [
            'class' => \app\components\User::class
        ],
        ...
    ],
    ...
];
```
