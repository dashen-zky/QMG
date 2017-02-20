<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/29 0029
 * Time: 下午 4:56
 */

namespace backend\modules\crm\models\project\model;


use backend\modules\fin\models\contract\ContractBaseForm;

class ProjectContractForm extends ContractBaseForm
{
    public $project_uuid;
    public $customer_name;
    public $project_name;
    public $project_manager_name;
    public $sales_name;

    public function rules()
    {
        return array_merge([
            [['project_uuid'],'required'],
        ],parent::rules());
    }
}