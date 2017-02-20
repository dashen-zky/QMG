<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22 0022
 * Time: 下午 3:42
 */
namespace backend\modules\crm\models\touchrecord;

use backend\modules\crm\models\CRMBaseForm;

class TouchRecordForm extends CRMBaseForm
{
    public $config;
    public $uuid;
    public $customer_uuid;
    public $predict_contract_time;
    public $predict_contract_value;
    public $time;
    public $next_touch_time;
    public $type;
    public $result;
    public $description;
    public $contact_uuid;

    public function rules() {
        return [
            [['time','contact_uuid'],'required'],
        ];
    }
    public function setConfig($config) {
        $this->config = $config;
    }

    public function getList($index) {
        return isset($this->config[$index])?$this->config[$index]:'';
    }

    public function handlerContactList($list) {
        $contactList = [];
        foreach($list as $value) {
            foreach($value as $key => $item) {
                if(isset($item['uuid'])) {
                    $contactList[$item['uuid']] = $item['name'];
                }
            }
        }
        return $contactList;
    }

}