# Internationalization

## Preparation

```php
$config = [
    ...
    'components' => [
        ...
        'i18n' => [
            'translations' => [
                'user*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@rhosocial/user/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'user' => 'user.php',
                    ],
                ],
            ],
        ],
        ...
    ],
    ...
];
```
