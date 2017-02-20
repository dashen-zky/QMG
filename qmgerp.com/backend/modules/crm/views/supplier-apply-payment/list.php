<?php
use backend\modules\fin\payment\models\PaymentConfig;
use yii\helpers\Url;
use backend\modules\crm\models\project\model\ProjectForm;
use backend\modules\crm\models\supplier\model\SupplierForm;
use backend\modules\crm\models\part_time\model\PartTimeForm;
use backend\models\AjaxLinkPage;
?>
<table class="table table-striped">
    <thead>
    <tr>
        <th>选择</th>
        <th>#</th>
        <th>项目信息</th>
        <th>供应商/兼职信息</th>
        <th>申请日期</th>
        <th>款项类型</th>
        <th>款项用途</th>
        <th>付款金额</th>
        <th>状态</th>
        <th>期望日期</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php $paymentConfig = new PaymentConfig();?>
    <?php foreach ($paymentList['list'] as $item) :?>
        <tr>
            <td>
                <?php if($item['status'] == PaymentConfig::StatusSave) :?>
                    <input type="checkbox" value="<?= $item['uuid']?>">
                <?php endif?>
            </td>
            <td><?= PaymentConfig::CodePrefix . $item['code']?></td>
            <td>
                <div><?= $item['project_name']?></div>
                <div><?= ProjectForm::codePrefix.$item['project_code']?></div>
            </td>
            <td>
                <?php if(!empty($item['supplier_name'])) :?>
                    <div>供应商</div>
                    <div><?= $item['supplier_name']?></div>
                    <div><?= SupplierForm::codePrefix.$item['supplier_code']?></div>
                <?php else:?>
                    <div>兼职</div>
                    <div><?= $item['part_time_name']?></div>
                    <div><?= PartTimeForm::codePrefix.$item['part_time_code']?></div>
                <?php endif?>
            </td>
            <td>
                <?= (isset($item['created_time'])
                    && $item['created_time'] != 0)
                    ?date('Y-m-d', $item['created_time']):''?>
            </td>
            <td>
                <?= $paymentConfig->getAppointed('type', $item['type'])?>
            </td>
            <td>
                <?= $paymentConfig->getAppointed(
                    $paymentConfig->getAppointed('type_purpose_map', $item['type']), $item['purpose']
                )?>
            </td>
            <td><?= $item['actual_money']?></td>
            <td><?= $paymentConfig->getAppointed('status', $item['status'])?></td>
            <td>
                <?= (isset($item['expect_time'])
                    && $item['expect_time'] != 0)
                    ?date('Y-m-d', $item['expect_time']):''?>
            </td>
            <td>
                <div>
                    <a name="<?= Url::to([
                        '/crm/supplier-apply-payment/show',
                        'uuid'=>$item['uuid'],
                    ])?>" class="show-apply-payment" href="javascript:;">查看</a>
                </div>
                <?php if(in_array($item['status'], [
                    PaymentConfig::StatusSave,
                ])) :?>
                    <div>
                        <a href="<?= Url::to([
                            '/crm/supplier-apply-payment/edit',
                            'uuid'=>$item['uuid'],
                        ])?>">编辑</a>
                    </div>
                <?php endif?>
                <?php if($item['status'] < PaymentConfig::StatusWaitFirstAssess) :?>
                    <div>
                        <a href="<?= Url::to([
                            '/crm/supplier-apply-payment/single-apply',
                            'uuid'=>$item['uuid'],
                        ])?>">申请付款</a>
                    </div>
                <?php endif?>
            </td>
        </tr>
    <?php endforeach?>
    </tbody>
</table>
<?php
if(isset($ser_filter) && !empty($ser_filter)) {
    $pageParams = [
        'pagination' => $paymentList['pagination'],
        'ser_filter'=>$ser_filter,
    ];
} else {
    $pageParams = [
        'pagination' => $paymentList['pagination'],
    ];
}
?>
<?= AjaxLinkPage::widget($pageParams); ?>
<?php
$Js = <<<Js
$(function() {
    $('.list .pagination').on('click', 'li', function() {
            pagination($(this));
    });

    function pagination(self) {
        var url = self.attr('url');
        $.get(
        url,
        function(data, status) {
            if(status !== 'success') {
                return ;
            }
            var container = self.parents('.list');
            container.html(data);
            $('.pagination').on('click', 'li', function() {
                pagination($(this));
            });
        });
    }
});
Js;
$this->registerJs($Js, \yii\web\View::POS_END);
?>
