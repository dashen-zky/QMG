<?php
use backend\modules\crm\models\project\model\ProjectForm;
use backend\modules\crm\models\supplier\model\SupplierForm;
use backend\modules\crm\models\part_time\model\PartTimeForm;
use backend\modules\fin\payment\models\PaymentConfig;
$paymentConfig = new PaymentConfig();
?>
<div class="panel">
<table class="table table-bordered">
    <?php if(isset($formData['project_code'])) :?>
        <tr>
            <td>项目信息</td>
            <td>
                <span style="margin-right: 5px">
                <?= isset($formData['project_name'])?$formData['project_name']:''?>
                </span>
                <span style="color: #0000aa">编码:
                    <?= ProjectForm::codePrefix.$formData['project_code']?>
                </span>
            </td>
            <td>供应商信息</td>
            <td>
                <?php if(isset($formData['supplier_name']) || isset($formData['part_time_name'])) :?>
                    <span style="margin-right: 5px">
                    <?= !empty($formData['supplier_name'])?
                        $formData['supplier_name']
                        :
                        $formData['part_time_name']?>
                </span>
                    <span style="color: #0000aa">编码:
                        <?= !empty($formData['supplier_code'])
                            ?
                            SupplierForm::codePrefix . $formData['supplier_code']
                            :
                            PartTimeForm::codePrefix . $formData['part_time_code']?>
                </span>
                <?php endif?>
            </td>
            <td colspan="2"></td>
        </tr>
    <?php endif;?>
    <tr>
        <td>
            申请日期
        </td>
        <td>
            <?= (isset($formData['created_time'])
            && $formData['created_time'] !='0')?date("Y-m-d", $formData['created_time']):''?>
        </td>
        <td>
            申请人
        </td>
        <td>
            <?= isset($formData['created_name'])?$formData['created_name']:null?>
        </td>
        <td>
            款项类型
        </td>
        <td>
            <?= isset($formData['type'])?$paymentConfig->getAppointed('type', $formData['type']):
                $paymentConfig->getAppointed('type', PaymentConfig::PaymentForManage)?>
        </td>
    </tr>
    <tr>
        <td>款项用途</td>
        <td>
            <?php $purposeList = isset($formData['type'])?
                    $paymentConfig->getList(
                        $paymentConfig->getAppointed('type_purpose_map', $formData['type'])
                    )
                    :$paymentConfig->getList(
                    $paymentConfig->getAppointed('type_purpose_map', PaymentConfig::PaymentForManage)
                );
            ?>
            <?= isset($purposeList[$formData['purpose']])?$purposeList[$formData['purpose']]:null?>
        </td>
        <td>
            期望付款时间*
        </td>
        <td>
            <?= (isset($formData['expect_time']) && $formData['expect_time'] !='0')?date("Y-m-d", $formData['expect_time']):''?>
        </td>
        <td>发票</td>
        <td>
            <?= isset($formData['with_stamp'])?
                $paymentConfig->getAppointed('with_stamp', $formData['with_stamp']):null?>
        </td>
    </tr>
    <tr>
        <td>金额*</td>
        <td>
            <?= isset($formData['actual_money'])?$formData['actual_money']:''?>
        </td>
        <td>已支付金额</td>
        <td>
            <?=  isset($formData['paied_money'])?$formData['paied_money']:''?>
        </td>
        <td>付款人</td>
        <td>
            <?= isset($formData['paied_name'])?$formData['paied_name']:''?>
        </td>
    </tr>
    <tr>
        <td>支付时间</td>
        <td>
            <?= (isset($formData['paied_time']) && $formData['paied_time'] !='0')
                ?date("Y-m-d", $formData['paied_time']):''?>
        </td>
        <td colspan="4"></td>
    </tr>
    <tr>
        <td>发票验收状态</td>
        <td>
            <?= isset($formData['stamp_status'])
                ?$paymentConfig->getAppointed('stamp_status', $formData['stamp_status']):null?>
        </td>
        <td>已验收发票金额</td>
        <td>
            <?= isset($formData['checked_stamp_money'])?$formData['checked_stamp_money'] : null?>
        </td>
        <td>发票编号</td>
        <td>
            <?= isset($formData['stamp_series_number'])?$formData['stamp_series_number'] : null?>
        </td>
    </tr>
    <tr>
        <td>发票验收人</td>
        <td>
            <?= isset($formData['checked_stamp_name'])?$formData['checked_stamp_name']:''?>
        </td>
        <td>发票验收时间</td>
        <td>
            <?= (isset($formData['checked_stamp_time']) && $formData['checked_stamp_time'] !='0')
                ?date("Y-m-d", $formData['checked_stamp_time']):'' ?>
        </td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td>收款人*</td>
        <td>
            <?= isset($formData['receiver'])?$formData['receiver']:'' ?>
        </td>
        <td>联系人*</td>
        <td>
            <?= isset($formData['receiver_contact_name'])?$formData['receiver_contact_name']:''?>
        </td>
        <td>联系电话*</td>
        <td>
            <?= isset($formData['receiver_contact_phone'])?$formData['receiver_contact_phone']:'' ?>
        </td>

    </tr>
    <tr>
        <td>开户行</td>
        <td>
            <?= isset($formData['receiver_bank_of_deposit'])?$formData['receiver_bank_of_deposit']:''?>
        </td>
        <td>账号类型</td>
        <td>
            <?= isset($formData['receiver_account_type'])?
                $paymentConfig->getAppointed('receiver_account_type', $formData['receiver_account_type']):null?>
        </td>
        <td>收款账号*</td>
        <td>
            <?= isset($formData['receiver_account'])?$formData['receiver_account']:''?>
        </td>
    </tr>
    <tr>
        <td>付款内容</td>
        <td colspan="5">
            <?= isset($formData['description'])?$formData['description']:''?>
        </td>
    </tr>
    <tr>
        <td>备注</td>
        <td colspan="5">
            <?= isset($formData['remarks'])?$formData['remarks']:''?>
        </td>
    </tr>
    <tr>
        <td>
            一级审核人
        </td>
        <td>
            <?= isset($formData['first_assess_name'])?$formData['first_assess_name']:''?>
        </td>
        <td>
            二级审核人
        </td>
        <td>
            <?= isset($formData['second_assess_name'])?$formData['second_assess_name']:'' ?>
        </td>
        <td>
            三级审核人
        </td>
        <td>
            <?= isset($formData['third_assess_name'])?$formData['third_assess_name']:'' ?>
        </td>
    </tr>
    <tr>
        <td>
            四级审核人
        </td>
        <td>
            <?= isset($formData['fourth_assess_name'])?$formData['fourth_assess_name']:'' ?>
        </td>
        <td colspan="4"></td>
    </tr>
</table>
</div>
