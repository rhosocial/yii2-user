# Using the Password History model

The password history model automatically records the new password after registering and changing the password.

## Preparation

Before using the password history features, we need to specify the password history class name:

```php
class User extends \rhosocial\user\models\User
{
    ...
    public $passwordHistoryClass = <Password History Class Name>;
    ...
}
```

If you do not specify, the password history feature is not enabled by default.
The following are based on enabling the password history features.

## Allow used password

By default, it is allowed to continue using the passwords that have been used.
That is:
```php
class User extends \rhosocial\user\models\User {
    ...
    $allowUsedPassword = true;
    ...
}
```

If you want to check whether the password is used, you can call the `PasswordHistory::isUsed()` method to achieve.

If you want to make sure that the password you have used is not recorded, set the
`allowUsedPassword` property of `User` class to false.

At this point, if you save a password has been used, the `save` process will be blocked,
the default error message is 'This password has been used.'.

If you want to modify it, you can assign `passwordUsedMessage` property of `User`
class with your own message:
```php
class User extends \rhosocial\user\models\User {
    ...
    $passwordUsedMessage = <Your Own Message>;
    ...
}
```