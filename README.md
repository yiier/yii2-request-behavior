Request Behavior for Yii 2
==========================
The request behavior for the Yii framework

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yiier/yii2-request-behavior "*"
```

or add

```
"yiier/yii2-request-behavior": "dev-master"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your Controller code by  :

```php
public function behaviors()
{
    return [
        // code ...
        [
            'class' => ThrottleBehavior::className(),
            'message'=>'您操作太频繁了，10秒内不能重复操作。'
        ],
    ];
}
```