# Integrate `Admin` Module

## Preparation

### Add `admin` module definition to application configuration

You need to specify the class name of the `admin` module in the 'modules' attribute of the application configuration:

```php
$config = [
    ...
    'modules' => [
        ...
        'admin' => [
            'class' => 'rhosocial\user\web\admin\Module',
        ],
        ...
    ],
    ...
];
```
