<?php
use yii\widgets\Pjax;
?>
<div class="modal fade SelectContactPanel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="panel panel-body" data-sortable-id="table-basic-4">
        <!-- begin panel -->
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></a>
            </div>
            <h4 class="panel-title">联系人列表</h4>
        </div>
        <?php Pjax::begin()?>
        <?php
        $JS = <<< JS
        $(function() {
            $('.SelectContactPanel').on('click','.selectContact',function() {
                var contactUuid = '';
                var contactName = '';
                var i = 0;
                var panel = $(this).parents('.SelectContactPanel');
                panel.find("table tbody input[name='uuid']:checked").each(function() {
                    if (i === 0) {
                        contactUuid = $(this).val();
                        contactName = $(this).parent().next()[0].innerHTML;
                        i = 1;
                    } else {
                        contactUuid += "," + $(this).val();
                        contactName += "," + $(this).parent().next()[0].innerHTML;
                    }
                });
                var projectContactNameField = $('.project-table input[name="ProjectForm[project_contact_name]"]');
                var projectContactUuidField = $('.project-table input[name="ProjectForm[project_contact_uuid]"]');
                projectContactUuidField.val(contactUuid);
                projectContactNameField.val(contactName);
                $('.SelectContactPanel').modal('hide');
            });

            $('.SelectContactPanel').on('click','.selectDuty',function() {
                var contactUuid = '';
                var contactName = '';
                var i = 0;
                var panel = $(this).parents('.SelectContactPanel');
                panel.find("table tbody input[name='uuid']:checked").each(function() {
                    if (i === 0) {
                        contactUuid = $(this).val();
                        contactName = $(this).parent().next()[0].innerHTML;
                        i = 1;
                    } else {
                        contactUuid += "," + $(this).val();
                        contactName += "," + $(this).parent().next()[0].innerHTML;
                    }
                });
                var projectDutyNameField = $('.project-table input[name="ProjectForm[project_duty_name]"]');
                var projectDutyUuidField = $('.project-table input[name="ProjectForm[project_duty_uuid]"]');
                projectDutyUuidField.val(contactUuid);
                projectDutyNameField.val(contactName);
                $('.SelectContactPanel').modal('hide');
            });
        });
JS;
        $this->registerJs($JS, \yii\web\View::POS_END);
        ?>
        <div class="contact-list">
        </div>
        <?php Pjax::end()?>
    </div>
</div>