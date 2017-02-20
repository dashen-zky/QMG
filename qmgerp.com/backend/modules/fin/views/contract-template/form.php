<?php
use yii\bootstrap\ActiveForm;
?>
<?php
$form = ActiveForm::begin([
    'options'=>['enctype'=>'multipart/form-data','class' => 'form-horizontal ' . $formClass],
    'method' => 'post',
    'action' => $action,
    'enableClientValidation'=>true,
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
]);
echo $form->errorSummary($model);
?>
<input hidden name="ContractTemplateForm[uuid]"
       value="<?= isset($contractTemplate['uuid'])?$contractTemplate['uuid'] : ''?>">
<table class="table contract-template">
    <tr>
        <td>模板名字</td>
        <td>
            <?= $form->field($model,'name')->textInput([
                
                'value'=>isset($contractTemplate['name'])?$contractTemplate['name']:''
            ])->label(false)?>
        </td>
        <td>附件</td>
        <td>
            <?= $form->field($model,'attachment')->fileInput()->label(false)?>
        </td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>
            <input type="submit" value="提交" class="form-control btn btn-default">
        </td>
    </tr>
</table>
<?php ActiveForm::end()?>
