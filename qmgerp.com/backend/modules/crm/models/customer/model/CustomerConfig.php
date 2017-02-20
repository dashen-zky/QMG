<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/13 0013
 * Time: 下午 5:21
 */

namespace backend\modules\crm\models\customer\model;


use Yii;
use backend\models\Config;
use yii\helpers\Json;

class CustomerConfig extends Config
{
    public $extraConfig;
    const StatusWaitingTouch = 1;
    const StatusTouching = 2;
    const StatusCooperating = 3;
    const StatusDone = 4;

    const PotentialLevel = 5; // 潜在客户
    const GeneralLevel = 6; // 普通客户
    const ImportantLevel = 7; // 重点客户
    const KALevel = 8; // KA客户
    const Invalid = 9;


    public function init()
    {
        $this->extraConfig = [
            'status'=>[
                self::StatusWaitingTouch => '待跟进',
                self::StatusTouching => '跟进中',
                self::StatusCooperating => '合作中',
                self::StatusDone => '已结案',
            ],
            'level' => [
                self::PotentialLevel => '潜在客户',
                self::GeneralLevel => '普通客户',
                self::ImportantLevel => '重点客户',
                self::KALevel => 'KA客户',
                self::Invalid => '无效客户'
            ],
        ];
        $this->key = 'customer_config';
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

    public function generateCustomerCode() {
        if (empty($this->config)) {
            $this->config = $this->generateConfig();
        }
        if(!isset($this->config['customer_code'])) {
            $code = intval(date('y',time()) . '0001');
        } else {
            $priCode =  intval(date('y',time()) . '0001');
            $lastCode = intval($this->config['customer_code']);
            $code = $priCode >= $lastCode ? $priCode:$lastCode;
        }

        return $code;
    }
}