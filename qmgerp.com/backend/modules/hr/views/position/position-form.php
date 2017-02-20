<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use backend\modules\hr\models\DepartmentForm;
use yii\web\View;
use backend\modules\hr\models\PositionForm;
use backend\models\ViewHelper;
use yii\helpers\Url;
?>

<?php $form = ActiveForm::begin([
    'options'=>[
        'enctype'=>'multipart/form-data',
        'class' => 'form-horizontal PositionForm',
        'data-parsley-validate' => "true",
    ],
    'method' => 'post',
    'action' => $action,
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
])?>
<?= $form->field($model,'uuid')->input('hidden',[
    'value'=>isset($formData['uuid'])?$formData['uuid']:'',
])->label(false)?>
    <table class="table">
        <tbody>
        <tr>
            <td class="col-md-1">*公司</td>
            <td class="col-md-3">
                <?= Html::dropDownList(
                    'PositionForm[department][1]',
                    null,
                    ViewHelper::appendElementOnDropDownList(isset($formData['department'][1])?
                        $formData['department'][1]:[]),
                    [
                        'data-parsley-required'=>'true',
                        'class'=>'form-control department-1 col-md-12',
                        'id'=>Url::to([
                            '/hr/position/department-list'
                        ]),
                        'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'department_level_1'),
                    ]
                ) ?>
            </td>
            <td class="col-md-1">事业部</td>
            <td class="col-md-2">
                <?= Html::dropDownList(
                    'PositionForm[department][2]',
                    null,
                    ViewHelper::appendElementOnDropDownList(isset($formData['department'][2])?
                        $formData['department'][2]:[]),
                    [
                        'class'=>'form-control department-2 col-md-12',
                        'id'=>Url::to([
                            '/hr/position/department-list'
                        ]),
                        'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'department_level_2'),
                    ]
                ) ?>
            <td class="col-md-1">部门</td>
            <td class="col-md-2">
                <?= Html::dropDownList(
                    'PositionForm[department][3]',
                    null,
                    ViewHelper::appendElementOnDropDownList(isset($formData['department'][3])?
                        $formData['department'][3]:[]),
                    [
                        'id'=>Url::to([
                            '/hr/position/department-list'
                        ]),
                        'class'=>'form-control department-3 col-md-12',
                        'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'department_level_3'),
                    ]
                ) ?>
            </td>
        </tr>
        <tr>
            <td>*职位编号</td>
            <td><?= $form->field($model,'code')->
                textInput([
                    
                    'value'=>isset($formData['code'])?$formData['code']:'',
                    'class'=>'form-control',
                    'data-parsley-required'=>'true',
                ])->
                label(false)?></td>
            <td>*职位名称</td>
            <td>
                <?= $form->field($model,'name')->
                textInput([
                    
                    'value'=>isset($formData['name'])?$formData['name']:'',
                    'class'=>'form-control',
                    'data-parsley-required'=>'true',
                ])->
                label(false)?>
            </td>
            <td>岗位编制</td>
            <td>
                <?= Html::textInput('PositionForm[members_limit]',
                    isset($formData['members_limit'])?$formData['members_limit']:null,
                    [
                        'class'=>'form-control',
                        'data-parsley-type'=>"number",
                    ])?>
            </td>
        </tr>
        <tr>
            <td>最小薪资</td>
            <td>
                <?= $form->field($model,'min_salary')->
                        textInput([
                    
                    'value'=>isset($formData['min_salary'])?$formData['min_salary']:'',
                    'data-parsley-type'=>"number",
                ])->
                label(false)?>
            </td>
            <td>最大薪资</td>
            <td>
                <?= $form->field($model,'max_salary')->
                textInput([
                    
                    'value'=>isset($formData['max_salary'])?$formData['max_salary']:'',
                    'data-parsley-type'=>"number"
                ])->
                label(false)?>
            </td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>岗位职责</td>
            <td colspan="5">
                <?= $form->field($model,'duty')->
                    textarea(['rows'=>3,'value'=>isset($formData['duty'])?$formData['duty']:''])->
                    label(false)?>
            </td>
        </tr>
        <tr>
            <td>岗位要求</td>
            <td colspan="5">
                <?= $form->field($model,'requirement')->
                    textarea(['rows'=>3,'value'=>isset($formData['requirement'])?$formData['requirement']:''])->
                    label(false)?>
            </td>
        </tr>
        <tr>
            <td>备注</td>
            <td colspan="5">
                <?= $form->field($model,'remarks')->
                textarea(['rows'=>3,'value'=>isset($formData['remarks'])?$formData['remarks']:''])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td>附件</td>
            <td>
                <?= $form->field($model,'attachment')->fileInput()->label(false)?>
            </td>

            <td colspan="2">
                <?= Html::submitButton('submit', [
                    'class' => 'btn btn-primary col-md-12',
                    'name' => 'login-button'
                ]) ?>
            </td>
        </tr>
        </tbody>
    </table>
<?php ActiveForm::end();?>
<?php
$JS = <<<JS
$(function() {
// 部门的选择的联动效果
$('.PositionForm').on('change','.department-1',function() {
    var url = $(this).attr('id');
    url += "&uuid="+$(this).val();
    $.get(
    url,
    function(data, status) {
        if(status === 'success') {
            var form = $('.PositionForm');
            form.find('.department-2').html(data);
        }
    }
    );
}).on('change','.department-2',function() {
    var url = $(this).attr('id');
    url += "&uuid="+$(this).val();
    $.get(
    url,
    function(data, status) {
        if(status === 'success') {
            var form = $('.PositionForm');
            form.find('.department-3').html(data);
        }
    }
    );
});
});
JS;
$this->registerJs($JS, View::POS_END);
?>
