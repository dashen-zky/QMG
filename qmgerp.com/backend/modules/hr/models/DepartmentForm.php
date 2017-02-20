<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3 0003
 * Time: 下午 6:19
 */

namespace backend\modules\hr\models;

use backend\modules\hr\models\HrBaseForm;
class DepartmentForm extends HrBaseForm
{
    public $uuid;
    public $name;
    public $description;
    public $level; // 0 表示公司，1表示事业部，2表示项目组
    public $remarks;
    public $attachment;
    public $parentDepartment;
    public $parent_uuid;
    public $parent;
    public $parent_name;
    public $parentDescription;
    public $parent_description;
    public $code;

    public function rules()
    {
        return [
            [['name','levey'],'required'],
            ['levey','integer'],
        ];
    }

    static public function levelList() {
        return [
            1 => '公司',
            2 => '事业部',
            3 => '部门',
        ];
    }
};
