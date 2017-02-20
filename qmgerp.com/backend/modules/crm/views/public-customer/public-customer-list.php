<?php
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\web\View;
use backend\models\MyLinkPage;
use backend\modules\rbac\model\PermissionManager;
use backend\modules\crm\models\customer\record\PublicCustomer;
use backend\modules\crm\models\customer\model\CustomerConfig;
$config = new CustomerConfig();
$isSales = Yii::$app->authManager->checkAccess(
    Yii::$app->user->getIdentity()->getId(),
    PermissionManager::MyCustomerMenu
);
?>
<!-- begin panel -->
<div class="panel-body">
<?php Pjax::begin(); ?>
<?= $this->render('list-filter-form',[
    'action'=>['/crm/public-customer/list-filter'],
    'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
    'model'=>$model,
]);?>
<div class="panel-body customer-list">
    <table class="table table-striped">
        <thead>
        <tr>
            <th class="col-md-1">#</th>
            <th class="col-md-1">简称</th>
            <th class="col-md-1">级别</th>
            <th class="col-md-1">业务板块</th>
            <th class="col-md-1">状态</th>
            <th class="col-md-1">联系人</th>
            <th class="col-md-1">类别</th>
            <th class="col-md-1">最后跟进时间</th>
            <th class="col-md-1">销售</th>
            <th class="col-md-1">行业</th>
            <?php if ($isSales) :?>
            <th class="col-md-1">操作</th>
            <?php endif;?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($publicCustomerList['publicCustomerList'] as $item) :?>
            <tr>
                <td><?= \backend\modules\crm\models\customer\model\PublicCustomerForm::codePrefix.$item['code']?></td>
                <td>
                    <a  url="<?=Yii::$app->urlManager->createUrl([
                        '/crm/public-customer/edit',
                        'uuid'=>$item['uuid']
                    ])?>" href="#" class="show-customer">
                        <?php if($item['enable'] == PublicCustomer::Disable) :?>
                            <s><?= $item['name']?></s>
                        <?php else:?>
                            <?= $item['name']?>
                        <?php endif?>
                    </a>
                </td>
                <td><?= $config->getAppointed('level', $item['customer_advance_level'])?></td>
                <td>
                    <?php if(isset($item['customer_business_business_id'])) :?>
                    <?php foreach($item['customer_business_business_id'] as $id) :?>
                            <div><?= isset($model->config['business'][$id])?
                                    $model->config['business'][$id]:''?></div>
                    <?php endforeach?>
                    <?php endif?>
                </td>
                <td><?= $config->getAppointed('status', $item['status'])?></td>
                <td>
                    <?php if(isset($item['contact'])) :?>
                        <?php foreach($item['contact'] as $uuid => $contact) :?>
                            <div>
                                <a href="javascript:;" class="editContact"
                                   name="<?= ($isSales || Yii::$app->authManager->isAuthor(
                                           Yii::$app->user->getIdentity()->getId(),
                                           $item['created_uuid']
                                       ))?Url::to([
                                       '/crm/public-customer-contact/edit',
                                       'uuid'=>$uuid,
                                       'object_uuid'=>$item['uuid'],
                                   ]) : '#'?>"><?= $contact?></a>
                            </div>
                        <?php endforeach?>
                    <?php endif?>
                </td>
                <td><?= $model->getAppointed('type', $item['type'])?></td>
                <td><?= empty($item['last_touch_time']) ? null : date("Y-m-d",$item['last_touch_time'])?></td>
                <td><?= $item['sales_name']?></td>
                <td><?= $model->getAppointed('industry', $item['industry'])?></td>
                <?php if($isSales) :?>
                <td>
                    <div class="btn-group m-r-5 m-b-5">
                        <a href="javascript:;" data-toggle="dropdown" class="btn btn-success dropdown-toggle" aria-expanded="false">
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li><?php if($item['public_tag'] == PublicCustomer::publicTag
                                    && $item['enable'] == PublicCustomer::Enable) :?>
                                        <a href="<?= Yii::$app->urlManager->createUrl([
                                            '/crm/private-customer/obtain',
                                            'uuid'=>$item['uuid']
                                        ])?>">认领</a>
                                <?php endif?></li>
                            <li><?php
                                if(Yii::$app->authManager->canAccess(
                                    PermissionManager::DeleteCustomer
                                )):
                                    ?>
                                        <a href="<?= Url::to([
                                            '/crm/public-customer/del',
                                            'uuid'=>$item['uuid']
                                        ])?>">删除</a>
                                <?php endif?></li>
                        </ul>
                    </div>
                </td>
                <?php endif;?>
            </tr>
        <?php endforeach?>
        </tbody>
    </table>
    <?php
    if(isset($ser_filter) && !empty($ser_filter)) {
        $pageParams = [
            'pagination' => $publicCustomerList['pagination'],
            'ser_filter'=>$ser_filter,
        ];
    } else {
        $pageParams = [
            'pagination' => $publicCustomerList['pagination'],
        ];
    }
    ?>
    <?= MyLinkPage::widget($pageParams); ?>
</div>
<?php
$JS = <<<JS
$(function(){
    $('.customer-list').on('click','.editContact',function() {
        var url = $(this).attr('name');
        if(url === '#') {
            return ;
        }
         $.get(
            url,
            function(data,status) {
                if(status === 'success') {
                    var formData = JSON.parse(data);
                    $.each(formData, function(key, value) {
                        var element = $(".customer-contact .edit-contact [name=" + "'ContactForm[" +key+ "]'" +"]");
                        element.val(value);
                    });
                    $('.customer-contact .edit-contact form .enableEdit').attr("disabled",true);
                    $(".customer-contact .edit-contact").modal('show');
                }
            }
        );
    });

    $('.customer-list').on('click','.show-customer',function() {
        var url = $(this).attr('url');
        window.open(url); 
    });

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
})
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>
<?php Pjax::end(); ?>
</div>
<!-- end panel -->
<div class="customer-contact">
    <?= $this->render('/contact-tab/edit',[
        'action'=>['/crm/public-customer-contact/update'],
        'formData'=>[],
        'model'=>$contactModel,
        'show'=>false,
    ])?>
</div>
