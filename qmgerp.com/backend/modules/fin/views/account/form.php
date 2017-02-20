<div class="fin-account panel-body">
<?php
use yii\widgets\ActiveForm;
use backend\models\ViewHelper;
$viewHelper = new ViewHelper($model);
?>
<?php $form = ActiveForm::begin([
    'options'=>[
        'enctype'=>'multipart/form-data',
        'class' => 'form-horizontal ' . $formClass,
        'data-parsley-validate' => "true",
    ],
    'id' => $formClass,
    'action' => $action,
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
])?>
    <input type="hidden" name="FINAccountForm[uuid]" value="<?=
    isset($formData['uuid'])?$formData['uuid']:''
    ?>">
<?= $form->field($model,'object_uuid')->
input('hidden',[
    
    'value'=>isset($formData['object_uuid'])?$formData['object_uuid']:''
])->label(false)?>
    <table class="table fin-account-table">
        <tbody>
        <tr>
            <td>*收款人名字</td>
            <td>
                <?= $form->field($model,'name')->textInput([
                    'data-parsley-required'=>'true',
                    'value'=>isset($formData['name'])?$formData['name']:''
                ])->label(false)?>
            </td>
            <td>*收款类型</td>
            <td>
                <?= $form->field($model, 'type')->
                dropDownList($model->typeList(),[
                    'data-parsley-required'=>'true',
                    'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'type'),
                ])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td>开户行</td>
            <td>
                <?= $form->field($model,'bank_of_deposit')->textInput([
                    'value'=>isset($formData['bank_of_deposit'])?$formData['bank_of_deposit']:''
                ])->label(false)?>
            </td>
            <td>*账号</td>
            <td>
                <?= $form->field($model,'account')->textInput([
                    'data-parsley-required'=>'true',
                    'value'=>isset($formData['account'])?$formData['account']:''
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>
                <input type="submit" value="提交" class="form-control btn btn-primary">
            </td>
        </tr>
        </tbody>
    </table>
<?php ActiveForm::end()?>
</div>