<?php
use yii\web\View;
use yii\widgets\Pjax;
?>
<div class="modal fade employeeSelectListPanel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="panel panel-body" data-sortable-id="table-basic-4">
        <?php Pjax::begin()?>
        <?php
        $JS = <<<JS
    $(document).ready(function() {
        // 选择项目经理
        $('.employeeSelectListPanel').on('click','.selectProjectManager',function() {
            var seletedRadio = $(".employeeSelectListPanel input[name='uuid']:checked");
            var employeeName = seletedRadio.parent().siblings(".name")[0];
            var selectedRadioValue = seletedRadio.val();
            var project_manager_uuid_field = $("form.ProjectForm input[name='ProjectForm[project_manager_uuid]']");
            var project_manager_name_field = $("form.ProjectForm input[name='ProjectForm[project_manager_name]']");
            project_manager_uuid_field.val(selectedRadioValue);
            project_manager_name_field.val(employeeName.innerHTML);
            $('.employeeSelectListPanel').modal('hide');
        });
        
        // 选择项目成员
        $('.employeeSelectListPanel').on('click','.selectProjectMember',function() {
                var employeeUuid = '';
                var employeeName = '';
                var i = 0;
                var panel = $(this).parents('.employeeSelectListPanel');
                panel.find("table tbody input[name='uuid']:checked").each(function() {
                    if (i === 0) {
                        employeeUuid = $(this).val();
                        employeeName = $(this).parent().next().next()[0].innerHTML;
                        i = 1;
                    } else {
                        employeeUuid += "," + $(this).val();
                        employeeName += "," + $(this).parent().next().next()[0].innerHTML;
                    }
                });
            var project_member_uuid_field = $("form.ProjectForm input[name='ProjectForm[project_member_uuid]']");
            var project_member_name_field = $("form.ProjectForm input[name='ProjectForm[project_member_name]']");
            project_member_uuid_field.val(employeeUuid);
            project_member_name_field.val(employeeName);
            $('.employeeSelectListPanel').modal('hide');
        });

        // 为角色选择员工
        $('.employeeSelectListPanel').on('click','.selectRoleEmployee',function() {
                var employeeUuid = '';
                var employeeName = '';
                var i = 0;
                var panel = $(this).parents('.employeeSelectListPanel');
                panel.find("table tbody input[name='uuid']:checked").each(function() {
                    if (i === 0) {
                        employeeUuid = $(this).val();
                        employeeName = $(this).parent().next().next()[0].innerHTML;
                        i = 1;
                    } else {
                        employeeUuid += "," + $(this).val();
                        employeeName += "," + $(this).parent().next().next()[0].innerHTML;
                    }
                });
            var role_employee_uuid_field = $("form.AssignForm input[name='AssignForm[role_employee_uuid]']");
            var role_employee_name_filed = $("form.AssignForm input[name='AssignForm[role_employee_name]']");
            role_employee_uuid_field.val(employeeUuid);
            role_employee_name_filed.val(employeeName);
            $('.employeeSelectListPanel').modal('hide');
        });
    });
JS;
        $this->registerJs($JS, View::POS_END);
        ?>
        <div></div>
        <?php Pjax::end()?>
    </div>
</div>

