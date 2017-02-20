<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/9 0009
 * Time: 下午 9:11
 */

namespace backend\modules\crm\models\supplier\model;


use backend\modules\fin\models\contract\ContractBaseForm;
use backend\modules\crm\models\supplier\model\SupplierConfig;
use yii\helpers\Json;

class SupplierContractForm extends ContractBaseForm
{
    public $supplier_name;
    public $supplier_manager_name;
    public $supplier_uuid;

    public function rules()
    {
        return array_merge([
            [['supplier_uuid'],'required'],
        ],parent::rules());
    }
}