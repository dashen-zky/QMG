<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use backend\modules\hr\models\Department;
?>
<?= $this->render('@webroot/../views/site/panel-header',[
    'title'=>'角色分配',
])?>
<?= Html::beginForm(['/rbac/assignment/assign'], 'post', [
    'class' => 'AssignForm',
]); ?>
<input type="hidden" name="backUrl" value="<?= $backUrl?>">
    <table class="table role-assignment">
        <tbody>
        <tr>
            <td>*角色id</td>
            <td>
                <div class="col-md-12">
                    <?= Html::textInput('AssignForm[name]',
                        isset($formData['name'])?$formData['name']:'',
                        [
                            'readOnly'=>true,
                            'class' => 'form-control col-md-12',
                        ]) ?>
                </div>
            </td>
            <td>人员</td>
            <td colspan="2">
                <input type="hidden" class="role-employee-uuid" name="AssignForm[role_employee_uuid]"
                       value="<?= isset($formData['role_employee_uuid'])?
                           $formData['role_employee_uuid']:''?>">
                <?= Html::textInput('AssignForm[role_employee_name]',
                    isset($formData['role_employee_name'])?$formData['role_employee_name']:'',
                    [
                        'disabled'=>true,
                        'class' => 'form-control col-md-12 role-employee-name',
                    ]) ?>
            </td>
            <td>
                <a href="javascript:;"
                   class="showSelectEmployeePanel"
                   name="<?= Url::to([
                       '/rbac/assignment/employee-list',
                       'selectClass'=>'selectRoleEmployee',
                   ])?>">
                    <i class="fa fa-2x fa-edit"></i>
                </a>
            </td>
        </tr>
        <tr>
            <td>角色描述</td>
            <td colspan="5">
                <div class="col-md-12">
                    <?= Html::textarea('AssignForm[description]',
                        isset($formData['description'])?$formData['description']:'',
                        [
                            'class' => 'form-control col-md-12',
                            'disabled'=>true,
                            'rows'=>3,
                        ]) ?>
                </div>
            </td>
        </tr>
        <tr>
        </tr>
        </tbody>
    </table>
    <span class="col-md-12">
    <span class="col-md-4"></span>
    <span class="col-md-4">
        <input type="reset" name="reset" style="display: none;" />
        <input type="submit"  value="提交" class="form-control btn-primary">
    </span>
    <span class="col-md-4"></span>
    </span>
<?= Html::endForm()?>
<!--// 选择人员表-->

<?= $this->render('@hr/views/employee/employee-select-list-panel-advance.php',[
    'filters'=>[
        'employee_uuid'=>isset($formData['role_employee_uuid'])?$formData['role_employee_uuid']:null,
        'employee_name'=>isset($formData['role_employee_name'])?$formData['role_employee_name']:null,
    ],
    'fieldInformation'=>Json::encode([
        0=>['AssignForm', 'role-employee-uuid'],
        1=>['AssignForm', 'role-employee-name'],
    ]),
    'departmentList'=>(new Department())->departmentListForDropDownList(),
])?>
<?= $this->render('@webroot/../views/site/panel-footer')?>

<?php
$Js = <<<Js
$(function() {
       // 显示选择项目成员
      $('.role-assignment').on('click','.showSelectEmployeePanel',function() {
            var url = $(this).attr('name');
             url += '&uuids=' + $(this).parents('form').find('input[name="AssignForm[role_employee_uuid]"]').val();
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
})
Js;

$this->registerJs($Js, \yii\web\View::POS_END);
?>
