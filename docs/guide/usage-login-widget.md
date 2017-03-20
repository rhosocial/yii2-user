# Using the Login Widget

We've provided a `Login` widget for you to help you simplify your login operation.
This widget only provides the simplest functionality, and may not be able to meet the diverse needs, so the widget is for reference only.

## Preparation

You need to implement the controller and action yourself to render the `login` page and collect the `login` form, for example:

Controller / Action:
```php
use rhosocial\user\forms\LoginForm;

class SiteController extends Controller
{
    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        // You need to pass the new form to the view.
        return $this->render('login', [
            'model' => $model,
        ]);
    }
}
```

View:
```php
<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model rhosocial\user\forms\LoginForm */

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
// You need to pass the form to widget.
echo $result = \rhosocial\user\widgets\LoginFormWidget::widget(['model' => $model]);
```

Then you need to implement the "logout" function yourself, for example:

Controller / Action:
```php
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class SiteController extends Controller
{
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * In order to prevent cross-site request forgery attack,
     * We strongly recommend that you limit the "logout" action to a logged-in
     * user only and have access to the post method only.
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
}
```
