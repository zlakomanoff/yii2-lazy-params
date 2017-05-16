Yii2 lazy params
===================
Yii2 zlay params

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist zlakomanoff/yii2-lazy-params "*"
```

or add

```
"zlakomanoff/yii2-lazy-params": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Simple config
```php
'lp' => 'zlakomanoff\lazyparams\Component'
```

Simple usage
```php
<?= Yii::$app->lp->get('option', 'default value') ?>
```

Advanced config

```php
'lp' => [
    'class' => 'zlakomanoff\lazyparams\Component',
    'tableName' => 'dynamic_options',
    'keyColumn' => 'keyword',
    'valueColumn' => 'value',
    'liquidMode' => false,
    'enableCache' => false,
    'cacheDefaultValues' => false,
    'cacheComponent' => 'cache',
    'dbComponent' => 'db'
]
```

Advanced usage

```php
echo Yii::$app->lp['param one'];
echo Yii::$app->lp['param two']['default value'];
echo Yii::$app->lp->paramThree('default value');
echo Yii::$app->lp->get('param four', 'default value');
```
