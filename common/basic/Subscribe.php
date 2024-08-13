<?php

declare(strict_types = 1);

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
                $events["{$eventName}." . lcfirst(substr($methodName, 2))] = [[$this, $methodName]];
                
            }
        }
        $event->listenEvents($events);
    }
}
