<!-- begin panel -->
<?php
use backend\modules\fin\stamp\models\StampConfig;
use backend\models\AjaxLinkPage;
use yii\helpers\Json;
$stampConfig = new StampConfig();
?>
<input hidden value='<?= isset($checked)?Json::encode($checked):""?>' class="checked-uuid">
<div class="panel-body">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>选择</th>
            <th>#</th>
            <th>发票类型</th>
            <th>状态</th>
            <th>金额</th>
            <th>开票日期</th>
            <th>验收日期</th>
            <th>开票方</th>
            <th>收票方</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($stampList['list'] as $item) :?>
            <tr>
                <td><input type="checkbox"
                        <?= !empty($checked) && in_array($item['uuid'], $checked)?'checked':''?>
                           class="stamp-uuid"
                           name="uuid"
                           id="<?= $item['uuid']?>"
                           value="<?= $item['uuid']?>">
                </td>
                <td class="stamp-name">
                        <?= $item['series_number']?>
                </td>
                <td>
                    <?= $stampConfig->getAppointed('service_type', $item['service_type'])?>
                </td>
                <td>
                    <?= $stampConfig->getAppointed('import_status', $item['status'])?>
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
</div>