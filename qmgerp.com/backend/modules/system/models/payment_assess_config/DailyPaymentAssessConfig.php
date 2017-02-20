<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/22 0022
 * Time: 下午 4:45
 */
namespace backend\modules\system\models\payment_assess_config;
use backend\models\Config;
use backend\modules\fin\payment\models\PaymentConfig;
use backend\modules\hr\models\EmployeeBasicInformation;

class DailyPaymentAssessConfig extends Config
{
    public function init()
    {
        $this->key = 'daily-payment-assess-config';
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
            return $config;
        }
        $assess_uuid = $this->getAppointedValue($config, 'assess_uuid');
        $employeeGroup = EmployeeBasicInformation::find()->select(['uuid','name'])
            ->andWhere(['in', 'uuid', $assess_uuid])->asArray()->all();
        $employeeGroup = $this->transformForDropDownList($employeeGroup, 'uuid', 'name');
        // 从paymentConfig里面获取相关的信息
        $paymentConfig = new PaymentConfig();
        $map = $paymentConfig->getList('map');
        foreach($config as $index=>$item) {
            $config[$index]['assess_name'] =
                isset($employeeGroup[$item['assess_uuid']])?$employeeGroup[$item['assess_uuid']]:'';
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