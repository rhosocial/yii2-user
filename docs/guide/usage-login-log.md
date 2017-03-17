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