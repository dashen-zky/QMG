<?php
use yii\helpers\Json;
use backend\modules\statistic\models\SalesAnniversaryAchievementStatistic;
use backend\modules\statistic\models\SalesCustomerStatistic;
$customerStatistic = new SalesCustomerStatistic();
$saleAchievementStatic = new SalesAnniversaryAchievementStatistic();
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-customer',
    'menu-2-sales-statistic'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= !isset($tab) || $tab == 'customer'?'active':''?>"><a href="#customer-statistic" data-toggle="tab">客户统计</a></li>
        <li class="<?= isset($tab) && $tab == 'achievement'?'active':''?>"><a href="#achievement-statistic" data-toggle="tab">业绩统计</a></li>
        <li class="<?= isset($tab) && $tab == 'add-target'?'active':''?>"><a href="#add-target" data-toggle="tab">添加目标</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= !isset($tab) || $tab == 'customer'?'active in':''?>" id="customer-statistic">
            <?= $this->render('customer-statistic-list', [
                'statisticList'=>isset($customerStatisticList)?$customerStatisticList : $customerStatistic->myStatisticList(),
                'ser_filter'=>isset($customer_statistic_ser_filter)?$customer_statistic_ser_filter:'',
            ])?>
        </div>
        <div class="tab-pane fade <?= isset($tab) && $tab == 'achievement'?'active in':''?>" id="achievement-statistic">
            <?= $this->render('anniversary-achievement-list',[
                'ser_filter'=>isset($anniversary_achievement_ser_filter)?$anniversary_achievement_ser_filter:'',
                'anniversaryAchievementList'=>isset($anniversaryAchievementList)?$anniversaryAchievementList:$saleAchievementStatic->myAnniversaryAchievementList(),
            ])?>
        </div>
        <div class="tab-pane fade <?= isset($tab) && $tab == 'add-target'?'active in':''?>" id="add-target">
            <?= $this->render('anniversary-achievement-form',[
                'action'=>['/statistic/sales-statistic/add-anniversary-achievement'],
                'validate'=>true,
            ])?>
        </div>
    </div>
</div>