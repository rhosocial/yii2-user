# Using the Profile model

## Concepts

- table name: `{{%profile}}`
- The GUID is referenced by the `user` table.

## Preparation

- Create table for this model.
- Implement your own Profile model.
  - Customize table name.
  - Specify user class if needed (in `init()` method).
  - Customize rules.
  - Customize attribute labels.
  - Customize behaviors.

## Built-in Attributes

- Email (`SimpleProfile` only)
- Phone (`SimpleProfile` only)
- First name and Last name
- Individual Sign

Each built-in attribute has a rules, if you do not use them, please override the corresponding rules method and return empty array.

## Implement your own Profile model

Regardless of whether the current model has met your needs, we do not recommend
that you use the model as a profile model. You need to implement your own profile model:

```php
class Profile extends \rhosocial\user\models\Profile
{
}
```

## Best Practices

- Please do not use this class directly, regardless of whether the current model has met your needs.
- Please do not override `rules()` method to attach additional rules, merge the parent's instead, or you know the consequences of doing so.
- Please do not override `behaviors()` method to attach additional behaviors, merge the parent's instead, or you know the consequences of doing so.
- If you do not want to use some built-in attributes, please override the corresponding
rules and return empty array.
- If you want to add your own attributes, please implement corresponding
rules and merge it with parent's.
