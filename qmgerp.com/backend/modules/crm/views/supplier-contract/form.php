<?php if(!$show):?>
<div class="panel-body">
<?php endif?>
<?php
use yii\widgets\ActiveForm;
use backend\models\ViewHelper;
use backend\modules\fin\models\contract\ContractConfig;
use yii\helpers\Url;

$viewHelper = new ViewHelper($model);
$contractConfig = new ContractConfig();
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
<?php if($show) :?>
    <a href="javascript:;" class="editForm" style="font-size: 15px; float: right"><i class="fa fa-2x fa-pencil"></i>编辑</a>
<?php endif?>
<input type="hidden" name="SupplierContractForm[supplier_uuid]" value="<?= isset($supplierContract['supplier_uuid'])?$supplierContract['supplier_uuid']:''?>">
<input type="hidden" name="SupplierContractForm[code]" value="<?= $supplierContract['code']?>">
<input type="hidden" name="SupplierContractForm[uuid]" value="<?=
isset($supplierContract['uuid'])?$supplierContract['uuid']:''
?>">
<table class="table supplier-contract-table">
    <tbody>
    <tr>
        <td>供应商名称</td>
        <td colspan="3">
            <?= $form->field($model,'supplier_name')->textInput([
                
                'disabled'=>true,
                'value'=>isset($supplierContract['supplier_name'])?$supplierContract['supplier_name']:''
            ])->label(false)?>
        </td>
        <td>供应商管理人</td>
        <td>
            <?= $form->field($model,'supplier_manager_name')->textInput([
                
                'disabled'=>true,
                'value'=>isset($supplierContract['supplier_manager_name'])?$supplierContract['supplier_manager_name']:''
            ])->label(false)?>
        </td>
        <td>合同类型</td>
        <td>
            <?= $form->field($model, 'type')->
            dropDownList(\backend\modules\crm\models\supplier\model\SupplierConfig::contractTypeList(),[
                'class'=>'enableEdit form-control contractType',
                'disabled'=>$show,
                
                'options'=>ViewHelper::defaultValueForDropDownList(true, $supplierContract,'type'),
            ])->
            label(false)?>
        </td>
    </tr>
    <tr>
        <td>合同负责人</td>
        <td>
            <?= $form->field($model,'duty_name')->textInput([
                
                'disabled'=>true,
                'value'=>isset($supplierContract['duty_name'])?$supplierContract['duty_name']:''
            ])->label(false)?>
        </td>
        <td><?= $viewHelper->isRequiredFiled('money')?>金额</td>
        <td>
            <?= $form->field($model,'money')->textInput([
                
                'data-parsley-required'=>'true',
                'data-parsley-type'=>"number",
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                'value'=>isset($supplierContract['money'])?$supplierContract['money']:''
            ])->label(false)?>
        </td>
        <td>合同状态</td>
        <td>
            <?= $form->field($model, 'status')->
            dropDownList($contractConfig->getList('status'),[
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'options'=>ViewHelper::defaultValueForDropDownList(true, $supplierContract,'status'),
            ])->
            label(false)?>
        </td>
        <td>合同模板</td>
        <td>
            <?= $form->field($model, 'template_uuid')->
            dropDownList($templateList,[
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'options'=>ViewHelper::defaultValueForDropDownList(true, $supplierContract,'template_uuid'),
            ])->
            label(false)?>
        </td>
    </tr>
    <tr>
        <td>合同编码</td>
        <td>
            <?= $form->field($model,'code')->textInput([
                'class'=>'form-control contractCode',
                
                'disabled'=>true,
                'value'=>$supplierContract['type'].$supplierContract['code']
            ])->label(false)?>
        </td>
        <td>开始时间</td>
        <td>
            <?= $form->field($model,'start_time')->textInput([
                'disabled'=>$show,
                'class'=>'enableEdit input-section datetimepicker form-control',
                
                'value'=>isset($supplierContract['start_time'])?
                    ($supplierContract['start_time']==0?'':date('Y-m-d',$supplierContract['start_time']))
                    :''
            ])->label(false)?>
        </td>
        <td>结束时间</td>
        <td>
            <?= $form->field($model,'end_time')->textInput([
                'disabled'=>$show,
                'class'=>'enableEdit input-section datetimepicker form-control',
                
                'value'=>isset($supplierContract['end_time'])?
                    ($supplierContract['end_time']==0?'':date('Y-m-d',$supplierContract['end_time']))
                    :''
            ])->label(false)?>
        </td>
        <td>签订时间</td>
        <td>
            <?= $form->field($model,'sign_time')->textInput([
                'disabled'=>$show,
                'class'=>'enableEdit input-section datetimepicker form-control',
                
                'value'=>isset($supplierContract['sign_time'])?
                    ($supplierContract['sign_time']==0?'':date('Y-m-d',$supplierContract['sign_time']))
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
                
                'value'=>isset($supplierContract['part_a_duty'])?$supplierContract['part_a_duty']:''
            ])->label(false)?>
        </td>
        <td>甲方电话</td>
        <td>
            <?= $form->field($model,'part_a_phone')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($supplierContract['part_a_phone'])?$supplierContract['part_a_phone']:''
            ])->label(false)?>
        </td>
        <td>甲方传真</td>
        <td>
            <?= $form->field($model,'part_a_fax')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($supplierContract['part_a_fax'])?$supplierContract['part_a_fax']:''
            ])->label(false)?>
        </td>
        <td>甲方地址</td>
        <td>
            <?= $form->field($model,'part_a_address')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($supplierContract['part_a_address'])?$supplierContract['part_a_address']:''
            ])->label(false)?>
        </td>
    </tr>
    <tr>
        <td>乙方联系人</td>
        <td>
            <?= $form->field($model,'part_b_duty')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($supplierContract['part_b_duty'])?$supplierContract['part_b_duty']:''
            ])->label(false)?>
        </td>
        <td>乙方电话</td>
        <td>
            <?= $form->field($model,'part_b_phone')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($supplierContract['part_b_phone'])?$supplierContract['part_b_phone']:''
            ])->label(false)?>
        </td>
        <td>乙方传真</td>
        <td>
            <?= $form->field($model,'part_b_fax')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($supplierContract['part_b_fax'])?$supplierContract['part_b_fax']:''
            ])->label(false)?>
        </td>
        <td>乙方地址</td>
        <td>
            <?= $form->field($model,'part_b_address')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($supplierContract['part_b_address'])?$supplierContract['part_b_address']:''
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
                'value'=>isset($supplierContract['remarks'])?$supplierContract['remarks']:''])->
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
            <?php if(isset($supplierContract['path']) && !empty($supplierContract['path'])) :?>
                <?php
                // 将path字段解析出来
                $supplierContract['path'] = unserialize($supplierContract['path']);
                ?>
                <?php foreach($supplierContract['path'] as $key=>$path) :?>
                    <div>
                        <a href="<?= Url::to([
                            '/crm/supplier-contract/attachment-download',
                            'path'=>$path,
                            'file_name'=>$key,
                        ])?>" class="enableEdit" <?= $show?'disabled':''?>><?= $key?></a>
                        <span style="float: right">
                            <a href="#" name="<?= Url::to([
                                '/crm/supplier-contract/attachment-delete',
                                'path'=>$path,
                                'uuid'=>$supplierContract['uuid'],
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
            <input type="submit" <?= $show?'disabled':''?> value="提交" class="enableEdit form-control btn btn-primary">
        </td>
        <td colspan="2"></td>
    </tr>
    </tbody>
</table>
<?php ActiveForm::end()?>
<?php if(!$show):?>
</div>
<?php endif?>

<?php
$JS = <<<JS
$(function() {
    $('form.SupplierContractForm').on('change','.contractType',function() {
        var code_prefix = $(this).val();
        var form = $(this).parents('form');
        var code_field = form.find('.contractCode');
        var code = code_field.val();
        code = code.replace(/[A-Z]+/,code_prefix);
        code_field.val(code);
    });
})
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>
