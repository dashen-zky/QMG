<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11 0011
 * Time: 上午 11:53
 */

namespace backend\modules\system\models\payment_assess_config;


use backend\models\Config;
use backend\modules\fin\payment\models\PaymentConfig;
use backend\modules\hr\models\EmployeeBasicInformation;
class PayConfig extends Config
{
    public function init()
    {
        $this->key = 'pay-config';
        parent::init(); // TODO: Change the autogenerated stub
    }

    public function updateConfig($formData)
    {
        // TODO: Implement updateConfig() method.
    }

    public function generateConfigForShow()
    {
        $config =  parent::generateConfig();
        if(empty($config)) {
            return null;
        }

        $assess_uuid = $this->getAppointedValue($config, 'assess_uuid');
        $_assess_uuid = [];
        $uuids = [];

        foreach ($assess_uuid as $index => $uuid) {
            $_assess_uuid[$index] = explode(',', trim($uuid, ','));
            $uuids = array_merge($uuids, explode(',', trim($uuid, ',')));
        }
        $assess_uuid = $_assess_uuid;

        $employeeGroup = EmployeeBasicInformation::find()->select(['uuid','name'])
            ->andWhere(['in', 'uuid', $uuids])->asArray()->all();

        $employeeGroup = $this->transformForDropDownList($employeeGroup, 'uuid', 'name');
        // 从paymentConfig里面获取相关的信息
        $paymentConfig = new PaymentConfig();
        $map = $paymentConfig->getList('map');
//var_dump($assess_uuid);
        foreach($config as $index=>$item) {
            foreach ($assess_uuid[$index] as $uuid) {
//                var_dump((isset($config[$index]['assess_name'])?$config[$index]['assess_name'] . ',':''));
                $config[$index]['assess_name'] =
                    (isset($config[$index]['assess_name'])?$config[$index]['assess_name'] . ',':'') .
                    (isset($employeeGroup[$uuid])?$employeeGroup[$uuid]:'');
            }
            $config[$index]['condition_type'] = $paymentConfig->getAppointed('assess_condition',$item['type']);
            // 取得配置的字段key值
            if(is_array($map[$item['type']])) {
                $key = $map[$item['type']]['daily'];
            } else {
                $key = $map[$item['type']];
            }
            $config[$index]['condition_item'] = $paymentConfig->getAppointed($key, $item['purpose']);
        }
        return $config;
    }
}