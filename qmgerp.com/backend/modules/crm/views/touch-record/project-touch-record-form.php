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
    'method' => 'post',
    'action' => ['/crm/touch-record/add'],
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
])?>
    <table class="table project-touch-record-table">
        <tbody>
        <tr>
            <td><?= $viewHelper->isRequiredFiled('time')?>跟进时间</td>
            <td>
                <input type="hidden" name="TouchRecordForm[category]" value="project">
                <input type="hidden" name="TouchRecordForm[project_uuid]" value="<?= $project_uuid?>">
                <input type="hidden" name="TouchRecordForm[uuid]" value="<?=
                isset($touchRecord['uuid'])?$touchRecord['uuid']:''
                ?>">
                <?= $form->field($model,'time')->textInput([
                    'data-parsley-required'=>'true',
                    'class'=>'input-section datetimepicker form-control',
                    
                ])->label(false)?>
            </td>
            <td>跟进方式</td>
            <td>
                <?= $form->field($model, 'type')->
                dropDownList($model->getList('touchType'),[
                    
                ])->
                label(false)?>
            </td>
            <td>*联系人</td>
            <td>
                <?= $form->field($model, 'contact_uuid')->
                dropDownList($contactList,[
                    'data-parsley-required'=>'true',
                    
                ])->
                label(false)?>
            </td>
            <td>下次跟进时间</td>
            <td>
                <?= $form->field($model,'next_touch_time')->textInput([
                    'class'=>'input-section datetimepicker form-control',
                    
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td>结果</td>
            <td>
                <?= $form->field($model, 'result')->
                dropDownList($model->getList('touchResult'),[
                    
                ])->
                label(false)?>
            </td>
            <td>预计签约时间</td>
            <td>
                <?= $form->field($model,'predict_contract_time')->textInput([
                    'class'=>'input-section datetimepicker form-control',
                    
                ])->label(false)?>
            </td>
            <td>预计签约金额</td>
            <td>
                <?= $form->field($model,'predict_contract_value')->textInput([
                    
                ])->label(false)?>
            </td>
            <td></td><td></td>
        </tr>
        <tr>
            <td>情况描述</td>
            <td colspan="7">
                <?= $form->field($model,'description')->
                textarea([
                    
                    'rows'=>3,
                ])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="2">
                <input type="submit" value="提交" class="form-control btn btn-primary">
            </td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        </tbody>
    </table>
<?php ActiveForm::end()?>