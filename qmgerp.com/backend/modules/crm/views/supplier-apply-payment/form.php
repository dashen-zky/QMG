<?php
use yii\helpers\Html;
use backend\modules\fin\payment\models\PaymentConfig;
use backend\models\ViewHelper;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Json;
use backend\modules\crm\models\supplier\record\SupplierPaymentMap;
use backend\modules\crm\models\customer\record\Contact;
use backend\models\BaseRecord;
use backend\modules\crm\models\supplier\record\SupplierFinAccountMap;
use backend\modules\crm\models\part_time\record\PartTimeFinAccountMap;
$paymentConfig = new PaymentConfig();

?>
<?= Html::beginForm($action, 'post', [
    'class' => 'form-horizontal ApplyPaymentForm',
    'data-parsley-validate' => "true",
])?>
<input hidden name="ApplyPaymentForm[uuid]" value="<?= isset($formData['uuid'])?$formData['uuid']:''?>">
    <table class="table">
        <tr>
            <td>
                申请日期
            </td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[created_time]',
                    (isset($formData['created_time']) && $formData['created_time'] !='0')?date("Y-m-d", $formData['created_time']):'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
            <td>
                 申请人
            </td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[created_name]',
                    isset($formData['created_name'])?$formData['created_name']:Yii::$app->user->getIdentity()->getEmployeeName(),
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
            <td>
                款项类型
            </td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[type_name]',
                    isset($formData['type'])?$paymentConfig->getAppointed('type', $formData['type']):$paymentConfig->getAppointed('type', PaymentConfig::PaymentForProjectMedia),
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
        </tr>
        <tr>
            <td>款项用途</td>
            <td>
                <?= Html::dropDownList(
                    'ApplyPaymentForm[purpose]',
                    isset($formData['purpose'])?$formData['purpose']:null,
                    isset($formData['type'])?
                        $paymentConfig->getList(
                            $paymentConfig->getAppointed('type_purpose_map', $formData['type'])
                        )
                    :$paymentConfig->getList(
                        $paymentConfig->getAppointed('type_purpose_map', PaymentConfig::PaymentForProjectMedia)
                    ),
                    [
                        'class'=>'form-control col-md-12',
                        'disabled'=>isset($show) && $show,
                        'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'purpose'),
                    ]
                    )?>
            </td>
            <td>
                期望付款时间*
            </td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[expect_time]',
                    (isset($formData['expect_time']) && $formData['expect_time'] !='0')?date("Y-m-d", $formData['expect_time']):'',
                    [
                        'data-parsley-required'=>"true",
                        'disabled'=>isset($show) && $show,
                        'class' => 'input-section datetimepicker form-control col-md-12',
                    ]) ?>
            </td>
            <td>发票</td>
            <td>
                <?= Html::dropDownList(
                    'ApplyPaymentForm[with_stamp]',
                    isset($formData['with_stamp'])?$formData['with_stamp']:null,
                    $paymentConfig->getList('with_stamp'),
                    [
                        'class'=>'form-control col-md-12',
                        'disabled'=>isset($show) && $show,
                    ]
                    )?>
            </td>
        </tr>
        <tr>
            <td>预算金额*</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[budget_money]',
                    isset($formData['budget_money'])?$formData['budget_money']:'',
                    [
                        'data-parsley-type'=>"number",
                        'data-parsley-required'=>"true",
                        'disabled'=>isset($show) && $show,
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
            <td>实际金额*</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[actual_money]',
                    isset($formData['actual_money'])?$formData['actual_money']:'',
                    [
                        'data-parsley-type'=>"number",
                        'data-parsley-required'=>"true",
                        'disabled'=>isset($show) && $show,
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
            <td>已支付金额</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[paied_money]',
                    isset($formData['paied_money'])?$formData['paied_money']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
        </tr>
        <tr>
            <td>付款人</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[paied_name]',
                    isset($formData['paied_name'])?$formData['paied_name']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
            <td>支付时间</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[paied_time]',
                    (isset($formData['paied_time']) && $formData['paied_time'] !='0')?date("Y-m-d", $formData['paied_time']):'',
                    [
                        'disabled'=>true,
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
            <td>已验票金额</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[checked_stamp_money]',
                    isset($formData['checked_stamp_money'])?$formData['checked_stamp_money']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>

        </tr>
        <tr>
            <td>项目*</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[project_name]',
                    isset($formData['project_name'])?$formData['project_name']:'',
                    [
                        'data-parsley-required'=>"true",
                        'disabled'=>true,
                        'class' => 'form-control col-md-12 project-name',
                    ]) ?>
                <input  name="ApplyPaymentForm[project_uuid]" type="hidden" class="project-uuid">
            </td>
            <td>
                <?php if(!isset($show) || !$show) :?>
                <a href="#" class="choose-project" name="<?= Url::to([
                    '/crm/supplier-apply-payment/project-list'
                ])?>"><i class="fa fa-2x fa-edit"></i></a>
                <?php endif?>
                供应商/兼职*
            </td>
            <td>
                <?php if(isset($show) && $show) :?>
                <input disabled class="form-control" value="<?= isset($formData['supplier_name'])?$formData['supplier_name']: (isset($formData['part_time_name']) ? $formData['part_time_name']:'' )?>">
                <?php else:?>
                    <?= Html::textInput('ApplyPaymentForm[supplier_name]',
                        isset($formData['supplier_name'])?$formData['supplier_name']:(
                        isset($formData['part_time_name'])?$formData['part_time_name']:''
                        ),
                        [
                            'data-parsley-required'=>"true",
                            'disabled'=>true,
                            'class' => 'form-control col-md-12 supplier-name',
                        ]) ?>
                    <input id="<?= Url::to([
                        '/crm/supplier-apply-payment/supplier-change'
                    ])?>" name="ApplyPaymentForm[supplier_uuid]"
                           data-parsley-required=true
                           type="hidden"
                           value="<?= isset($formData['supplier_uuid'])?$formData['supplier_uuid']:(
                           isset($formData['part_time_uuid'])?$formData['part_time_uuid']:''
                           )?>"
                           class="supplier-uuid">
                    <input
                        name="ApplyPaymentForm[supplier_type]"
                        type="hidden"
                        class="supplier-type"
                        value="<?= isset($formData['supplier_payment_map_supplier_type'])
                            ?$formData['supplier_payment_map_supplier_type']:''?>"
                    >
                <?php endif?>
            </td>
            <td>
                <?php if(!isset($show) || !$show) :?>
                <a href="#" class="choose-supplier" name="<?= Url::to([
                    '/crm/supplier-union-part-time/supplier-union-part-time-list'
                ])?>"><i class="fa fa-2x fa-edit"></i></a>
                <?php endif?>
                验票时间
            </td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[checked_stamp_time]',
                    (isset($formData['created_time']) && $formData['checked_stamp_time'] !='0')
                        ?date("Y-m-d", $formData['checked_stamp_time']):'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
        </tr>
        <tr>
            <td>联系人*</td>
            <td>
                <?php if(isset($show) && $show) :?>
                    <input class="form-control"  disabled value="<?= isset($formData['receiver_contact_name'])?$formData['receiver_contact_name']:null?>">
                <?php else:?>
                    <div class="contact">
                        <?php if(isset($formData['supplier_payment_map_supplier_type'])
                        && $formData['supplier_payment_map_supplier_type'] == SupplierPaymentMap::PartTime):?>
                            <?= Html::textInput('ApplyPaymentForm[receiver_contact_name]',
                            isset($formData['receiver_contact_name'])?$formData['receiver_contact_name']:'',
                            [
                                'class'=>'form-control',
                                'readOnly'=>true,
                            ]
                        )?>
                        <?php else :?>
                            <?php if(isset($formData['supplier_uuid']) && !empty($formData['supplier_uuid'])):?>
                                <?php
                                $contact = new Contact();
                                $contactList = $contact->getContactListByObjectUuid($formData['supplier_uuid'], 'supplier');
                                $helper = new BaseRecord();
                                $list1 = $helper->transformForDropDownList($contactList['contactList'],'uuid', 'name');
                                $list2 = $helper->transformForDropDownList($contactList['customerDutyList'],'uuid', 'name');
                                $contact_list = array_merge($list1, $list2);
                                ?>
                            <?php endif?>
                            <?= Html::dropDownList(
                                'ApplyPaymentForm[receiver_contact_uuid]',
                                isset($formData['supplier_payment_map_receiver_contact_uuid'])?$formData['supplier_payment_map_receiver_contact_uuid']:null,
                                ViewHelper::appendElementOnDropDownList(isset($contact_list)?$contact_list:[]),
                                [
                                    'class'=>'form-control col-md-12 contact-uuid',
                                    'disabled'=>isset($show) && $show,
                                    'data-parsley-required'=>"true",
                                    'url'=>Url::to([
                                        '/crm/supplier-apply-payment/get-contact-information',
                                    ]),
                                ]
                            )?>
                        <?php endif?>
                    </div>
                    <input hidden name="ApplyPaymentForm[receiver_contact_name]"
                           value="<?= isset($formData['receiver_contact_name'])?$formData['receiver_contact_name']:null?>"
                           class="contact-name">
                <?php endif?>
            </td>
            <td>联系电话*</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[receiver_contact_phone]',
                    isset($formData['receiver_contact_phone'])?$formData['receiver_contact_phone']:'',
                    [
                        'data-parsley-required'=>"true",
                        'class' => 'form-control col-md-12 contact-phone',
                        'readOnly'=>true,
                        'disabled'=>isset($show) && $show,
                    ]) ?>
            </td>
            <td>收款账号*</td>
            <td>
                <?php if(isset($show) && $show) :?>
                    <input class="form-control"  disabled value="<?= isset($formData['receiver_account'])?$formData['receiver_account']:null?>">
                <?php else:?>
                    <?php
                    if(isset($formData['supplier_payment_map_supplier_type'])) {
                        if($formData['supplier_payment_map_supplier_type'] == SupplierPaymentMap::Supplier) {
                            // 收款账户列表
                            $finAccountMap = new SupplierFinAccountMap();
                        } else if($formData['supplier_payment_map_supplier_type'] == SupplierPaymentMap::PartTime) {
                            $finAccountMap = new PartTimeFinAccountMap();
                        }
                        $uuid = isset($formData['supplier_uuid'])?$formData['supplier_uuid']:(
                        isset($formData['part_time_uuid'])?$formData['part_time_uuid']:'');
                        $finAccountList = $finAccountMap->finAccountList($uuid);
                        $helper = new BaseRecord();
                        $finAccountList = $helper->transformForDropDownList($finAccountList['list'], 'uuid', 'account', 'name');
                    }
                    ?>
                    <div class="receiver">
                        <?= Html::dropDownList(
                            'ApplyPaymentForm[account_uuid]',
                            isset($formData['supplier_payment_map_receiver_account_uuid'])?$formData['supplier_payment_map_receiver_account_uuid']:null,
                            ViewHelper::appendElementOnDropDownList(isset($finAccountList)?$finAccountList:[]),
                            [
                                'class'=>'form-control col-md-12 receiver-account-uuid',
                                'disabled'=>isset($show) && $show,
                                'data-parsley-required'=>"true",
                                'url'=>Url::to([
                                    '/crm/supplier-apply-payment/get-receiver-account-information'
                                ]),
                            ]
                        )?>
                    </div>
                    <input class="account-uuid" hidden
                           value="<?= isset($formData['receiver_account_uuid'])?$formData['receiver_account_uuid']:null?>"
                           name="ApplyPaymentForm[receiver_account_uuid]">
                    <input class="receiver-account" hidden
                           value="<?= isset($formData['receiver_account'])?$formData['receiver_account']:null?>"
                           name="ApplyPaymentForm[receiver_account]">
                <?php endif?>
            </td>
        </tr>
        <tr>
            <td>账号类型</td>
            <td>
                <?php if(isset($show) && $show) :?>
                    <input class="form-control"  disabled value="<?= isset($formData['receiver_account_type'])?$paymentConfig->getAppointed('receiver_account_type', $formData['receiver_account_type']):''?>">
                <?php else:?>
                    <input readonly class="form-control col-md-12 receiver-account-type-name"
                           value="<?=isset($formData['receiver_account_type'])?$paymentConfig->getAppointed('receiver_account_type', $formData['receiver_account_type']):''?>">
                    <?= Html::input('hidden', 'ApplyPaymentForm[receiver_account_type]',
                        isset($formData['receiver_account_type'])?$formData['receiver_account_type']:'',
                        [
                            'class' => 'form-control col-md-12 receiver-account-type',
                        ]) ?>
                <?php endif?>
            </td>
            <td>开户行</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[receiver_bank_of_deposit]',
                    isset($formData['receiver_bank_of_deposit'])?$formData['receiver_bank_of_deposit']:'',
                    [
                        'class' => 'form-control col-md-12 receiver-bank-of-deposit',
                        'disabled'=>isset($show) && $show,
                        'readOnly'=>true,
                    ]) ?>
            </td>
            <td>收款人*</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[receiver]',
                    isset($formData['receiver'])?$formData['receiver']:'',
                    [
                        'data-parsley-required'=>"true",
                        'disabled'=>isset($show) && $show,
                        'class' => 'form-control col-md-12 receiver',
                        'readOnly'=>true,
                    ]) ?>
            </td>
        </tr>
        <tr>
            <td>付款内容</td>
            <td colspan="5">
                <?= Html::textarea('ApplyPaymentForm[description]',
                    isset($formData['description'])?$formData['description']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>isset($show) && $show,
                        'rows'=>3,
                    ]
                )?>
            </td>
        </tr>
        <tr>
            <td>备注</td>
            <td colspan="5">
                <?= Html::textarea('ApplyPaymentForm[remarks]',
                    isset($formData['remarks'])?$formData['remarks']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>isset($show) && $show,
                        'rows'=>3,
                    ]
                )?>
            </td>
        </tr>
        <tr>
            <td>
                一级审核人
            </td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[first_assess_name]',
                    isset($formData['first_assess_name'])?$formData['first_assess_name']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
            <td>
                二级审核人
            </td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[second_assess_name]',
                    isset($formData['second_assess_name'])?$formData['second_assess_name']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
            <td>
                三级审核人
            </td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[third_assess_name]',
                    isset($formData['third_assess_name'])?$formData['third_assess_name']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
        </tr>
        <tr>
            <td>
                四级审核人
            </td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[fourth_assess_name]',
                    isset($formData['fourth_assess_name'])?$formData['fourth_assess_name']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
            <td colspan="4">
                <?= Html::textInput('ApplyPaymentForm[assessor_remind]',
                    isset($formData['assessor_remind'])?$formData['assessor_remind']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
        </tr>
        <?php if(isset($show) && $show) :?>
            <tr>
                <td  colspan="6">付款凭证</td>
            </tr>
        <?php endif;?>
    </table>
<?php if(isset($formData['evidence']) && !empty($formData['evidence'])) :?>
    <?php $evidence = unserialize($formData['evidence']);?>
    <?php $i = 0;?>
    <?php foreach ($evidence as $index=>$item) :?>
        <?php if($i % 3 === 0) :?>
            <span class="col-md-12">
        <?php endif;?>
        <span class="col-md-4"><img width="100%" src="<?= Yii::getAlias('@web').'/../'.$item?>"></span>
        <?php if($i % 3 === 2) :?>
            </span>
        <?php endif;?>
        <?php $i++?>
    <?php endforeach;?>
<?php endif;?>
<?php if(!isset($show) || !$show) :?>
    <span class="col-md-12">
    <span class="col-md-4"></span>
    <span class="col-md-2" style="display: <?= isset($edit) && $edit ? 'none':'block'?>;">
        <input type="button" name="<?= Url::to([
            '/crm/supplier-apply-payment/save-apply'
        ])?>" value="保存申请" class="save-apply-payment form-control btn-primary">
    </span>
<?php if(isset($edit) && $edit) :?>
    <span class="col-md-4">
        <input type="submit" value="保存" class="form-control btn-primary">
    </span>
<?php else:?>
    <span class="col-md-2">
        <input type="submit" value="提交申请" class="form-control btn-primary">
    </span>
<?php endif;?>
    <span class="col-md-4"></span>
</span>
<?php endif?>
<?= Html::endForm()?>
<?=  $this->render('/supplier-union-part-time/list-panel',[
    'fieldInformation'=>Json::encode([
        0=>['ApplyPaymentForm', 'supplier-name'],
        1=>['ApplyPaymentForm', 'supplier-uuid'],
        2=>['ApplyPaymentForm', 'supplier-type'],
    ]),
])?>
<?=  $this->render('/project-select/list-panel',[
    'fieldInformation'=>Json::encode([
        0=>['ApplyPaymentForm', 'project-name'],
        1=>['ApplyPaymentForm', 'project-uuid'],
    ]),
])?>
<?php
$Js = <<<Js
$(function() {
// 保存申请
    $('.ApplyPaymentForm').on('click', '.save-apply-payment', function() {
           var form = $(this).parents('form');
           form.attr('action', $(this).attr('name'));
           form.submit();
    });

    // 选择供应商
    $('.ApplyPaymentForm').on('click', '.choose-supplier', function() {
        var self = $(this);
        var url = self.attr('name');
        $.get(
        url,
        function(data, status) {
            if(status !== 'success') {
                return ;
            }

            var panel = self.parents('.panel-body');
            var modal = panel.find('.supplier-union-part-time-list');
            modal.find('.modal-body').html(data);
            modal.modal('show');
        });
    });

    // 选择项目
    $('.ApplyPaymentForm').on('click', '.choose-project', function() {
        var self = $(this);
        var url = self.attr('name');
        $.get(
        url,
        function(data, status) {
            if(status !== 'success') {
                return ;
            }

            var panel = self.parents('.panel-body');
            var modal = panel.find('.project-list');
            modal.find('.modal-body').html(data);
            modal.modal('show');
        });
    });

    // 已选择供应商或是兼职之后
    $('.ApplyPaymentForm').on('change', '.supplier-uuid', function() {
            // 先将一些无效的数据清空掉
           var form = $(this).parents('form');
           form.find('.receiver').attr('value', '');
           form.find('.receiver-account').attr('value', '');
           form.find('.receiver-account-type').attr('value', '');
           form.find('.receiver-account-type-name').attr('value', '');
           form.find('.receiver-bank-of-deposit').attr('value', '');
           form.find('.contact-phone').attr('value', '');

           var url = $(this).attr('id');
           var uuid = $(this).val();
           var type = $(this).parents('td').find('.supplier-type').val();
           url = url + '&uuid=' + uuid + '&type=' + type;
           $.get(
           url,
           function(data, status) {
                if(status !== 'success') {
                      return ;
                }

                data = JSON.parse(data);
                var cc = '';
                for(var key in data.html) {
                    form.find('.' + key ).html(data.html[key]);
                }
                for(var key in data.value) {
                    form.find('.' + key ).val(data.value[key]);
                }
           })
    });

    // 根据选择的联系人不同，展示出来的电话不一样
    $('.ApplyPaymentForm').on('change', '.contact-uuid', function() {
           var url = $(this).attr('url');
           var uuid = $(this).val();
           url = url + '&uuid=' + uuid;
           var self = $(this);
           $.get(
           url,
           function(data, status) {
                if(status !== 'success') {
                      return ;
                }
                var form = self.parents('form');
                data = JSON.parse(data);
                form.find('.contact-phone').val(data.phone);
                form.find('.contact-name').val(data.name);
           })
    });

    // 根据所选的账号不同，出来的相关的账号的信息也是不一样的
    $('.ApplyPaymentForm').on('change', '.receiver-account-uuid', function() {
           var url = $(this).attr('url');
           var uuid = $(this).val();
           url = url + '&uuid=' + uuid;
           var self = $(this);
           $.get(
           url,
           function(data, status) {
                if(status !== 'success') {
                      return ;
                }
                var form = self.parents('form');
                data = JSON.parse(data);
                form.find('.account-uuid').val(uuid);
                form.find('.receiver-account-type-name').val(data.type_name);
                form.find('.receiver-account-type').val(data.type);
                form.find('.receiver').val(data.name);
                form.find('.receiver-bank-of-deposit').val(data.bank_of_deposit);
                form.find('.receiver-account').val(data.account);
           })
    });
});
Js;
$this->registerJs($Js, View::POS_END);
?>
