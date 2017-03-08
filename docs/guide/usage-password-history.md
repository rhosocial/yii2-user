# Using the Password History model

The password history model automatically records the new password after registering and modifying the password.

## Preparation

Before using the password history features, we need to specify the password history class name:

```php
class User extends \rhosocial\user\User
{
    ...
    public $passwordHistoryClass = <Password History Class Name>;
    ...
}
```

If you do not specify, the password history feature is not enabled by default.

## Allow duplicate password

By default, the password that has been used would be continued to use.

If you want to check whether the password is used, you can call the `isUsed` method to achieve.

If you want to make sure that the password you have used is not recorded, set the `allowDuplicatePassword` property to false.
At this point, if you have saved a password has been used, the `save` process will be blocked.