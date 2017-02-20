<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/3 0003
 * Time: 下午 4:15
 */

namespace backend\modules\hr\models\config;


use backend\models\Config;
use yii\helpers\Json;
class EmployeeDismissConfig extends Config
{
    public function init()
    {
        $this->key = 'employee_dismiss_config';
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
}