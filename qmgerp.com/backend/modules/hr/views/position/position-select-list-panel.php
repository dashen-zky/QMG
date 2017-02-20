<?php
use yii\web\View;
use yii\helpers\Html;
use backend\models\ViewHelper;
use yii\helpers\Url;
?>
<div class="select-position-container-modal modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-content">
            <div class="modal-header">
                <div>
                    <span style="float: left"><strong style="font-size: 15px;">已选职位</strong></span>
        <span class="selected-position-tags" style="margin-bottom: 10px; float: left">
            <ul class="float-left">
                <?php
                if(isset($filters['position_uuid']) && !empty($filters['position_uuid'])) :
                    $position_uuids = explode(',', $filters['position_uuid']);
                    $position_names = explode(',', $filters['position_name']);?>
                    <?php for($i = 0; $i < count($position_uuids); $i++) :?>
                    <li>
                        <div class="tag">
                            <span class="tag-content"><?= $position_names[$i]?></span>
                            <span class="tag-close" id="<?= $position_uuids[$i]?>">
                                <a href="javascript:;">×</a>
                            </span>
                        </div>
                    </li>
                <?php endfor?>
                <?php endif?>
            </ul>
        </span>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="panel panel-body" data-sortable-id="table-basic-4">
                <?php
                $JS = <<<JS
$(document).ready(function() {
    // 搜索ajax
    $('.select-position-container-modal .position-list-filter').on('click', '.submit',function() {
        var form = $(this).parents('form');
        var url = form.attr('action');
        $.ajax({
            url: url,
            type: 'post',
            data: form.serialize(),
            success: function (data) {
                $(".select-position-container-modal .panel-body div.position-list").html(data);
            }
        });
    });
    // 部门的选择的联动效果
    $('.position-list-filter').on('change','.department-1',function() {
        var url = $(this).attr('id');
        url += "&uuid="+$(this).val();
        $.get(
        url,
        function(data, status) {
            if(status === 'success') {
                var form = $('.select-position-container-modal .position-list-filter');
                if(data == '<option value="0">未选择</option>') {
                    form.find('.department-3').html(data);
                }
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
                var form = $('.select-position-container-modal .position-list-filter');
                form.find('.department-3').html(data);
            }
        }
        );
    });
    // 删除tag的按钮
    $('.select-position-container-modal').on('click','.selected-position-tags .tag-close',function(){
        var uuid = $(this).attr('id');
        var position_uuid = $('.select-position-container-modal').find('.position-list #'+uuid);
        position_uuid.attr('checked', false);
        $(this).parentsUntil('ul').remove();
        // 将删除操作同步到listform里面去
        var position_name_filed = $('.select-position-container-modal .position-list-filter .position-name');
        var position_uuid_filed = $('.select-position-container-modal .position-list-filter .position-uuid');
        var position_name_value = position_name_filed.val();
        position_name_value = position_name_value.split(',');
        var position_uuid_value = position_uuid_filed.val();
        position_uuid_value = position_uuid_value.split(',');
        for(var i = 0; i < position_uuid_value.length; i++) {
            if(position_uuid_value[i] === uuid) {
                position_name_value.splice(i,1);
                position_uuid_value.splice(i,1);
                break;
            }
        }
        position_name_filed.val(position_name_value.join(','));
        position_uuid_filed.val(position_uuid_value.join(','));
    });
    // checkbox点击事件，作的功能
    $('.select-position-container-modal').on('click','.position-list .position-uuid',function() {
        var checked = $(this).attr('checked');
        var position_tags = $('.select-position-container-modal .selected-position-tags ul');
        var uuid = $(this).val();
        var position_name = $(this).parents('tr').find('.position-name')[0].innerHTML;
        // listfilter 里面两个元素保存已选择的职位信息
        var position_name_filed = $('.select-position-container-modal .position-list-filter .position-name');
        var position_uuid_filed = $('.select-position-container-modal .position-list-filter .position-uuid');
        if(checked === 'checked') {
            var html = '<li>' +
             '<div class="tag">' +
               '<span class="tag-content">'+position_name+'</span>' +
                '<span class="tag-close" id="'+uuid+'">' +
                 '<a href="javascript:;">×</a>' +
                  '</span>' +
                   '</div>' +
                    '</li>';
            position_tags.append(html);
            // 将元素保存到listfilterform里面去

            var position_name_value = position_name_filed.val();
            // 将其以逗号分开
            position_name_value = position_name_value.split(',');
            // 将空的元素删除掉
            position_name_value = $.grep(position_name_value, function(n) {return $.trim(n).length > 0;});

            var position_uuid_value = position_uuid_filed.val();
            position_uuid_value = position_uuid_value.split(',');
            position_uuid_value = $.grep(position_uuid_value, function(n) {return $.trim(n).length > 0;});

            position_name_value.push(position_name);
            position_uuid_value.push(uuid);
            position_name_filed.val(position_name_value.join(','));
            position_uuid_filed.val(position_uuid_value.join(','));
        } else {
            var uuid_filed = position_tags.find('#'+uuid);
            uuid_filed.parentsUntil('ul').remove();
            // 将元素从listfilterform里面删去
            var position_name_value = position_name_filed.val();
            position_name_value = position_name_value.split(',');
            var position_uuid_value = position_uuid_filed.val();
            position_uuid_value = position_uuid_value.split(',');
            for(var i = 0; i < position_uuid_value.length; i++) {
                if(position_uuid_value[i] === uuid) {
                    position_name_value.splice(i,1);
                    position_uuid_value.splice(i,1);
                    break;
                }
            }
            position_name_filed.val(position_name_value.join(','));
            position_uuid_filed.val(position_uuid_value.join(','));
        }
    });
    $('.select-position-container-modal').on('click','.select-position', function() {
        // 获取list form filter 存储的职位信息
        var position_name_filed = $('.select-position-container-modal .position-list-filter .position-name');
        var position_uuid_filed = $('.select-position-container-modal .position-list-filter .position-uuid');
        var position_name_value = position_name_filed.val();
        var position_uuid_value = position_uuid_filed.val();
        var position_uuid = $("form#employeeForm input[name='EmployeeForm[position_uuid]']");
        var position_name = $("form#employeeForm input[name='EmployeeForm[position_name]']");
        position_name.val(position_name_value);
        position_uuid.val(position_uuid_value);
        $('.select-position-container-modal').modal('hide');
    });
});
JS;
                $this->registerJs($JS, View::POS_END);
                ?>
                <?= Html::beginForm(['/hr/employee/position-list-filter'], 'post', ['data-pjax' => '', 'class' => 'position-list-filter']); ?>
                <input class='position-name' disabled hidden
                       value="<?= isset($filters['position_uuid'])?$filters['position_name']:''?>">
                <input class='position-uuid' name="ListFilterForm[position_uuid]" hidden
                       value="<?= isset($filters['position_name'])?$filters['position_uuid']:''?>">
                <table class="table position-list-filter-table">
                    <tbody>
                    <tr>
                        <td class="col-md-1">公司</td>
                        <td class="col-md-3">
                            <?= Html::dropDownList(
                                'ListFilterForm[department][1]',
                                null,
                                ViewHelper::appendElementOnDropDownList(isset($filters['department'][1])?
                                    $filters['department'][1]:[]),
                                [
                                    'data-parsley-required'=>'true',
                                    'class'=>'form-control department-1 col-md-12',
                                    'id'=>Url::to([
                                        '/hr/position/department-list'
                                    ]),
                                ]
                            ) ?>
                        </td>
                        <td class="col-md-1">事业部</td>
                        <td class="col-md-3">
                            <?= Html::dropDownList(
                                'ListFilterForm[department][2]',
                                null,
                                ViewHelper::appendElementOnDropDownList(isset($filters['department'][2])?
                                    $filters['department'][2]:[]),
                                [
                                    'class'=>'form-control department-2 col-md-12',
                                    'id'=>Url::to([
                                        '/hr/position/department-list'
                                    ]),
                                    'options'=>ViewHelper::defaultValueForDropDownList(true, $filters,'department_level_2'),
                                ]
                            ) ?>
                        </td>
                        <td class="col-md-1">部门</td>
                        <td class="col-md-3">
                            <?= Html::dropDownList(
                                'ListFilterForm[department][3]',
                                null,
                                ViewHelper::appendElementOnDropDownList(isset($filters['department'][3])?
                                    $filters['department'][3]:[]),
                                [
                                    'id'=>Url::to([
                                        '/hr/position/department-list'
                                    ]),
                                    'class'=>'form-control department-3 col-md-12',
                                    'options'=>ViewHelper::defaultValueForDropDownList(true, $filters,'department_level_3'),
                                ]
                            ) ?>
                        </td>
                        <td>
                            <?= Html::button('搜索', ['class' => 'form-control btn btn-primary submit']) ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?= Html::endForm()?>
                <div class="position-list"></div>
                <a href="#"><button class="btn btn-primary col-md-3 select-position" style="float: right">选择</button></a>
            </div>
        </div>
</div>

