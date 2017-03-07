# Installation

## Migrations

If you want to use built-in tables, you can execute built-in migrations.

Before you execute built-in migrations, you need to add our migration namespace
to the `migrationNamespaces` attribute of `migrate` controller in the `controllerMap`
attribute of the console application:
```
'controllerMap' => [
    'migrate' => [
        'class' => 'yii\console\controllers\MigrateController',
        'migrationNamespaces' => [
            'rhosocial\user\migrations',
        ],
    ],
],
```

Then you can execute the following command in the console:
```
yii migrate/up
```

You may see the following tips:
```
Total 2 new migrations to be applied:
        rhosocial\user\migrations\M170304140437CreateUserTable
        rhosocial\user\migrations\M170304142349CreateProfileTable

Apply the above migrations? (yes|no) [no]:
```

You can enter 'yes' to apply above migrations.

If you see more than two of those above migrations, you need to specify the migration like following:
```
yii migrate/to rhosocial\user\migrations\M170304140437CreateUserTable
```

