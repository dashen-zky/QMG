<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/7 0007
 * Time: 下午 9:43
 */

namespace backend\models;


class CompressHtml
{
    /**
     * 压缩html : 清除换行符,清除制表符,去掉注释标记
     * @param $string
     * @return 压缩后的$string
     * */
    static public function compressHtml($string) {
        $string = str_replace("\r\n", '', $string); //清除换行符
        $string = str_replace("\n", '', $string); //清除换行符
        $string = str_replace("\t", '', $string); //清除制表符
        $pattern = array (
            "/> *([^ ]*) *</", //去掉注释标记
            "/[\s]+/",
            "/<!--[^!]*-->/",
            "/\" /",
            "/ \"/",
            "'/\*[^*]*\*/'"
        );
        $replace = array (
            ">\\1<",
            " ",
            "",
            "\"",
            "\"",
            ""
        );
        return preg_replace($pattern, $replace, $string);
    }
}