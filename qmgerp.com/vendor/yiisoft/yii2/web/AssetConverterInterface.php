<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\web;

/**
 * The AssetConverterInterface must be implemented by assets converter classes.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
interface AssetConverterInterface
{
    /**
     * Converts a given assets file into a CSS or JS file.
     * @param string $asset the assets file path, relative to $basePath
     * @param string $basePath the directory the $assets is relative to.
     * @return string the converted assets file path, relative to $basePath.
     */
    public function convert($asset, $basePath);
}
