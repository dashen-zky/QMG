<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/23 0023
 * Time: 下午 3:31
 */
namespace backend\modules\crm\models\project\model;
use Yii;
use yii\helpers\Json;
use backend\models\Config;
class ProjectConfig extends Config
{
    public $extraConfig ;
    const StatusTouching = 1;
    const StatusExecuting = 2;
    const StatusDone = 3;
    const StatusFailed = 4;
    const StatusExecuteApplying = 5; // 项目立项审核中
    const StatusDoneApplying = 6; // 项目结案审核中


    public function init()
    {
        $this->key = 'project_config';
        $this->extraConfig = [
            'projectStatus'=>[
                self::StatusTouching => '跟进中',
                self::StatusExecuting => '执行中',
                self::StatusDone => '已结案',
                self::StatusFailed => '失败',
                self::StatusExecuteApplying => '项目立项审核中',
                self::StatusDoneApplying => '项目结案审核中',
            ],
        ];
        parent::init();
    }

    public function updateConfig($formData)
    {
        if(empty($formData)) {
            return false;
        }
        $config = $this->generateConfig();
        foreach($formData as $key => $item) {
            if(isset($config[$key])) unset($config[$key]);
            $value = [];
            foreach ($item as $k => $itemValue) {
                if (empty($itemValue) || $itemValue === null
                    || !isset($itemValue['key']) || empty($itemValue['key'])) {
                    unset($value[$k]);
                    continue;
                }
                $value[$itemValue['key']] = $itemValue['value'];
            }
            $config[$key] = $value;
        }

        return $this->updateRecord([
            'uuid' => md5($this->key),
            'config'=>Json::encode($config),
            'uuid_key'=>$this->key,
        ]);
    }

    public function getList($key)
    {
        $list =  parent::getList($key);
        if(empty($list)) {
            $list = isset($this->extraConfig[$key])?$this->extraConfig[$key]:[];
        }
        return $list;
    }

    public function getAppointed($key, $index)
    {
        $value =  parent::getAppointed($key, $index);
        if(empty($value)) {
            $value =  isset($this->extraConfig[$key][$index])?$this->extraConfig[$key][$index]:'';
        }
        return $value;
    }

    // 生成项目编号的方法
    public function generateProjectCode() {
        $config = $this->config;
        if (empty($config)) {
            $config = $this->generateConfig();
        }

        if(!isset($config['project_code'])) {
            $code = date('ym',time()) . '00001';
        } else {
            $priCode = date('ym',time()) . '00001';
            $configCode = $config['project_code'];
            // 项目的编码按年清零
            $y1 = date('y',time());
            $y2 = substr($configCode,0,2);
            if($y1 > $y2) {
                $code = $priCode;
            } else {
                // 比较月份
                $m1 = date('m',time());
                $m2 = substr($configCode,2,2);
                // 如果当前月大于系统存储的月份，则使用现在的月份
                if($m1 > $m2) {
                    $code = $y2.$m1.substr($configCode,4);
                } else {
                    $code = $configCode;
                }
            }
        }
        return $code;
    }
}