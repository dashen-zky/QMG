<?= $this->render('@webroot/../views/site/panel-header',[
    'title'=>'添加配置',
    'class'=>'col-md-6'
])?>
<?php
use yii\helpers\Html;
use backend\modules\fin\payment\models\PaymentConfig;
use yii\helpers\Url;
use yii\helpers\Json;
use backend\modules\hr\models\Department;
?>
<?php
$paymentConfig = new PaymentConfig();
$assess_conditions = $paymentConfig->getList('assess_condition');
$stampItemList = $paymentConfig->getList('with_stamp');
?>
<?= Html::beginForm($action, 'post', [
        'class' => 'form-horizontal PaymentAssessConfigForm',
        'data-parsley-validate' => "true",
])?>
<?= Html::input('hidden', 'PaymentAssessConfigForm[step]', $step)?>
<table class="table">
    <tr>
        <td colspan="3">
            <input hidden class="checked-condition" value="<?= PaymentConfig::StampCondition?>">
            <?php foreach($assess_conditions as $index=>$item) :?>
                <div class="col-md-3">
                    <?= Html::radio('PaymentAssessConfigForm[type]',($index == PaymentConfig::StampCondition)?true:false, [
                        'value'=>$index,
                        'data-parsley-required'=>'true',
                        'url'=>Url::to([
                            $choose_condition_type_url,
                            'type'=>$index,
                            /**
                             * 表明是什么入口，现在入口有日常管理，（daily）
                             * 项目管理，project_manage
                             * 媒介的项目申请付款 project_media
                             */
                            'entrance'=>$entrance,
                        ]),
                        'class'=>'condition-type',
                    ])?><?= $item?>
                </div>
            <?php endforeach?>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <div class="condition-item">
                <?php foreach($stampItemList as $index=>$item) :?>
                    <div class="col-md-3"><?= Html::radio('PaymentAssessConfigForm[purpose]', false, [
                            'value'=>$index,
                            'data-parsley-required'=>'true',
                        ])?><?= $item?></div>
                <?php endforeach?>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            审核人*
        </td>
        <td>
            <?= Html::input('text', 'PaymentAssessConfigForm[assess_name]', null, [
                'disabled'=>"disabled",
                'class'=>'assess-name form-control',
                'data-parsley-required'=>'true',
            ])?>
            <?= Html::input('hidden', 'PaymentAssessConfigForm[assess_uuid]', null, [
                'class'=>'assess-uuid form-control',
                'data-parsley-required'=>'true',
            ])?>
        </td>
        <td>
            <a href="#" name="<?= Url::to([
                '/system/payment-assess-config/employee-list',
            ])?>" class="show-employee-panel">
                <i class="fa fa-2x fa-edit"></i>
            </a>
        </td>
    </tr>
    <tr>
    </tr>
</table>
<span class="col-md-12">
    <span class="col-md-4"></span>
    <span class="col-md-4">
        <input type="submit" value="提交" class="form-control btn-primary">
    </span>
    <span class="col-md-4"></span>
</span>
<?= Html::endForm()?>
<?= $this->render('@hr/views/employee/employee-select-list-panel-advance.php',[
    'filters'=>[
        'employee_uuid'=>null,
        'employee_name'=>null,
    ],
    'fieldInformation'=>Json::encode([
        0=>['PaymentAssessConfigForm', 'assess-uuid'],
        1=>['PaymentAssessConfigForm', 'assess-name'],
    ]),
    'departmentList'=>(new Department())->departmentListForDropDownList(),
])?>
<?= $this->render('@webroot/../views/site/panel-footer')?>
<?php
$JS = <<<JS
$(function() {
$('.PaymentAssessConfigForm').on('click','.condition-type', function() {
    var checked_condition = $(this).parents('td').find('.checked-condition');
    var checked_value = checked_condition.val();
    var self = $(this);
    var this_value = self.val();
    if(this_value == checked_value) {
        return ;
    }
    // 将新选定的值保存起来
    checked_condition.val(this_value);
    var url = $(this).attr('url');
    $.get(
    url,
    function(data,status) {
        if(status === 'success') {
            var form = self.parents('form');
            form.find('.condition-item').html(data);
        }
    });
});
// 选择审核人列表
$('.PaymentAssessConfigForm').on('click','.show-employee-panel', function() {
    var url = $(this).attr('name');
    var form = $(this).parents('form');
    var duty_uuid = form.find('.duty-uuid').val();
    url += '&uuids='+duty_uuid;
    var self = $(this);
    $.get(
    url,
    function(data,status) {
        if('success' == status) {
            var container = self.parents('.tab-pane');
            var employee_modal = container.find(".select-employee-container-modal");
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
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>
