<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-11-21
 * Time: 上午1:47
 */

namespace backend\models;


class Trigger
{
    public $handler;
    public function on($event, $handler) {
        $this->handler[$event][] = $handler;
    }

    public function trigger($event, $params) {
        if(!isset($this->handler[$event])) {
            return ;
        }

        $handlers = $this->handler[$event];
        foreach ($handlers as $handler) {
            call_user_func($handler, $params);
        }
    }
}