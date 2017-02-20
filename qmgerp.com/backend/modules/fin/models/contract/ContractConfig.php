<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/29 0029
 * Time: 下午 5:47
 */

namespace backend\modules\fin\models\contract;


use backend\models\Config;
use Yii;
use yii\helpers\Json;
class ContractConfig extends Config
{
    public function init()
    {
        $this->key = 'contract_config';
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
}