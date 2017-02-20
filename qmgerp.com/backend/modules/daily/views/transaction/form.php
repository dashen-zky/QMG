<?php
use yii\helpers\Html;
use yii\helpers\Json;
use backend\modules\hr\models\Department;
use backend\modules\daily\models\transaction\TransactionConfig;
use yii\helpers\Url;
$config = new TransactionConfig();
?>
<?= Html::beginForm($action, 'post', [
    'class' => 'form-horizontal TransactionForm',
    'data-parsley-validate' => "true",
])?>
<input hidden value="<?= isset($back_tab)?$back_tab:''?>" name="TransactionForm[back_tab]">
<input value="<?= isset($formData['uuid'])?$formData['uuid'] : ''?>" hidden name="TransactionForm[uuid]">
<table class="table">
    <tr>
        <td class="col-md-1">标题*</td>
        <td class="col-md-3">
            <?= Html::textInput('TransactionForm[title]',
                isset($formData['title'])?$formData['title']:null,
                [
                    'disabled'=>isset($show)&&$show,
                    'class'=>'form-control enableEdit',
                    'data-parsley-required'=>'true',
                ])?>
        </td>
        <td class="col-md-1">任务截止日期</td>
        <td class="col-md-3">
            <?= Html::textInput("TransactionForm[expect_finish_time]",
                isset($formData['expect_finish_time']) &&
                $formData['expect_finish_time'] != 0 ?date("Y-m-d",$formData['expect_finish_time']):null,[
                    'class'=>'form-control input-section datetimepicker enableEdit',
                    'disabled'=>isset($show)&&$show,
                ])?>
        </td>
        <td class="col-md-1">状态</td>
        <td class="col-md-3">
            <?= Html::dropDownList('TransactionForm[status]',
                isset($formData['status'])?$formData['status']:'',
                $config->getList('status'),
                [
                    'disabled'=>true,
                    'class'=>'form-control',
                ])?>
        </td>
    </tr>
    <tr>
        <td>创建人</td>
        <td>
            <?= Html::textInput('TransactionForm[created_name]',
                isset($formData['created_name'])?$formData['created_name']:
                    Yii::$app->user->getIdentity()->getEmployeeName(),
                [
                    'disabled'=>true,
                    'class'=>'form-control',
                ])?>
        </td>
        <td>执行人</td>
        <td>
            <!-- 这个字段里面存放json字符串-->
            <input hidden name="TransactionForm[execute_uuid]" class="execute-uuid"
                   value="<?= isset($formData['execute_uuid'])?$formData['execute_uuid']:''?>">
            <?= Html::textInput('TransactionForm[execute_name]',
                isset($formData['execute_name'])?$formData['execute_name'] : null, [
                    'readOnly'=>true,
                    'class'=>'form-control execute-name',
                ])?>
        </td>
        <td>
            <?php if(!isset($show) || !$show) :?>
            <a href="javascript:;" class="show-employee-panel" url="<?= Url::to([
                '/daily/transaction/employee-list',
            ])?>"><i class="fa fa-2x fa-edit"></i></a>
            <?php endif;?>
        </td>
        <td>如果不指定执行人，默认自己是执行人，可以指定多个执行人</td>
    </tr>
    <tr>
        <td>内容*</td>
        <td colspan="5">
            <?= Html::textarea('TransactionForm[content]',
                isset($formData['content'])?$formData['content']:null,
                [
                    'disabled'=>isset($show)&&$show,
                    'class'=>'form-control enableEdit',
                    'data-parsley-required'=>'true',
                    'rows'=>'5'
                ])?>
        </td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>
</table>
<span class="col-md-12">
<span class="col-md-4"></span>
<span class="col-md-4">
    <input type="submit"
           value="提交" style="display: <?= isset($show) && $show ? 'none':'' ?>"
           class="form-control btn-primary displayBlockWhileEdit">
</span>
<span class="col-md-4"></span>
</span>
<?= Html::endForm()?>
<!--// 指定执行人员-->
<?= $this->render('@hr/views/employee/employee-select-list-panel-advance.php',[
    'class'=>'select-executor-container-modal',
    'filters'=>[
        'employee_uuid'=>isset($formData['execute_uuid'])?$formData['execute_uuid']:null,
        'employee_name'=>isset($formData['execute_name'])?$formData['execute_name']:null,
    ],
    'fieldInformation'=>Json::encode([
        0=>['TransactionForm', 'execute-uuid'],
        1=>['TransactionForm', 'execute-name'],
    ]),
    'departmentList'=>(new Department())->departmentListForDropDownList(),
])?>

<?php 
$JS = <<<JS
$(function () {
    // 点击弹出选择可以查看的人员
    $('.TransactionForm').on('click','.show-employee-panel', function() {
        var url = $(this).attr('url');
        var form = $(this).parents('form');
        var execute_uuid = form.find('.execute-uuid').val();
        url += '&uuids='+execute_uuid;
        $.get(
        url,
        function(data,status) {
            if('success' == status) {
                var employee_modal = form.parents('.panel-body').find(".select-executor-container-modal");
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
})
JS;

$this->registerJs($JS, \yii\web\View::POS_END);
?>
