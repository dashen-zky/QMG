<?php
use backend\modules\crm\models\customer\model\ContactForm;
use yii\bootstrap\ActiveForm;
use backend\models\ViewHelper;
$viewHelper = new ViewHelper($model);
?>
<?php $form = ActiveForm::begin([
    'options'=>[
        'enctype'=>'multipart/form-data',
        'class' => 'form-horizontal',
        'data-parsley-validate' => "true",
    ],
    'method' => 'post',
    'action' => $action,
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
])?>
<input hidden name="Stamp[object_uuid]"
       value="<?= isset($formData['object_uuid'])?$formData['object_uuid']:''?>">
<input hidden name="Stamp[uuid]"
       value="<?= isset($formData['uuid'])?$formData['uuid']:''?>">
<?php if($show) :?>
    <a href="javascript:;" class="editForm" style="font-size: 15px; float: right"><i class="fa fa-2x fa-pencil"></i>编辑</a>
<?php endif?>
<table class="table">
    <tbody>
    <tr>
        <td>*公司名字</td>
        <td>
            <?= $form->field($model,'company_name')->textInput([
                
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                'value'=>isset($formData['company_name'])?$formData['company_name']:'',
                'data-parsley-required'=>'true',
            ])->label(false)?>
        </td>
        <td><?= $viewHelper->isRequiredFiled('company_address')?>公司地址</td>
        <td>
            <?= $form->field($model,'company_address')->textInput([
                
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                'value'=>isset($formData['company_address'])?$formData['company_address']:'',
                'data-parsley-required'=>'true',
            ])->label(false)?>
        </td>
        <td><?= $viewHelper->isRequiredFiled('stamp_number')?>公司税号</td>
        <td>
            <?= $form->field($model,'stamp_number')->textInput([
                
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                'value'=>isset($formData['stamp_number'])?$formData['stamp_number']:'',
                'data-parsley-required'=>'true',
            ])->label(false)?>
        </td>
    </tr>
    <tr>
        <td><?= $viewHelper->isRequiredFiled('company_phone')?>公司电话</td>
        <td>
            <?= $form->field($model,'company_phone')->textInput([
                
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                'value'=>isset($formData['company_phone'])?$formData['company_phone']:'',
                'data-parsley-required'=>'true',
            ])->label(false)?>
        </td>
        <td><?= $viewHelper->isRequiredFiled('bank_of_deposit')?>开户行</td>
        <td>
            <?= $form->field($model,'bank_of_deposit')->textInput([
                
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                'value'=>isset($formData['bank_of_deposit'])?$formData['bank_of_deposit']:'',
                'data-parsley-required'=>'true',
            ])->label(false)?>
        </td>
        <td><?= $viewHelper->isRequiredFiled('account')?>银行账号</td>
        <td>
            <?= $form->field($model,'account')->textInput([
                
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                'value'=>isset($formData['account'])?$formData['account']:'',
                'data-parsley-required'=>'true',
            ])->label(false)?>
        </td>
    </tr>
    <tr><td colspan="6"></td></tr>
    </tbody>
</table>
<span class="col-md-12">
    <span class="col-md-4"></span>
    <span class="col-md-4">
        <input type="submit" <?= $show?'disabled':''?> value="提交" class="form-control enableEdit btn-primary">
    </span>
    <span class="col-md-4"></span>
</span>
<?php ActiveForm::end()?>
