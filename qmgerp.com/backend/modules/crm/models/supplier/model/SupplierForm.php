<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/3 0003
 * Time: 下午 12:10
 */

namespace backend\modules\crm\models\supplier\model;


use backend\modules\crm\models\CRMBaseForm;
use yii\helpers\Json;
use backend\modules\fin\models\contract\ContractBaseRecord;
use backend\modules\crm\models\CRMBaseRecord;

class SupplierForm extends CRMBaseForm
{
    public $uuid;
    public $name;
    public $level;
    public $type;
    public $feature;
    public $status;
    public $term; // 供应商账期
    public $from;
    public $value_term; //价格有效期
    public $bottom_value; //保底金额
    public $manager_uuid; //管理人
    public $manager_name;
    public $description;
    public $remarks;
    public $refuse_reason; // 拒绝的理由
    public $path; // 附件存放路径
    public $attachment;
    public $config;
    public $contactUuids;
    public $file_name;
    public $code;
    public $contract_code;
    const codePrefix = "S";

    public function init()
    {
        $this->config = new SupplierConfig();
        $this->config->config = $this->config->generateConfig();
        parent::init();
    }

    public function rules()
    {
        return [
            [['name'],'required'],
            [['bottom_value'],'number'],
            ['attachment','file', 'maxFiles'=>10],
            // 文件名不能包含特殊字符，或是超过30个字符
            ['file_name','match','pattern'=>'/^[\x{4e00}-\x{9fa5}\w\-\.]{1,30}$/u','message'=>'文件名不能包含特殊字符或长度不宜超过30个字符']
        ];
    }

    public function generateContractCode($supplier_uuid, $supplier_code) {
        $contract = ContractBaseRecord::find()
            ->alias('t1')
            ->select(['max(t1.code) code'])
            ->leftJoin(CRMBaseRecord::CRMSupplierContractMap . ' t2', 't1.uuid=t2.contract_uuid')
            ->andWhere(['t2.supplier_uuid'=>$supplier_uuid])
            ->asArray()->one();
        $code = isset($contract['code'])?intval($contract['code'])+1:$supplier_code.'01';
        return $code;
    }

    public function generateCode() {
        $config = $this->config->config;
        if(!isset($config['code'])) {
            $config['code'] = date('Ym',time()) . '0001';
            (new SupplierConfig())->updateDateConfigByJsonString(Json::encode($config));
        } else {
            $priCode = intval(date('Ym',time()) . '0001');
            $code = $priCode >= intval($config['code'])
                ? $priCode:intval($config['code']);
            $config['code'] = $code;
            (new SupplierConfig())->updateDateConfigByJsonString(Json::encode($config));
        }
        $this->code = $config['code'];
        return $this->code;
    }

    public function generateSupplierCode() {
        $config = $this->config->config;
        if(!isset($config['supplier_code'])) {
            $config['supplier_code'] = $code = date('y',time()) . '0001';
            (new SupplierConfig())->updateDateConfigByJsonString(Json::encode($config));
        } else {
            $priCode =  date('y',time()) . '0001';
            $lastCode = $config['supplier_code'];
            $code = $priCode >= $lastCode ? $priCode:$lastCode;
            $config['supplier_code'] = $code;
            (new SupplierConfig())->updateDateConfigByJsonString(Json::encode($config));
        }

        return $code;
    }
}