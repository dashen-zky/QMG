<?php
use backend\models\AjaxLinkPage;
use backend\modules\fin\stamp\models\StampConfig;
use yii\helpers\Url;
?>
<table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>发票类型</th>
            <th>状态</th>
            <th>金额</th>
            <th>开票日期</th>
            <th>验收日期</th>
            <th>开票方</th>
            <th>收票方</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php $stampConfig = new StampConfig();?>
        <?php foreach ($stampList['list'] as $item) :?>
            <tr>
                <td>
                    <?php if($item['enable'] == StampConfig::Enable) :?>
                        <?= $item['series_number']?>
                    <?php else:?>
                        <s><?= $item['series_number']?></s>
                    <?php endif;?>
                </td>
                <td>
                    <?= $stampConfig->getAppointed('service_type', $item['service_type'])?>
                </td>
                <td>
                    <?= $stampConfig->getAppointed('export_status', $item['status'])?>
                </td>
                <td>
                    <div>
                        金额:<?= $item['money']?>
                    </div>
                    <div>
                        税前金额:<?= $item['before_tax_money']?>
                    </div>
                    <div>
                        税费:<?= $item['tax_money']?>
                    </div>
                </td>
                <td>
                    <?= (isset($item['made_time'])
                        && $item['made_time'] != 0)
                        ?date('Y-m-d', $item['made_time']):''?>
                </td>
                <td>
                    <?= (isset($item['checked_time'])
                        && $item['checked_time'] != 0)
                        ?date('Y-m-d', $item['checked_time']):''?>
                </td>
                <td>
                    <div>
                        开票方:<?= $item['provider']?>
                    </div>
                    <div>
                        税号:<?= $item['provider_tax_code']?>
                    </div>
                </td>
                <td>
                    <div>
                        收票方:<?= $item['receiver']?>
                    </div>
                    <div>
                        税号:<?= $item['receiver_tax_code']?>
                    </div>
                </td>
                <td>
                    <div>
                        <a url="<?= Url::to([
                            '/stamp/export-stamp/show',
                            'uuid'=>$item['uuid'],
                        ])?>" class="show-stamp" href="javascript:;">查看</a>
                    </div>
                <?php if($item['enable'] == StampConfig::Enable) :?>
                    <div>
                        <a url="<?= Url::to([
                            '/stamp/export-stamp/disable',
                            'uuid'=>$item['uuid'],
                        ])?>" href="#" class="disable">作废</a>
                    </div>
                <?php endif;?>
                </td>
            </tr>
        <?php endforeach?>
        </tbody>
    </table>
<?php
if(isset($ser_filter) && !empty($ser_filter)) {
    $pageParams = [
        'pagination' => $stampList['pagination'],
        'ser_filter'=>$ser_filter,
    ];
} else {
    $pageParams = [
        'pagination' => $stampList['pagination'],
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
});
Js;
$this->registerJs($Js, \yii\web\View::POS_END);
?>