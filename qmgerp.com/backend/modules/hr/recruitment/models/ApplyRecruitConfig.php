<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-12-8
 * Time: 下午11:50
 */

namespace backend\modules\hr\recruitment\models;


use backend\models\Config;

class ApplyRecruitConfig extends Config
{
    public $extraConfig;
    const StatusApplying = 1; // 申请中
    const StatusRecruiting = 2; // 招聘中
    const StatusAchieved = 3; // 完成招聘
    const StatusAssessRefused = 4; // 审核不通过

    const ReasonEmployeeLost = 1; // 流失更替
    const ReasonPositionIncrease = 2; // 岗位新增
    const ReasonStorage = 3; // 储备
    const ReasonOthers = 4; // 其他

    public function init() {
        $this->key = 'apply_recruit_config';
        $this->extraConfig = [
            'status'=>[
                self::StatusApplying => '申请中',
                self::StatusRecruiting => '招聘中',
                self::StatusAchieved => '完成招聘',
                self::StatusAssessRefused => '审核不通过',
            ],
            'reason'=>[
                self::ReasonEmployeeLost => '流失更替',
                self::ReasonPositionIncrease => '岗位新增',
                self::ReasonStorage => '储备',
                self::ReasonOthers => '其他',
            ]
        ];
    }

    public function updateConfig($formData)
    {
        // TODO: Implement updateConfig() method.
    }

    public function getList($key)
    {
        $list = isset($this->extraConfig[$key])?$this->extraConfig[$key]:null;
        if(empty($list)) {
            return parent::getList($key);
        }
        return $list;
    }

    public function getAppointed($key, $index)
    {
        $value =  isset($this->extraConfig[$key][$index])?$this->extraConfig[$key][$index]:null;
        if(empty($value)) {
            return parent::getAppointed($key, $index);
        }
        return $value;
    }
}