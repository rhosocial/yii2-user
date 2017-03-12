# rhosocial/yii2-user
Common User & Profile Models for Yii 2.

[![Latest Stable Version](https://poser.pugx.org/rhosocial/yii2-user/v/stable.png)](https://packagist.org/packages/rhosocial/yii2-user)
[![License](https://poser.pugx.org/rhosocial/yii2-user/license)](https://packagist.org/packages/rhosocial/yii2-user)
[![Reference Status](https://www.versioneye.com/php/rhosocial:yii2-user/reference_badge.svg)](https://www.versioneye.com/php/rhosocial:yii2-user/references)
[![Build Status](https://img.shields.io/travis/rhosocial/yii2-user.svg)](http://travis-ci.org/rhosocial/yii2-user)
[![Dependency Status](https://www.versioneye.com/php/rhosocial:yii2-user/dev-master/badge.png)](https://www.versioneye.com/php/rhosocial:yii2-user/dev-master)
[![Code Coverage](https://scrutinizer-ci.com/g/rhosocial/yii2-user/badges/coverage.png)](https://scrutinizer-ci.com/g/rhosocial/yii2-user/)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/rhosocial/yii2-user/badges/quality-score.png)](https://scrutinizer-ci.com/g/rhosocial/yii2-user/)
[![Code Climate](https://img.shields.io/codeclimate/github/rhosocial/yii2-user.svg)](https://codeclimate.com/github/rhosocial/yii2-user)

## Introduction
This package consists of two models:
- User: GUID, ID, Password Hash, IP Address, Timestamp, Password Refresh Token, Auth Key, Source, Status.
- Profile: GUID (corresponding with GUID), Nickname, Timestamp.

## Installation

The preferred way to install this extension is through [composer](https://getcomposer.org)

Either run

```
php composer.phar require rhosocial/yii2-user:dev-master
```

or add

```
"rhosocial/yii2-user": "dev-master"
```

to the require section of your composer.json

## Basic Usage

Once the extension is installed, simply use it in your code by:

```php
class User extends \rhosocial\user\User
{
    ...
}
```

and

```php
class Profile extends \rhosocial\user\Profile
{
    ...
}
```

But the above `Profile` class does not contain `email` and `phone` attributes.
The following `SimpleProfile` contains them (including corresponding rules):

```php
class Profile extends \rhosocial\user\SimpleProfile
{
    ...
}
```
further detailed usage seen in [here](docs/guide).