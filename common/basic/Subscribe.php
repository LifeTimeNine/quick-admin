<?php

namespace basic;

use think\Event;

/**
 * 订阅事件基类
 * @package basic
 */
abstract class Subscribe
{
    public function subscribe(Event $event)
    {
        $events = [];
        $eventName = lcfirst(basename(str_replace('\\', '/', get_class($this))));
        foreach(get_class_methods($this) as $methodName) {
            if (strpos($methodName, 'on') === 0) {
                $events["{$eventName}:" . lcfirst(str_replace('on', '', $methodName))] = [[$this, $methodName]];
                
            }
        }
        $event->listenEvents($events);
    }
}
