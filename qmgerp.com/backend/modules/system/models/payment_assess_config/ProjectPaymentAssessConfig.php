<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/22 0022
 * Time: 下午 11:40
 */

namespace backend\modules\system\models\payment_assess_config;


use backend\models\Config;
use backend\modules\fin\payment\models\PaymentConfig;
use backend\modules\hr\models\EmployeeBasicInformation;

class ProjectPaymentAssessConfig extends Config
{
    public function init()
    {
        $this->key = 'project-payment-assess-config';
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
                $key = $map[$item['type']]['project'];
            } else {
                $key = $map[$item['type']];
            }

            if (!isset($item['purpose'])) {
                $config[$index]['condition_item'] = null;
                continue;
            }

            if (!is_array($key)) {
                $config[$index]['condition_item'] = $paymentConfig->getAppointed($key, $item['purpose']);
                continue;
            }

            foreach ($key as $k) {
                $config[$index]['condition_item'] = $paymentConfig->getAppointed($k, $item['purpose']);
                if (!empty($config[$index]['condition_item'])) {
                    break;
                }
            }
        }

        return $config;
    }
}