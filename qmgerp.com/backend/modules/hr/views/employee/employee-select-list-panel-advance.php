<?php
use yii\web\View;
use yii\helpers\Html;
use backend\models\ViewHelper;
use yii\helpers\Url;
?>
<div class="<?= isset($class)?$class:''?> select-employee-container-modal scroll modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" xmlns="http://www.w3.org/1999/html">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <div>
                <span style="float: left"><strong style="font-size: 15px;">已选人员</strong></span>
        <span class="selected-employee-tags" style="margin-bottom: 10px; float: left">
            <ul class="float-left">
                <?php
                if(isset($filters['employee_uuid']) && !empty($filters['employee_uuid'])) :
                    $employee_uuids = explode(',', $filters['employee_uuid']);
                    $employee_names = explode(',', $filters['employee_name']);?>
                    <?php for($i = 0; $i < count($employee_uuids); $i++) :?>
                    <li>
                        <div class="tag">
                            <span class="tag-content"><?= $employee_names[$i]?></span>
                            <span class="tag-close" id="<?= $employee_uuids[$i]?>">
                                <a href="javascript:;">×</a>
                            </span>
                        </div>
                    </li>
                <?php endfor?>
                <?php endif?>
            </ul>
        </span>
            </div>
        </div>
        <div class="panel panel-body" data-sortable-id="table-basic-4">
            <?php
            $JS = <<<JS
$(document).ready(function() {
    // 选择提交功能
    $('.select-employee-container-modal').on('click','.select-employee',function() {
        var modal = $(this).parents('.select-employee-container-modal');
        var employee_name_filed = modal.find('.employee-list-filter .employee-name');
        var employee_uuid_filed = modal.find('.employee-list-filter .employee-uuid');
        var employee_name_value = employee_name_filed.val();
        var employee_uuid_value = employee_uuid_filed.val();
        var field_information = JSON.parse($(this).attr('name'));
        // 给表单相对应的域赋值
        var contianer = $(this).parents('.tab-pane');
        for(var i = 0; i < field_information.length; i++) {
            if(field_information[i][1].indexOf("uuid") > -1) {
                contianer.find('.'+field_information[i][0]).find('.'+field_information[i][1]).val(employee_uuid_value);
            } else {
                contianer.find('.'+field_information[i][0]).find('.'+field_information[i][1]).val(employee_name_value);
           }
        }
        modal.modal('hide');
    });
    // 全选功能
    $('.select-employee-container-modal').on('click','.select-all',function() {
        var checked = $(this).attr('checked');
        var modal = $(this).parents('.select-employee-container-modal');
        var employee_tags = modal.find('.selected-employee-tags ul');
        var employee_name_filed = modal.find('.employee-list-filter .employee-name');
        var employee_uuid_filed = modal.find('.employee-list-filter .employee-uuid');
        if(checked == 'checked') {
            var employee_uuid_list = modal.find('.employee-list .employee-uuid').not("input:checked");
            employee_uuid_list.attr('checked',true);
            var html = '';
            var i = 0;
            var employee_name = new Array();
            var employee_uuid = new Array();
            employee_uuid_list.each(function() {
                var name = $(this).attr('name');
                employee_name[i] = name;
                employee_uuid[i] = $(this).val();
                html += '<li>' +
             '<div class="tag">' +
               '<span class="tag-content">'+employee_name[i]+'</span>' +
                '<span class="tag-close" id="'+employee_uuid[i]+'">' +
                 '<a href="javascript:;">×</a>' +
                  '</span>' +
                   '</div>' +
                    '</li>';
                i++;
            });
            employee_tags.append(html);

            var employee_name_value = employee_name_filed.val();
            // 将其以逗号分开
            employee_name_value = employee_name_value.split(',');
            // 将空的元素删除掉
            employee_name_value = $.grep(employee_name_value, function(n) {return $.trim(n).length > 0;});

            var employee_uuid_value = employee_uuid_filed.val();
            employee_uuid_value = employee_uuid_value.split(',');
            employee_uuid_value = $.grep(employee_uuid_value, function(n) {return $.trim(n).length > 0;});

            employee_name_value.push(employee_name);
            employee_uuid_value.push(employee_uuid);
            employee_name_filed.val(employee_name_value.join(','));
            employee_uuid_filed.val(employee_uuid_value.join(','));
        } else {
            var employee_uuid_list = modal.find('.employee-list .employee-uuid:checked');
            employee_uuid_list.attr('checked',false);
            var employee_name = new Array();
            var employee_uuid = new Array();
            var i = 0;
            employee_uuid_list.each(function() {
                var uuid = $(this).val();
                var uuid_filed = employee_tags.find('#'+uuid);
                uuid_filed.parentsUntil('ul').remove();
                employee_name[i] = $(this).attr('name');
                employee_uuid[i] = uuid;
                i++;
            });
            // 将元素从listfilterform里面删去
            var employee_name_value = employee_name_filed.val();
            var employee_uuid_value = employee_uuid_filed.val();
            for(var i = 0; i < employee_uuid.length; i++) {
                employee_name_value = employee_name_value.replace(eval('/'+employee_name[i]+'\,?/'), '');
                employee_uuid_value = employee_uuid_value.replace(eval('/'+employee_uuid[i]+'\,?/'), '');
            }
            employee_name_filed.val(employee_name_value);
            employee_uuid_filed.val(employee_uuid_value);
        }
    });
    // 搜索ajax
    $('.select-employee-container-modal .employee-list-filter').on('click', '.submit',function() {
        var form = $(this).parents('form');
        var modal = $(this).parents('.select-employee-container-modal');
        var url = form.attr('action');
        $.ajax({
            url: url,
            type: 'post',
            data: form.serialize(),
            success: function (data) {
                modal.find(".panel-body div.employee-list").html(data);
            }
        });
    });
    // 删除tag的按钮
    $('.select-employee-container-modal').on('click','.selected-employee-tags .tag-close',function(){
        var uuid = $(this).attr('id');
        var modal = $(this).parents('.select-employee-container-modal');
        var employee_uuid = modal.find('.employee-list #'+uuid);
        employee_uuid.attr('checked', false);
        $(this).parentsUntil('ul').remove();
        // 将删除操作同步到listform里面去
        var employee_name_filed = modal.find('.employee-list-filter .employee-name');
        var employee_uuid_filed = modal.find('.employee-list-filter .employee-uuid');
        var employee_name_value = employee_name_filed.val();
        employee_name_value = employee_name_value.split(',');
        var employee_uuid_value = employee_uuid_filed.val();
        employee_uuid_value = employee_uuid_value.split(',');
        for(var i = 0; i < employee_uuid_value.length; i++) {
            if(employee_uuid_value[i] === uuid) {
                employee_name_value.splice(i,1);
                employee_uuid_value.splice(i,1);
                break;
            }
        }
        employee_name_filed.val(employee_name_value.join(','));
        employee_uuid_filed.val(employee_uuid_value.join(','));
    });
    // checkbox点击事件，作的功能
    $('.select-employee-container-modal').on('click','.employee-list .employee-uuid',function() {
        var checked = $(this).attr('checked');
        var modal = $(this).parents('.select-employee-container-modal');
        var employee_tags = modal.find('.selected-employee-tags ul');
        var uuid = $(this).val();
        var employee_name = $(this).parents('tr').find('.employee-name')[0].innerHTML;
        // listfilter 里面两个元素保存已选择的职位信息
        var employee_name_filed = modal.find('.employee-list-filter .employee-name');
        var employee_uuid_filed = modal.find('.employee-list-filter .employee-uuid');
        if(checked === 'checked') {
            var html = '<li>' +
             '<div class="tag">' +
               '<span class="tag-content">'+employee_name+'</span>' +
                '<span class="tag-close" id="'+uuid+'">' +
                 '<a href="javascript:;">×</a>' +
                  '</span>' +
                   '</div>' +
                    '</li>';
            employee_tags.append(html);
            // 将元素保存到listfilterform里面去

            var employee_name_value = employee_name_filed.val();
            // 将其以逗号分开
            employee_name_value = employee_name_value.split(',');
            // 将空的元素删除掉
            employee_name_value = $.grep(employee_name_value, function(n) {return $.trim(n).length > 0;});

            var employee_uuid_value = employee_uuid_filed.val();
            employee_uuid_value = employee_uuid_value.split(',');
            employee_uuid_value = $.grep(employee_uuid_value, function(n) {return $.trim(n).length > 0;});

            employee_name_value.push(employee_name);
            employee_uuid_value.push(uuid);
            employee_name_filed.val(employee_name_value.join(','));
            employee_uuid_filed.val(employee_uuid_value.join(','));
        } else {
            var uuid_filed = employee_tags.find('#'+uuid);
            uuid_filed.parentsUntil('ul').remove();
            // 将元素从listfilterform里面删去
            var employee_name_value = employee_name_filed.val();
            employee_name_value = employee_name_value.split(',');
            var employee_uuid_value = employee_uuid_filed.val();
            employee_uuid_value = employee_uuid_value.split(',');
            for(var i = 0; i < employee_uuid_value.length; i++) {
                if(employee_uuid_value[i] === uuid) {
                    employee_name_value.splice(i,1);
                    employee_uuid_value.splice(i,1);
                    break;
                }
            }
            employee_name_filed.val(employee_name_value.join(','));
            employee_uuid_filed.val(employee_uuid_value.join(','));
        }
    });
});
JS;
            $this->registerJs($JS, View::POS_END);
            ?>
            <?= Html::beginForm(['/hr/employee/select-list-filter'], 'post', ['class' => 'employee-list-filter']); ?>
            <input class='employee-name' disabled hidden
                   value="<?= isset($filters['employee_name'])?$filters['employee_name']:''?>">
            <input class='employee-uuid' name="ListFilterForm[employee_uuid]" hidden
                   value="<?= isset($filters['employee_uuid'])?$filters['employee_uuid']:''?>">
            <table class="table position-list-filter-table">
                <tbody>
                <tr>
                    <td>姓名</td>
                    <td>
                        <?= Html::textInput('ListFilterForm[name]',null,[
                            'class'=>'form-control'
                        ])?>
                    </td>
                    <td>部门</td>
                    <td>
                        <?= Html::dropDownList('ListFilterForm[department_uuid]',
                            null,
                            ViewHelper::appendElementOnDropDownList($departmentList),
                            [
                                'class'=>'form-control'
                            ]
                        )?>
                    </td>
                    <td>
                        <?= Html::button('搜索', ['class' => 'form-control btn btn-primary submit']) ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <?= Html::endForm()?>
            <div><input type="checkbox" name="select-all" class="select-all" style="margin-right: 8px; margin-left: 16px">
                <label for="select-all">全选</label></div>
            <div class="employee-list"></div>
            <a href="#"><button class="btn btn-primary col-md-3 select-employee"
                                name = <?= $fieldInformation?>
                                style="float: right">选择</button></a>
        </div>
    </div>
</div>

