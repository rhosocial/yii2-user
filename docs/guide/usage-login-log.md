# Using Login Log

We have provided you with login log function.

This feature is not turned on by default, you need to follow the following tips to enable:

## Preparation

### Migration

Please execute the following migration:
```
yii migrate --migrationPath=@vendor --migrationNamespaces=rhosocial\user\models\log\migrations --interactive=0
```

Or create the data table yourself according to the database table schema listed in the `\rhosocial\user\models\log\Login` model comment.

### Implement event triggered when logging in

For example:

```php
class User extends \rhosocial\base\models\web\User
{
    ...
    public function init()
    {
        parent::init();
        $this->on(EVENT_AFTER_LOGIN, [$this, 'onRecordLogon']);
    }

    /**
     * @param \yii\db\Event
     */
    public function onRecordLogon($event)
    {
        $user = $event->sender->identity;
        /* @var $user \app\models\User */
        $log = $user->create(Login::class, ['device' => 0x011]); // PC (Windows, Browser)
        try {
            return $log->save();
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
        }
    }
    ...
}
```

Then, you could add the above class to application components configuration:

```
    ...
    'components' => [
        ...
        'user' => [
            'class' => <Your Own User Component Class>
        ],
        ...
    ],
    ...
```

### Record limitation

We allow you to set an upper limit for the number of logs per user.

There are two types of upper limit, one is based on total number:

```php
$loginLog->limitType = Login::LIMIT_MAX;
$loginLog->limitMax = 100; // The upper limit is one hundred.
```

and the other is based on the validity period:

```php
$loginLog->limitType = Login::LIMIT_DURATION;
$loginLog->limitDuration = 90 * 86400; // Valid for 90 days.
```

While, you can set to enable both of above (default):
```php
$loginLog->limitType = Login::LIMIT_DURATION | Login::LIMIT_MAX;
```

and, the total number can also be unrestricted.

```php
$loginLog->limitType = Login::LIMIT_NO_LIMIT;
```

> Note: The total limit can not be less than 2, the duration limit can not be less than 1 day.
If you really need to break through these restrictions, you can implement the Login log model yourself and override the `init` method.