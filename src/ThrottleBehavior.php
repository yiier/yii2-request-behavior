<?php

/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2015-9-7 10:00:22
 * description: 插入行为
 */

namespace yiier\request;

use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\caching\Cache;
use yii\db\ActiveRecord;
use yii\web\Controller;

class ThrottleBehavior extends Behavior
{
    /**
     * @var string 错误提示
     */
    public $message = '回复内容不能重复提交';
    /**
     * @var int 持续时间 单位秒
     */
    public $duration = 10;
    /**
     * @var Cache|string the cache object or the application component id of the cache object
     */
    public $cache = 'cache';

    /**
     * @inhertidoc
     */
    public function init()
    {
        if (is_string($this->cache)) {
            //if the cache component is not defined, will throw the exception
            $this->cache = Yii::$app->get($this->cache);
        }
        if (!($this->cache instanceof Cache)) {
            throw new InvalidConfigException('You must set the cache component!');
        }
    }

    /**
     * 事件
     * @return array
     */
    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'checkRepeatSubmit',
        ];
    }

    public function checkRepeatSubmit($event)
    {
//        $post = \Yii::$app->request->bodyParams;
        //        unset($post['_csrf']);
        //        $key = md5(json_encode($post));
        $key = Yii::$app->controller->action->uniqueId;

        if ($this->cache->get($key) === false) {
            // 没有数据  证明是初次进入 或者缓存失效
        } else {
            // 缓存还没失效
            // 是重复提交
            // 此处如果抛异常是防止过快提交的
            Yii::$app->getSession()->setFlash('warning', $this->message);
            Yii::$app->getResponse()->redirect(Yii::$app->request->referrer);
            $event->isValid = false;
        }

        Event::on(ActiveRecord::className(), ActiveRecord::EVENT_AFTER_INSERT, function (Event $event) use ($key) {
            // 这里证明AR已经插入了
            // 此处可以写入缓存
            $this->cache->set($key, time(), $this->duration);
        });
    }

}
