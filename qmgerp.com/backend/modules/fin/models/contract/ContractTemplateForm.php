<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/29 0029
 * Time: 下午 4:27
 */

namespace backend\modules\fin\models\contract;


use backend\modules\crm\models\interfaces\RecordOperator;
use backend\modules\fin\models\FINBaseForm;

class ContractTemplateForm extends FINBaseForm
{
    public $path; // 模板存放路径
    public $uuid;
    public $name;
    public $attachment;
    public $file_name;

    public function rules()
    {
        return [
            // 名字和附件都需要
            [['name'],'required'],
            // 附件必须是文件
            ['attachment','file'],

            ['file_name','match','pattern'=>'/^[\x{4e00}-\x{9fa5}\w\-\.]{1,30}$/u','message'=>'文件名不能包含特殊字符或长度不宜超过30个字符']
        ];

    }
}