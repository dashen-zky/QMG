<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use yii\helpers\Url;
?>
<!-- begin panel -->
<div class="panel panel-body customer-statistic-list">
    <?php Pjax::begin(); ?>
    <?= $this->render('customer-statistic-list-filter-form',[
        'formData'=>isset($ser_filter)?unserialize($ser_filter):'',
    ])?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>姓名</th>
            <th class="col-md-2">部门</th>
            <th class="col-md-2">岗位</th>
            <th>客户总数</th>
            <th>待跟进客户数</th>
            <th>跟进中客户数</th>
            <th>合作中客户数</th>
            <th>KA客户数</th>
            <th>最近7天跟进数</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($statisticList['list'] as $item) :?>
            <tr>
                <td><a url="<?= Url::to([
                        '/crm/private-customer/index',
                        'ser_filter'=>serialize(['sales_name'=>$item['sales_name']])
                    ])?>" href="#" class="show-new-tab"><?= $item['sales_name']?></a></td>
                <td><?= $item['department_name']?></td>
                <td><?= $item['position_name']?></td>
                <td><?= $item['customer_total']?></td>
                <td><?= $item['number_of_waiting_touch']?></td>
                <td><?= $item['number_of_touching']?></td>
                <td><?= $item['number_of_cooperating']?></td>
                <td><?= $item['number_of_ka']?></td>
                <td><a href="#" class="show-new-tab" url="<?= Url::to([
                        '/crm/private-customer/index',
                        'tab'=>'touch-record-list',
                        'touch_record_list_ser_filter'=>serialize([
                            'follow_name'=>$item['sales_name'],
                            'min_time'=>date('Y-m-d', time()-604800),
                            'max_time'=>date('Y-m-d', time())
                        ])
                    ])?>"><?= $item['number_of_last_7_days_touch_records']?></a></td>
            </tr>
        <?php endforeach?>
        </tbody>
    </table>
    <?php
    if(isset($ser_filter) && !empty($ser_filter)) {
        $pageParams = [
            'pagination' => $statisticList['pagination'],
            'ser_filter'=>$ser_filter,
        ];
    } else {
        $pageParams = [
            'pagination' => $statisticList['pagination'],
        ];
    }
    ?>
    <?= MyLinkPage::widget($pageParams); ?>
    <?php Pjax::end(); ?>
</div>
<!-- end panel -->