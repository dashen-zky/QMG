<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/2 0002
 * Time: 下午 9:09
 */
namespace backend\modules\crm\models\supplier\model;
use backend\models\Config;
use Yii;
use yii\helpers\Json;
class SupplierConfig extends Config
{
    const NormalContract = "S";
    const FrameworkContract = "KS";
    const Allocated = 2;
    const UnAllocate = 1;
    const StatusWaitForAssess = 3; //待审核
    const StatusAssessFailed = 4; // 审核失败
    const StatusAssessSuccess = 5; // 通过审核
    public $extraConfig; // 这个是为了记录一些不需要存储在数据库里面的配置数据
    public function init()
    {
        $this->key = 'supplier_config';
        $this->extraConfig = [
            'allocate'=> [
                self::UnAllocate=>'未被分配',
                self::Allocated=>'已被分配',
            ],
            'status'=>[
                self::StatusWaitForAssess => '待审核',
                self::StatusAssessFailed => '审核不通过',
                self::StatusAssessSuccess => '审核通过',
            ],
        ];
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

    public static function contractTypeList() {
        return [
            self::NormalContract=>'普通合同',
            self::FrameworkContract=>'框架合同',
        ];
    }

    public static function getAllocate($key) {

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
}