<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use backend\modules\fin\payment\models\PaymentConfig;
use yii\helpers\Url;
?>
<div class="payment-list-panel panel-body panel">
<?php Pjax::begin(); ?>
<?php
$Js = <<<JS
$(function() {
    $(".datetimepicker").datetimepicker({
        lang:"ch",           //语言选择中文
        format:"Y-m-d",      //格式化日期H:i
        i18n:{
          // 以中文显示月份
          de:{
            months:["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月",],
            // 以中文显示每周（必须按此顺序，否则日期出错）
            dayOfWeek:["日","一","二","三","四","五","六"]
          }
        }
        // 显示成年月日，时间--
    });
    
    // 查看付款申请详情
    $('.payment-list-panel').on('click', '.show-apply-payment', function() {
        var url = $(this).attr('name');
        var self = $(this);
        $.get(
        url,
        function(data,status) {
            if(status === 'success') {
                var panel = self.parents('.panel-body');
                var modal = panel.find('.apply-payment-show');
                modal.find('.modal-body').html(data);
                modal.modal('show');
            }
        });
    });
    // 提交申请
    $('.payment-list-panel').on('click', '.apply-payment', function() {
        var panel = $(this).parents('.panel-body');
        var checked = new Array();
        panel.find('input[type=checkbox]:checked').each(function(i) {
            checked[i] = $(this).val();
        });
        checked = JSON.stringify(checked);
        var url = $(this).attr('name') + "&uuids=" + checked;
        window.location.href = url;
    });
});
JS;
$this->registerJs($Js, \yii\web\View::POS_END);
?>
<?= $this->render('list-filter-form',[
    'action'=>['/daily/apply-payment/list-filter'],
    'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
]);?>
    <div class="panel-body payment-list">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>选择</th>
                <th>#</th>
                <th>申请日期</th>
                <th>款项类型</th>
                <th>款项用途</th>
                <th class="col-md-2">付款内容</th>
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
                    <td><?= $item['description']?></td>
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
                                '/daily/apply-payment/show',
                                'uuid'=>$item['uuid'],
                            ])?>" class="show-apply-payment" href="javascript:;">查看</a>
                        </div>
                        <?php if(in_array($item['status'], [
                            PaymentConfig::StatusSave,
//                            PaymentConfig::StatusFirstAssessRefuse,
//                            PaymentConfig::StatusSecondAssessRefuse,
//                            PaymentConfig::StatusThirdAssessRefuse
                        ])) :?>
                        <div>
                            <a href="<?= Url::to([
                                '/daily/apply-payment/edit',
                                'uuid'=>$item['uuid'],
                            ])?>">编辑</a>
                        </div>
                        <?php endif?>
                        <?php if($item['status'] < PaymentConfig::StatusWaitFirstAssess) :?>
                        <div>
                            <a href="<?= Url::to([
                                '/daily/apply-payment/single-apply',
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
        <?= MyLinkPage::widget($pageParams); ?>
    </div>
<?php Pjax::end(); ?>
<span>
    <button class="btn btn-primary apply-payment" name="<?= Url::to([
        '/daily/apply-payment/multi-apply',
    ])?>">申请付款</button>
</span>
<?= $this->render('show')?>
</div>
