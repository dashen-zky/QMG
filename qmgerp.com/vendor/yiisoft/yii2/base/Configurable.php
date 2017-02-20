<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\base;

/**
 * Configurable is the interfaces that should be implemented by classes who support configuring
 * its properties through the last parameter to its constructor.
 *
 * The interfaces does not declare any method. Classes implementing this interfaces must declare their constructors
 * like the following:
 *
 * ```php
 * public function __constructor($param1, $param2, ..., $customer-config = [])
 * ```
 *
 * That is, the last parameter of the constructor must accept a configuration array.
 *
 * This interfaces is mainly used by [[\yii\di\Container]] so that it can pass object configuration as the
 * last parameter to the implementing class' constructor.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0.3
 */
interface Configurable
{
}

