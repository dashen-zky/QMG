<?php
use yii\bootstrap\Html;
use backend\models\ViewHelper;
use backend\modules\daily\models\regulation\Regulation;
use yii\helpers\Url;
use backend\modules\hr\models\Department;
use yii\helpers\Json;
use yii\bootstrap\ActiveForm;
?>
<div class="panel-body panel">
<?php $form = ActiveForm::begin([
    'options'=>[
        'enctype'=>'multipart/form-data',
        'class' => 'form-horizontal RegulationForm',
        'data-parsley-validate' => "true",
    ],
    'method' => 'post',
    'action' => $action,
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
])?>
<input hidden name="RegulationForm[uuid]" value="<?= isset($formData['uuid'])?$formData['uuid']:''?>">
<table class="table">
    <tbody>
    <tr>
        <td class="col-md-1">*标题</td>
        <td colspan="2">
            <div class="col-md-12">
                <?= Html::textInput('RegulationForm[title]',
                    isset($formData['title'])?$formData['title']:'',
                    [
                        'data-parsley-required'=>'true',
                        'class' => 'form-control col-md-12'
                    ]) ?>
            </div>
        </td>
        <td>
            <div class="col-md-12">
            <?= Html::textInput('RegulationForm[tags]',
                isset($formData['tags'])?$formData['tags']:'',
                [
                    'placeholder'=>'标签',
                    'class' => 'form-control col-md-12'
                ]) ?>
            </div>
        </td>
        <td colspan="2">
            <div style="float:left;margin-right: 30px">
            <a href="javascript:;" class="show-employee-panel" name="<?= Url::to([
                '/daily/regulation/employee-list',
            ])?>">指定可见人员</a>
                <!-- 这个字段里面存放json字符串-->
            <input hidden name="RegulationForm[pointed_uuid]" class="pointed-uuid"
                   value="<?= isset($formData['pointed_uuid'])?$formData['pointed_uuid']:''?>">
            </div>
            <div style="float: left">
            <a href="javascript:;" class="show-editor-panel" name="<?= Url::to([
                '/daily/regulation/editor-list',
            ])?>">指定可编辑人员</a>
            <!-- 这个字段里面存放json字符串-->
            <input hidden name="RegulationForm[editor_uuid]" class="editor-uuid"
                   value="<?= isset($formData['editor_uuid'])?$formData['editor_uuid']:''?>">
            </div>
        </td>
    </tr>
    <tr>
        <td class="col-md-1">*编码</td>
        <td class="col-md-3">
            <div class="col-md-12">
                <?= Html::textInput('RegulationForm[code]',
                    isset($formData['code'])?$formData['code']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'data-parsley-required'=>'true',
                    ]) ?>
            </div>
        </td>
        <td class="col-md-1">制度类型</td>
        <td class="col-md-3">
            <div class="col-md-12">
                <?= Html::dropDownList(
                    'RegulationForm[type]',
                    null,
                    Regulation::typeList(),
                    [
                        'class'=>'form-control department col-md-12',
                        'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'type'),
                    ]
                ) ?>
            </div>
        </td>
        <td class="col-md-1">创建时间</td>
        <td class="col-md-3">
            <div class="col-md-12">
                <?= Html::textInput('RegulationForm[created_time]',
                    isset($formData['created_time'])?
                        (($formData['created_time'] != 0)?date("Y-m-d",$formData['created_time']):''):'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="col-md-1">创建人</td>
        <td class="col-md-3">
            <div class="col-md-12">
                <?= Html::textInput('RegulationForm[created_name]',
                    isset($formData['created_name'])?$formData['created_name']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </div>
        </td>
        <td class="col-md-1">更新时间</td>
        <td class="col-md-3">
            <div class="col-md-12">
                <?= Html::textInput('RegulationForm[update_time]',
                    isset($formData['update_time'])?
                        (($formData['update_time'] != 0)?date("Y-m-d",$formData['update_time']):''):'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </div>
        </td>
        <td class="col-md-1">更新人</td>
        <td class="col-md-3">
            <div class="col-md-12">
                <?= Html::textInput('RegulationForm[update_name]',
                    isset($formData['update_name'])?$formData['update_name']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled' => true,
                    ]) ?>
            </div>
        </td>
    </tr>
    <tr>
        <td>摘要</td>
        <td colspan="5">
            <div class="col-md-12">
                <?= Html::textarea('RegulationForm[abstract]',
                    isset($formData['abstract'])?$formData['abstract']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'rows'=>3,
                    ])?>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <script id="editor" type="text/plain" style="height:800px;">
                <?= isset($formData['content'])?$formData['content']:''?>
            </script>
        </td>
    </tr>
    <tr>
        <td>
        附件
        </td>
        <td>
            <?= $form->field(new \backend\models\UploadForm([
                'fileRules'=>[
                    'maxFiles'=>10
                ]
            ]),'file[]')->fileInput([
                'multiple' => true,
            ])?>
        </td>
        <td colspan="4">
            <?php if(isset($formData['path']) && !empty($formData['path'])) :?>
                <?php
                // 将path字段解析出来
                $formData['path'] = unserialize($formData['path']);
                ?>
                <?php foreach($formData['path'] as $key=>$path) :?>
                    <div>
                        <a href="<?= Url::to([
                            '/daily/regulation/attachment-download',
                            'path'=>$path,
                            'file_name'=>$key,
                        ])?>"><?= $key?></a>
                        <span style="float: right">
                            <a href="#" name="<?= Url::to([
                                '/daily/regulation/attachment-delete',
                                'path'=>$path,
                                'uuid'=>$formData['uuid'],
                            ])?>" class="attachmentDelete">删除</a>
                        </span>
                    </div>
                <?php endforeach?>
            <?php endif?>
        </td>
    </tr>
    <tr></tr>
    </tbody>
</table>
<span class="col-md-12">
    <span class="col-md-4"></span>
    <span class="col-md-4">
        <input type="submit" value="提交" class="form-control btn-primary">
    </span>
    <span class="col-md-4"></span>
</span>
<?php ActiveForm::end()?>
<!--// 指定可见人员-->
<?= $this->render('@hr/views/employee/employee-select-list-panel-advance.php',[
    'class'=>'select-watcher-container-modal',
    'filters'=>[
        'employee_uuid'=>isset($formData['pointed_uuid'])?$formData['pointed_uuid']:null,
        'employee_name'=>isset($formData['pointed_name'])?$formData['pointed_name']:null,
    ],
    'fieldInformation'=>Json::encode([
        0=>['RegulationForm', 'pointed-uuid'],
    ]),
    'departmentList'=>(new Department())->departmentListForDropDownList(),
])?>
<!--//指定可编辑人员-->
<?= $this->render('@hr/views/employee/employee-select-list-panel-advance.php',[
    'class'=>'select-editor-container-modal',
    'filters'=>[
        'employee_uuid'=>isset($formData['editor_uuid'])?$formData['editor_uuid']:null,
        'employee_name'=>isset($formData['editor_name'])?$formData['editor_name']:null,
    ],
    'fieldInformation'=>Json::encode([
        0=>['RegulationForm', 'editor-uuid'],
    ]),
    'departmentList'=>(new Department())->departmentListForDropDownList(),
])?>
</div>

<?php
$JS = <<<JS
$(function() {
var ue = UE.getEditor('editor');
// 点击弹出选择可以查看的人员
$('.RegulationForm').on('click','.show-employee-panel', function() {
    var url = $(this).attr('name');
    var form = $(this).parents('form');
    var pointed_uuid = form.find('.pointed-uuid').val();
    url += '&uuids='+pointed_uuid;
    $.get(
    url,
    function(data,status) {
        if('success' == status) {
            var employee_modal = form.parents('.panel-body').find(".select-watcher-container-modal");
            var employee_list_container = employee_modal.find(".panel-body div.employee-list");
            employee_list_container.html(data);
            // 选定好了的，但是没有没有提交的员工，当再一次加载这个文档的时候，我们应该让它被checked
            var selected = employee_modal.find('.selected-employee-tags li');
            selected.each(function() {
                var uuid = $(this).find('.tag .tag-close').attr('id');
                employee_list_container.find('input#'+uuid).attr('checked', true);
            });
            employee_modal.modal('show');
        }
    });
});

// 点击弹出选择可以编辑的人员
$('.RegulationForm').on('click','.show-editor-panel', function() {
    var url = $(this).attr('name');
    var form = $(this).parents('form');
    var editor_uuid = form.find('.editor-uuid').val();
    url += '&uuids='+editor_uuid;
    $.get(
    url,
    function(data,status) {
        if('success' == status) {
            var employee_modal = form.parents('.panel-body').find(".select-editor-container-modal");
            var employee_list_container = employee_modal.find(".panel-body div.employee-list");
            employee_list_container.html(data);
            // 选定好了的，但是没有没有提交的员工，当再一次加载这个文档的时候，我们应该让它被checked
            var selected = employee_modal.find('.selected-employee-tags li');
            selected.each(function() {
                var uuid = $(this).find('.tag .tag-close').attr('id');
                employee_list_container.find('input#'+uuid).attr('checked', true);
            });
            employee_modal.modal('show');
        }
    });
});

    // 附件删除功能
 $('.RegulationForm').on('click','.attachmentDelete',function() {
    var url = $(this).attr('name');
    var self = $(this);
    $.get(
    url,
    function(data,status) {
        if('success' == status) {
            if(data) {
                self.parentsUntil('td').remove();
            }
        }
    });
 });
})
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>