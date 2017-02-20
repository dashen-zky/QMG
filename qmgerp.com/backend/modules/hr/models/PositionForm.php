<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3 0003
 * Time: 下午 6:21
 */

namespace backend\modules\hr\models;

use backend\modules\hr\models\HrBaseForm;
class PositionForm extends HrBaseForm
{
    public $uuid;
    public $name;
    public $min_salary;
    public $max_salary;
    public $de_uuid;
    public $departmentName;
    public $departmentLevel; // 所在部门的等级
    public $remarks;
    public $duty;
    public $requirement;
    public $attachment;
    public $role; // 在部门里面的角色
    const DutyRole = 3;
    const LeadRole = 2;
    const GeneralRole = 1;
    public $code;

    public function rules()
    {
        return [
            [['name','min_salary','max_salary','levey','remarks','de_uuid','requirement','duty'],'required'],
            ['levey','integer'],
        ];
    }

    static public function levelList() {
        return [
            1 => '总经理级别',
            2 => '事业部总监级别',
            3 => '部门经理级别',
            4 => '普通职员级别'
        ];
    }

    static public function roleList() {
        return [
            self::GeneralRole => '普通成员',
            self::LeadRole => '领导',
            self::DutyRole => '负责人',
        ];
    }
}