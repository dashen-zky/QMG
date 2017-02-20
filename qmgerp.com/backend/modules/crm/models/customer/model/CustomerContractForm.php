<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/2 0002
 * Time: 下午 12:20
 */

namespace backend\modules\crm\models\customer\model;


use backend\modules\fin\models\contract\ContractBaseForm;

class CustomerContractForm extends ContractBaseForm
{
    public $customer_uuid;
    public $customer_name;
    public $sales_name;

    public function rules()
    {
        return array_merge([
            [['customer_uuid'],'required'],
        ],parent::rules());
    }
}