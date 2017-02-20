<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-12-20
 * Time: 上午10:50
 */

namespace backend\modules\crm\models\project\record;


class ProjectBriefConfig
{
    const StatusApplying = 1;
    const StatusAssessed = 2;
    const StatusAssessRefused = 3;
    public static $extraConfig = [
        'status'=>[
            self::StatusApplying => '审核中',
            self::StatusAssessed => '审核通过',
            self::StatusAssessRefused => '审核没通过',
        ],
    ];

    public static function getList($key) {
        return isset(self::$extraConfig[$key])?self::$extraConfig[$key]:null;
    }

    public static function getAppointed($key, $index) {
        return isset(self::$extraConfig[$key][$index])?self::$extraConfig[$key][$index]:null;
    }
}