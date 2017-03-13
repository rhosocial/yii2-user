# Using Login Log

## Preparation

## Implement event triggered when logging in

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
        $sender = $event->sender;
        /* @var $sender static */
        $log = $sender->create(Login::class, ['device' => 0x011]); // PC (Windows, Browser)
        return $log->save();
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