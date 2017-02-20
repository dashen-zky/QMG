<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use yii\helpers\Url;
use yii\web\View;
use backend\modules\rbac\model\PermissionManager;
use backend\modules\crm\models\customer\record\PublicCustomer;
use backend\modules\crm\models\customer\model\CustomerConfig;
$config = new CustomerConfig();
?>
<!-- begin panel -->
<div class="panel-body">
    <?php Pjax::begin(); ?>
<?php
$JS = <<<JS
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
    
    $('.panel-body').on('click','.show-customer',function() {
        var url = $(this).attr('url');
        window.open(url); 
    });
    $('.panel-body').on('click','.drop',function() {
        var panel = $(this).parents('.panel-body');
        var modal = panel.find('.drop-modal');
        modal.find('.uuid').val($(this).attr('uuid'));
        modal.modal('show');
    });
});
JS;
$this->registerJs($JS, View::POS_END);
?>
    <?= $this->render('list-filter-form',[
        'action'=>['/crm/private-customer/list-filter'],
        'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
        'model'=>$model,
    ]);?>
        <table class="table table-striped">
            <thead>
            <tr>
                <th class="col-md-1">#</th>
                <th class="col-md-1">简称</th>
                <th class="col-md-1">级别</th>
                <th class="col-md-1">行业</th>
                <th class="col-md-1">类型</th>
                <th class="col-md-1">业务板块</th>
                <th class="col-md-1">销售</th>
                <th class="col-md-1">状态</th>
                <th class="col-md-2">最近跟进时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $industryList = $model->industryList();
            $typeList = $model->typeList();
            $fromList = $model->fromList();
            ?>
            <?php foreach ($privateCustomerList['privateCustomerList'] as $item) :?>
                <tr>
                    <td><?= \backend\modules\crm\models\customer\model\PublicCustomerForm::codePrefix.$item['code']?></td>
                    <td>
                        <a url="<?= Yii::$app->urlManager->createUrl([
                            '/crm/private-customer/edit',
                            'uuid'=>$item['uuid']
                        ])?>" class="show-customer" href="#">
                            <?php if($item['enable'] == PublicCustomer::Disable) :?>
                                <s><?= $item['name']?></s>
                            <?php else:?>
                                <?= $item['name']?>
                            <?php endif?>
                        </a>
                    </td>
                    <td><?= $config->getAppointed('level', $item['level'])?></td>
                    <td><?= isset($industryList[$item['industry']])?$industryList[$item['industry']]:''?></td>
                    <td><?= isset($typeList[$item['type']])?$typeList[$item['type']]:''?></td>
                    <td>
                        <?php if(isset($item['customer_business_map_business_id'])) :?>
                            <?php foreach($item['customer_business_map_business_id'] as $id) :?>
                                <div><?= isset($model->config['business'][$id])?
                                        $model->config['business'][$id]:''?></div>
                            <?php endforeach?>
                        <?php endif?>
                    </td>
                    <td><?= $item['sales_name']?></td>
                    <td><?= $config->getAppointed('status', $item['status'])?></td>
                    <td><?= ($item['last_touch_time'] != 0)?date("Y-m-d",$item['last_touch_time']):''?></td>
                    <td>
                        <div class="btn-group m-r-5 m-b-5">
                            <a href="javascript:;" data-toggle="dropdown" class="btn btn-success dropdown-toggle" aria-expanded="false">
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a url="<?= Yii::$app->urlManager->createUrl([
                                        '/crm/private-customer/edit',
                                        'uuid'=>$item['uuid'],
                                        'tab'=>'add-touch-record',
                                    ])?>"  class="show-customer" href="#">添加跟进记录</a></li>
                                <li><a url="<?= Yii::$app->urlManager->createUrl([
                                    '/crm/private-customer/edit',
                                    'uuid'=>$item['uuid'],
                                    'tab'=>'stamp',
                                ])?>"  class="show-customer" href="#">添加开票信息</a></li>
                                <li><a url="<?= Yii::$app->urlManager->createUrl([
                                        '/crm/private-customer/edit',
                                        'uuid'=>$item['uuid'],
                                        'tab'=>'add-project',
                                    ])?>"  class="show-customer" href="#">添加项目</a></li>
                                <li><a url="<?= Yii::$app->urlManager->createUrl([
                                        '/crm/private-customer/edit',
                                        'uuid'=>$item['uuid'],
                                        'tab'=>'add-contract',
                                    ])?>"  class="show-customer" href="#">添加合同</a></li>
                                <li><a uuid="<?= $item['uuid']?>" class="drop" href="javascript:;">放弃</a></li>
                                <?php
                                if(Yii::$app->authManager->canAccess(
                                    PermissionManager::DeleteCustomer
                                )):
                                    ?>
                                <li><a href="<?= Url::to([
                                        '/crm/private-customer/del',
                                        'uuid'=>$item['uuid']
                                    ])?>">删除</a></li>
                                <?php endif?>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach?>
            </tbody>
        </table>
        <?php
        if(isset($ser_filter) && !empty($ser_filter)) {
            $pageParams = [
                'pagination' => $privateCustomerList['pagination'],
                'ser_filter'=>$ser_filter,
            ];
        } else {
            $pageParams = [
                'pagination' => $privateCustomerList['pagination'],
            ];
        }
        ?>
        <?= MyLinkPage::widget($pageParams); ?>
    <?php Pjax::end(); ?>
    <?= $this->render('drop')?>
</div>
<!-- end panel -->