<?php
use yii\widgets\ActiveForm;
use backend\models\ViewHelper;
use backend\modules\fin\models\contract\ContractConfig;
use yii\helpers\Url;

$contractConfig = new ContractConfig();
if (!isset($enableEdit)) {
    $enableEdit = true;
}
?>
<?php $form = ActiveForm::begin([
    'options'=>[
        'enctype'=>'multipart/form-data',
        'class' => 'form-horizontal ' . $formClass,
        'data-parsley-validate' => "true",
    ],
    'method' => 'post',
    'enableClientValidation'=>true,
    'action' => $action,
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
])?>
<input hidden value="<?= isset($back_url)?$back_url:null?>" name="CustomerContractForm[back_url]">
<?php if($enableEdit) :?>
    <a href="javascript:;" class="editForm" style="font-size: 15px; float: right"><i class="fa fa-2x fa-pencil"></i>编辑</a>
<?php endif?>
<input type="hidden" name="CustomerContractForm[customer_uuid]" value="<?= isset($customerContract['customer_uuid'])?$customerContract['customer_uuid']:''?>">
<input type="hidden" name="CustomerContractForm[code]" value="<?= $customerContract['code']?>">
<input type="hidden" name="CustomerContractForm[uuid]" value="<?=
isset($customerContract['uuid'])?$customerContract['uuid']:''
?>">
<table class="table customer-contract-table">
    <tbody>
    <tr>
        <td>客户名称</td>
        <td colspan="3">
            <?= $form->field($model,'customer_name')->textInput([
                
                'disabled'=>true,
                'value'=>isset($customerContract['customer_name'])?$customerContract['customer_name']:''
            ])->label(false)?>
        </td>
        <td>销售</td>
        <td colspan="3">
            <?= $form->field($model,'sales_name')->textInput([
                'disabled'=>true,
                'value'=>isset($customerContract['sales_name'])?$customerContract['sales_name']:''
            ])->label(false)?>
        </td>
    </tr>
    <tr>
        <td>合同负责人</td>
        <td>
            <?= $form->field($model,'duty_name')->textInput([
                
                'disabled'=>true,
                'value'=>isset($customerContract['duty_name'])?$customerContract['duty_name']:''
            ])->label(false)?>
        </td>
        <td>*金额</td>
        <td>
            <?= $form->field($model,'money')->textInput([
                
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                'data-parsley-required'=>'true',
                'data-parsley-type'=>"number",
                'value'=>isset($customerContract['money'])?$customerContract['money']:''
            ])->label(false)?>
        </td>
        <td>合同状态</td>
        <td>
            <?= $form->field($model, 'status')->
            dropDownList($contractConfig->getList('status'),[
                
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                'options'=>ViewHelper::defaultValueForDropDownList(true, $customerContract,'status'),
            ])->
            label(false)?>
        </td>
        <td>合同模板</td>
        <td>
            <?= $form->field($model, 'template_uuid')->
            dropDownList($templateList,[
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'options'=>ViewHelper::defaultValueForDropDownList(true, $customerContract,'template_uuid'),
            ])->
            label(false)?>
        </td>
    </tr>
    <tr>

        <td>合同编码</td>
        <td>
            <?= $form->field($model,'code')->textInput([
                
                'disabled'=>true,
                'value'=>
                isset($customerContract['code']) && isset($customerContract['type'])
                ?$customerContract['type'] . $customerContract['code']:''
            ])->label(false)?>
        </td>
        <td>开始时间</td>
        <td>
            <?= $form->field($model,'start_time')->textInput([
                'disabled'=>$show,
                'class'=>'enableEdit input-section datetimepicker form-control',
                
                'value'=>isset($customerContract['start_time'])?
                    ($customerContract['start_time']==0?'':date('Y-m-d',$customerContract['start_time']))
                    :''
            ])->label(false)?>
        </td>
        <td>结束时间</td>
        <td>
            <?= $form->field($model,'end_time')->textInput([
                'disabled'=>$show,
                'class'=>'enableEdit input-section datetimepicker form-control',
                
                'value'=>isset($customerContract['end_time'])?
                    ($customerContract['end_time']==0?'':date('Y-m-d',$customerContract['end_time']))
                    :''
            ])->label(false)?>
        </td>
        <td>签订时间</td>
        <td>
            <?= $form->field($model,'sign_time')->textInput([
                'disabled'=>$show,
                'class'=>'enableEdit input-section datetimepicker form-control',
                
                'value'=>isset($customerContract['sign_time'])?
                    ($customerContract['sign_time']==0?'':date('Y-m-d',$customerContract['sign_time']))
                    :''
            ])->label(false)?>
        </td>
    </tr>
    <tr>
        <td>甲方联系人</td>
        <td>
            <?= $form->field($model,'part_a_duty')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($customerContract['part_a_duty'])?$customerContract['part_a_duty']:''
            ])->label(false)?>
        </td>
        <td>甲方电话</td>
        <td>
            <?= $form->field($model,'part_a_phone')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($customerContract['part_a_phone'])?$customerContract['part_a_phone']:''
            ])->label(false)?>
        </td>
        <td>甲方传真</td>
        <td>
            <?= $form->field($model,'part_a_fax')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($customerContract['part_a_fax'])?$customerContract['part_a_fax']:''
            ])->label(false)?>
        </td>
        <td>甲方地址</td>
        <td>
            <?= $form->field($model,'part_a_address')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($customerContract['part_a_address'])?$customerContract['part_a_address']:''
            ])->label(false)?>
        </td>
    </tr>
    <tr>
        <td>乙方联系人</td>
        <td>
            <?= $form->field($model,'part_b_duty')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($customerContract['part_b_duty'])?$customerContract['part_b_duty']:''
            ])->label(false)?>
        </td>
        <td>乙方电话</td>
        <td>
            <?= $form->field($model,'part_b_phone')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($customerContract['part_b_phone'])?$customerContract['part_b_phone']:''
            ])->label(false)?>
        </td>
        <td>乙方传真</td>
        <td>
            <?= $form->field($model,'part_b_fax')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($customerContract['part_b_fax'])?$customerContract['part_b_fax']:''
            ])->label(false)?>
        </td>
        <td>乙方地址</td>
        <td>
            <?= $form->field($model,'part_b_address')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($customerContract['part_b_address'])?$customerContract['part_b_address']:''
            ])->label(false)?>
        </td>
    </tr>
    <tr>
        <td>备注</td>
        <td colspan="7">
            <?= $form->field($model,'remarks')->
            textarea([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                'rows'=>3,
                'value'=>isset($customerContract['remarks'])?$customerContract['remarks']:''])->
            label(false)?>
        </td>
    </tr>
    <tr>
        <td>合同附件</td>
        <td><?= $form->field($model,'attachment[]')->fileInput([
                'multiple' => true,
                'class'=>'enableEdit',
                'disabled'=>$show,
            ])?></td>
        <td colspan="4">
            <?php if(isset($customerContract['path']) && !empty($customerContract['path'])) :?>
                <?php
                // 将path字段解析出来
                $customerContract['path'] = unserialize($customerContract['path']);
                ?>
                <?php foreach($customerContract['path'] as $key=>$path) :?>
                    <div>
                        <a href="<?= Url::to([
                            '/crm/customer-contract/attachment-download',
                            'path'=>$path,
                            'file_name'=>$key,
                        ])?>" class="enableEdit" <?= $show?'disabled':''?>><?= $key?></a>
                        <span style="float: right; display: <?= $enableEdit?'block':'none'?>">
                            <a href="#" name="<?= Url::to([
                                '/crm/customer-contract/attachment-delete',
                                'path'=>$path,
                                'uuid'=>$customerContract['uuid'],
                            ])?>" class="attachmentDelete enableEdit" <?= $show?'disabled':''?>>删除</a>
                        </span>
                    </div>
                <?php endforeach?>
            <?php endif?>
        </td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td colspan="4">
            <input type="submit" <?= $show?'disabled':''?> value="提交" class="enableEdit form-control btn-primary">
        </td>
        <td colspan="2"></td>
    </tr>
    </tbody>
</table>
<?php ActiveForm::end()?>
