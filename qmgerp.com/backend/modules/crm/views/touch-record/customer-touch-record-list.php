<?php
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
if(!isset($model)) {
    $model = new \backend\modules\crm\models\touchrecord\TouchRecordForm();
    $model->setConfig((new \backend\modules\crm\models\customer\model\CustomerConfig())->generateConfig());
}
?>
<?php Pjax::begin(); ?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th class="col-md-2">客户</th>
            <th>跟进人</th>
            <th>跟进时间</th>
            <th>跟进方式</th>
            <th>联系人</th>
            <th>结果</th>
            <th>预计签约时间</th>
            <th>预计签约金额</th>
            <th>下次跟进时间</th>
            <th class="col-md-2">跟进情况</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $touchResultList = $model->getList('touchResult');
        $touchTypeList = $model->getList('touchType');
        ?>
        <?php foreach ($touchRecordList['touchRecordList'] as $item) :?>
            <tr>
                <td><?= $item['customer_name']?></td>
                <td><?= $item['follow_name']?></td>
                <td><?= ($item['time'] != 0)?date("Y-m-d",$item['time']):''?></td>
                <td><?= isset($touchTypeList[$item['type']])?$touchTypeList[$item['type']]:''?></td>
                <td><?= $item['contact_name']?></td>
                <td><?= isset($touchResultList[$item['result']])?$touchResultList[$item['result']]:''?></td>
                <td><?= ($item['predict_contract_time'] != 0)?date("Y-m-d",$item['predict_contract_time']):''?></td>
                <td><?= $item['predict_contract_value']?></td>
                <td><?= ($item['next_touch_time'] != 0)?date("Y-m-d",$item['next_touch_time']):''?></td>
                <td><?= $item['description']?></td>
            </tr>
        <?php endforeach?>
        </tbody>
    </table>
    <?= LinkPager::widget(['pagination' => $touchRecordList['pagination']]); ?>
<?php Pjax::end(); ?>