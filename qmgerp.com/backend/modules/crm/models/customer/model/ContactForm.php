<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/17 0017
 * Time: 下午 12:59
 */

namespace backend\modules\crm\models\customer\model;


use backend\models\BaseRecord;
use backend\modules\crm\models\customer\model\CustomerBaseForm;

class ContactForm extends CustomerBaseForm
{
    const CustomerContact = 1;
    const CustomerDuty = 2;
    const ProjectContact = 1;
    const ProjectDuty = 2;

    public $name;
    public $gender;
    public $position;
    public $phone;
    public $weichat;
    public $qq;
    public $office_phone;
    public $address;
    public $email;
    public $type;
    public function rules()
    {
        return [];
    }
    static public function customerTypeList() {
        return [
            self::CustomerContact=>'联系人',
            self::CustomerDuty=>'负责人',
        ];
    }

    static public function enableList() {
        return [
            BaseRecord::Enable => '在职',
            BaseRecord::Disable => '离职',
        ];
    }

    static public function getType($index) {
        $list = self::customerTypeList();
        return $list[$index];
    }

    static public function projectTypeList() {
        return [
            self::ProjectContact=>'联系人',
            self::ProjectDuty=>'负责人',
        ];
    }
}