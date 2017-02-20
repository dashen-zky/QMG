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
<input hidden name="ContactForm[object_uuid]"
       value="<?= isset($formData['object_uuid'])?$formData['object_uuid']:''?>">
<input hidden name="ContactForm[uuid]"
       value="<?= isset($formData['uuid'])?$formData['uuid']:''?>">
<?php if($show && (!isset($showOnly) || !$showOnly)) :?>
        <a href="javascript:;" class="editForm" style="font-size: 15px; float: right"><i class="fa fa-2x fa-pencil"></i>编辑</a>
<?php endif?>
<table class="table">
    <tbody>
    <tr>
        <td>*姓名</td>
        <td>
            <?= $form->field($model,'name')->textInput([
                
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                'value'=>isset($formData['name'])?$formData['name']:'',
                'data-parsley-required'=>'true',
            ])->label(false)?>
        </td>
        <td>性别</td>
        <td>
            <?= $form->field($model, 'gender')->
            dropDownList(ContactForm::genderList(),[
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'gender'),
            ])->
            label(false)?>
        </td>
        <td>职位</td>
        <td>
            <?= $form->field($model,'position')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($formData['position'])?$formData['position']:''
            ])->label(false)?>
        </td>
    </tr>
    <tr>
        <td>*电话</td>
        <td>
            <?= $form->field($model,'phone')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($formData['phone'])?$formData['phone']:'',
                'data-parsley-required'=>'true',
                'data-parsley-type'=>"number",
            ])->label(false)?>
        </td>
        <td>微信</td>
        <td>
            <?= $form->field($model,'weichat')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($formData['weichat'])?$formData['weichat']:''
            ])->label(false)?>
        </td>
        <td>qq</td>
        <td>
            <?= $form->field($model,'qq')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($formData['qq'])?$formData['qq']:''
            ])->label(false)?>
        </td>
    </tr>
    <tr>
        <td>办公电话</td>
        <td>
            <?= $form->field($model,'office_phone')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($formData['office_phone'])?$formData['office_phone']:''
            ])->label(false)?>
        </td>
        <td>邮箱</td>
        <td>
            <?= $form->field($model,'email')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'data-parsley-type'=>"email",
                'value'=>isset($formData['email'])?$formData['email']:''
            ])->label(false)?>
        </td>
        <td>类别</td>
        <td>
            <?= $form->field($model, 'type')->
            dropDownList(ContactForm::customerTypeList(),[
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'type'),
            ])->
            label(false)?>
        </td>
    </tr>
    <tr>
        <td>地址</td>
        <td>
            <?= $form->field($model,'address')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($formData['address'])?$formData['address']:''
            ])->label(false)?>
        </td>
        <td>状态</td>
        <td>
            <?= \yii\bootstrap\Html::dropDownList("ContactForm[enable]",
                isset($contact['enable'])?$contact['enable']:null,
                ContactForm::enableList(),[
                    'disabled'=>$show,
                    'class'=>'form-control'
                ])?>
        </td>
        <td>备注</td>
        <td>
            <?= $form->field($model,'remarks')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($formData['remarks'])?$formData['remarks']:''
            ])->label(false)?>
        </td>
    </tr>
    <tr><td colspan="6"></td></tr>
    </tbody>
</table>
<?php if(!isset($showOnly) || !$showOnly) :?>
<span class="col-md-12">
    <span class="col-md-4"></span>
    <span class="col-md-4">
        <input type="submit" <?= $show?'disabled':''?> value="提交" class="form-control enableEdit btn-primary">
    </span>
    <span class="col-md-4"></span>
</span>
<?php endif?>
<?php ActiveForm::end()?>
