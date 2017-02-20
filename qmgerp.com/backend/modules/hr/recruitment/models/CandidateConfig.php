<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-12-11
 * Time: 下午4:30
 */

namespace backend\modules\hr\recruitment\models;


use backend\models\Config;

class CandidateConfig extends Config
{
    public $extraConfig;
    const LocateCandidate = 1;
    const LocateTalent = 2; // 人才库
    const LocateBlackList = 3; // 黑名单
    const LocateHired = 4; // 已经别录用

    const StatusMatching = 1;
    const StatusNotifyInterView = 2; // 通知面试（通知面试）
    const StatusHire = 3; // 录用 （通过面试就是录用）
    const StatusDisHire = 4; //不录用 （加入到人才库，黑名单的都是不录用的）

    public function init()
    {
        $this->key = 'candidate-config';
        $this->extraConfig = [
            'location'=>[
                self::LocateCandidate => '候选人',
                self::LocateTalent => '人才库',
                self::LocateBlackList => '黑名单',
                self::LocateHired => '已录用',
            ],
            'status'=>[
                self::StatusMatching =>'匹配中',
                self::StatusNotifyInterView => '通知面试',
                self::StatusHire => '已录用',
                self::StatusDisHire => '不录用',
            ],
        ];
        parent::init();
    }

    public function updateConfig($formData)
    {

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