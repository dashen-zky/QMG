<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/13 0013
 * Time: 上午 12:47
 */

namespace backend\modules\crm\models\customer\model;

use backend\modules\crm\models\CRMBaseRecord;
use backend\modules\fin\models\contract\ContractBaseForm;
use backend\modules\fin\models\contract\ContractBaseRecord;
use yii\helpers\Json;

class PublicCustomerForm extends CustomerBaseForm
{
    public $uuid;
    public $contactUuids; //存放联系人的id
    public $dutyUuids; // 存放负责人的id
    public $name;
    public $type;
    public $from;
    public $industry;
    public $city;
    public $address;
    public $website;
    public $contact;
    public $requireAnalyse;
    public $reason;
    public $description;
    public $remarks;
    public $intent_level; //意向度等级
    public $fund_level; // 以可合作的资金量大小来决定是不是大客户
    public $em_uuid; // 添加人
    public $time; // 添加时间
    public $last_touch_time; //最后一次联系时间
    public $status; // 客户状态
    public $config;
    public $contact_name;
    public $duty_name;
    public $code;
    public $full_name;
    public $sales_uuid;
    const codePrefix = 'C';

    public function rules()
    {
        return [
            [['full_name'],'required'],
        ];
    }

    public function setConfig($config) {
        $this->config = $config;
    }

    public  function statusList() {
        if (isset($this->config['status'])) {
            return $this->config['status'];
        }
        return [];
    }

    public  function intentLevelList() {
        if (isset($this->config['internLevel'])) {
            return $this->config['internLevel'];
        }
        return [];
    }

    public  function fundLevelList() {
        if (isset($this->config['fundLevel'])) {
            return $this->config['fundLevel'];
        }
        return [];
    }

    public  function typeList() {
        if (isset($this->config['type'])) {
            return $this->config['type'];
        }
        return [];
    }

    public  function fromList() {
        if (isset($this->config['from'])) {
            return $this->config['from'];
        }
        return [];
    }

    public  function industryList() {
        if (isset($this->config['industry'])) {
            return $this->config['industry'];
        }
        return [];
    }

    public function getList($key) {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }
        return [];
    }

    public function getAppointed($key, $index) {
        if(empty($index)) {
            return null;
        }
        return isset($this->config[$key][$index])?$this->config[$key][$index]:null;
    }

    // 生成项目编号的方法
    public function generateCode() {

        if(!isset($this->config['code'])) {
            $this->config['code'] = date('Ym',time()) . '0001';
            (new CustomerConfig())->updateDateConfigByJsonString(Json::encode($this->config));
        } else {
            $priCode = intval(date('Ym',time()) . '0001');
            $code = $priCode >= intval($this->config['code'])
                ? $priCode:intval($this->config['code']);
            $this->config['code'] = $code;
            (new CustomerConfig())->updateDateConfigByJsonString(Json::encode($this->config));
        }
        $this->code = $this->config['code'];
        return $this->code;
    }

    public function generateContractCode($customer_uuid, $customer_code) {
        $contract = ContractBaseRecord::find()
            ->alias('t1')
            ->select(['max(t1.code) code'])
            ->leftJoin(CRMBaseRecord::CRMCustomerContractMap . ' t2', 't1.uuid=t2.contract_uuid')
            ->andWhere(['t2.customer_uuid'=>$customer_uuid])
            ->asArray()->one();
        $code = isset($contract['code'])?intval($contract['code'])+1:$customer_code.'01';
        return $code;
    }
}