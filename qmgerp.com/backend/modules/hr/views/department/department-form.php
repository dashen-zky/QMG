<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
use backend\models\ViewHelper;
use yii\helpers\Url;
use yii\helpers\Json;
use backend\modules\hr\models\Department;
use backend\modules\hr\models\DepartmentForm;
$model = new DepartmentForm();
?>
<?php $form = ActiveForm::begin([
    'options'=>[
        'enctype'=>'multipart/form-data',
        'class' => 'form-horizontal DepartmentForm',
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
    'value'=>isset($formData['uuid'])?$formData['uuid']:''
])->label(false)?>
    <table class="table">
        <tbody>
        <tr>
            <td>部门等级</td>
            <td>
                <?= $form->field($model, 'level')->
                dropDownList($model->levelList(),
                    [
                        'class'=>'departmentLevel form-control',
                        'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'level')
                    ])->
                label(false)?>
            </td>
            <td colspan="2">
                <div class="parent-department-filed"
                     style="display: <?= (isset($formData['level'])&&$formData['level'] == 1)?'none':'block' ?>">
                <div style="float: left">
                    <?= $form->field($model,'parent_uuid')->input('hidden',[
                        'value'=>isset($formData['parent_uuid'])?$formData['parent_uuid']:'',
                    ])->label(false)?>
                </div>
                <div style="float: left" class="col-md-7">
                <?= $form->field($model,'parent_name')->textInput([
                    
                    'disabled'=>"disabled",
                    'value'=>isset($formData['parent_name'])?$formData['parent_name']:'',
                ])->label(false)?>
                </div>
                <div style="float: left; margin-left: 50px">
                    <button type="button" name="<?= Url::to([
                        '/hr/department/parent-list',
                    ])?>" class="selectParentDepartment btn btn-default">选择上级部门</button>
                </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>*部门编号</td>
            <td>
                <?= $form->field($model,'code')->
                textInput([
                    
                    'value'=>isset($formData['code'])?$formData['code']:'',
                    'data-parsley-required'=>'true',
                ])->
                label(false)?>
            </td>
            <td>*部门名称</td>
            <td>
                <?= $form->field($model,'name')->
                textInput([
                    
                    'value'=>isset($formData['name'])?$formData['name']:'',
                    'data-parsley-required'=>'true',
                ])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td>负责人</td>
            <td>
                <?= $form->field($model,'duty_name')->textInput([
                    
                    'disabled'=>"disabled",
                    'class'=>'duty-name form-control',
                    'value'=>isset($formData['duty_name'])?$formData['duty_name']:'',
                ])->label(false)?>
                <?= $form->field($model,'duty_uuid')->input('hidden',[
                    'class'=>'duty-uuid',
                    'value'=>isset($formData['duty_uuid'])?$formData['duty_uuid']:'',
                ])->label(false)?>
            </td>
            <td>
                <a href="#" name="<?= Url::to([
                    '/hr/department/employee-list',
                ])?>" class="show-employee-panel">
                    <i class="fa fa-2x fa-edit"></i>
                </a>
            </td>
            <td></td>
        </tr>
        <?php if(isset($edit)):?>
        <tr>
            <td>上级部门描述</td>
            <td colspan="5">
                <?= $form->field($model,'parent_description')->
                textarea([
                    
                    'rows'=>3,
                    'disabled'=>'true',
                    'value'=>isset($formData['parent_description'])?$formData['parent_description']:'',
                ])->
                label(false)?>
            </td>
        </tr>
    <?php endif?>
        <tr>
            <td>描述</td>
            <td colspan="5">
                <?= $form->field($model,'description')->
                textarea([
                    
                    'rows'=>3,
                    'value'=>isset($formData['description'])?$formData['description']:''
                ])->
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
                <?= Html::submitButton('submit', ['class' => 'btn btn-primary col-md-12', 'name' => 'login-button']) ?>
            </td>
        </tr>
        </tbody>
    </table>
<?php ActiveForm::end();?>
<?= $this->render('@hr/views/employee/employee-select-list-panel-advance.php',[
    'filters'=>[
        'employee_uuid'=>isset($formData['duty_uuid'])?$formData['duty_uuid']:null,
        'employee_name'=>isset($formData['duty_name'])?$formData['duty_name']:null,
    ],
    'fieldInformation'=>Json::encode([
        0=>['DepartmentForm', 'duty-uuid'],
        1=>['DepartmentForm', 'duty-name'],
    ]),
    'departmentList'=>(new Department())->departmentListForDropDownList(),
])?>
<?php
$JS = <<<Js
$(document).ready(function() {
    // 当部门等级变成1的时候，选择上级部门字段不显示
    $('.departmentLevel').change(function() {
        var level = $(this).val();
        var parent_department_field = $('.parent-department-filed');
        if(level == 1) {
            parent_department_field.css('display','none');
            return ;
        }
        if(parent_department_field.css('display') == 'none') {
            parent_department_field.css('display','block');
        }
    });
    $(".selectParentDepartment").click(function() {
        var departmentLevel = $(".departmentLevel").val();
        // 如果改变部门等级为1，那么该部门没有上级部门，应该将上级部门信息清空
        if(departmentLevel == '1') {
            $(".department-form form input[name='DepartmentForm[parent_uuid]']").val('');
            $(".department-form form input[name='DepartmentForm[parent_name]']").val('');
        }
        var url = $(this).attr('name')+"&level="+departmentLevel;
        $.get(
            url,
            function(data,status) {
                if(status === "success") {
                    $("#selectDepartmentContainerModal .panel-body div").html(data);
                    $("#selectDepartmentContainerModal").modal('show');
                }
            }
        );
    });
    // 选择负责人
    $('.DepartmentForm').on('click','.show-employee-panel', function() {
        var url = $(this).attr('name');
        var form = $(this).parents('form');
        var duty_uuid = form.find('.duty-uuid').val();
        url += '&uuids='+duty_uuid;
        $.get(
        url,
        function(data,status) {
            if('success' == status) {
                var employee_modal = $(".select-employee-container-modal");
                var employee_list_container = employee_modal.find(".panel-body div.employee-list");
                employee_list_container.html(data);
                // 选定好了的，但是没有没有提交的员工，当再一次加载这个文档的时候，我们应该让它被checked
                var selected = $('.select-employee-container-modal .selected-employee-tags li');
                selected.each(function() {
                    var uuid = $(this).find('.tag .tag-close').attr('id');
                    employee_list_container.find('input#'+uuid).attr('checked', true);
                });
                employee_modal.modal('show');
            }
        });
    });
});
Js;
$this->registerJs($JS, View::POS_END);
?>
