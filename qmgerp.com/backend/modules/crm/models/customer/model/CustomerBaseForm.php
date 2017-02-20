<?php

namespace backend\modules\crm\models\customer\model;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/13 0013
 * Time: 上午 12:44
 */
use backend\modules\crm\models\CRMBaseForm;

class CustomerBaseForm extends CRMBaseForm
{
    static public function genderList() {
        return  [
            1=>'男',
            2=>'女',
        ];
    }
}