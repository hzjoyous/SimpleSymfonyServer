<?php

namespace App\EventSubscriber;

use App\Events\Test1925Event;
use App\Events\TestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * 这里解释一下哈，
 * 这个类之所以可以被调用是因为在 src/* 内除了被规避的一些文件以外其他文件都已被加载，这个可以从service.yaml中看到
 * 所以这个文件在任何位置都可以使用,当然是可识别的 比如 event 之类的
 */
class TestSubscriber implements EventSubscriberInterface
{
    public function onTestEvent(TestEvent $event)
    {
        dump(__FUNCTION__);
    }

    public function onTestEvent02(TestEvent $event)
    {
        dump(__FUNCTION__);
    }

    public function onTest1925Event(Test1925Event $event)
    {
        dump(__FUNCTION__);
    }

    public static function getSubscribedEvents()
    {
        return [

            Test1925Event::class => 'onTest1925Event',

            TestEvent::class => [
                ['onTestEvent', -1],
                ['onTestEvent02', 1]
            ],

        ];
    }
}
