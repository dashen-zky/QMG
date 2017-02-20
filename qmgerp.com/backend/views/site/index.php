<style type="text/css">
    .home-page-nav li {
        color: #fff;
        background: #2d353c;
        border-color: #2d353c;
        width: 31%;
        margin: 10px;
        border-radius: 10px;
    }

    .home-page-nav li a div{
        height: 15px;
    }
</style>
<?php
use yii\helpers\Url;
use yii\helpers\Json;
use backend\modules\rbac\model\PermissionManager;
use backend\modules\daily\models\regulation\RegulationConfig;
?>
<div class="panel col-md-9" data-sortable-id="index-4" style="margin-top: 10px">
    <div class="panel-heading">
        <h4 class="panel-title">首页导航</h4>
    </div>
    <div class="panel-body">
        <ul class="home-page-nav registered-users-list clearfix" >
            <li>
                <a href="<?= Url::to([
                    '/daily/transaction/index',
                    'menu'=>Json::encode([
                        'menu-1-com',
                        'menu-2-transaction'
                    ]),
                ])?>" class="btn btn-inverse">
                    <div>
                        待完成事项
                    </div>
                </a>
                <a class="btn  btn-inverse" href="#">
                    <div>
                    </div>
                </a>
                <a class="btn  btn-inverse" href="<?= Url::to([
                    '/daily/transaction/index',
                    'menu'=>Json::encode([
                        'menu-1-com',
                        'menu-2-transaction'
                    ]),
                    'tab'=>'add-transaction',
                ])?>">
                    <div>
                        添加事项
                    </div>
                </a>
            </li>
            <li>
                <a href="<?= Url::to([
                    '/daily/week-report/index',
                    'menu'=>Json::encode([
                        'menu-1-com',
                        'menu-2-week-report'
                    ]),
                ])?>" class="btn  btn-inverse">
                    <div>
                        周报列表
                    </div>
                </a>
                <a class="btn  btn-inverse" href="#">
                    <div>
                    </div>
                </a>
                <a class="btn  btn-inverse" href="<?= Url::to([
                    '/daily/week-report/index',
                    'menu'=>Json::encode([
                        'menu-1-com',
                        'menu-2-week-report'
                    ]),
                    'tab'=>'add'
                ])?>">
                    <div>
                        写周报
                    </div>
                </a>
            </li>
            <?php if(Yii::$app->authManager->canAccess(PermissionManager::MyCustomerMenu)) : ?>
            <li>
                <a href="<?= Url::to([
                    '/crm/private-customer/index',
                    'menu'=>Json::encode([
                        'menu-1-customer',
                        'menu-2-private-customer'
                    ]),
                ])?>" class="btn  btn-inverse">
                    <div>
                        我的客户
                    </div>
                </a>
                <a href="<?= Url::to([
                    '/crm/private-customer/index',
                    'menu'=>Json::encode([
                        'menu-1-customer',
                        'menu-2-private-customer'
                    ]),
                    'tab'=>'add-customer',
                ])?>" class="btn  btn-inverse">
                    <div>
                        添加客户
                    </div>
                </a>
                <a class="btn  btn-inverse" href="<?= Url::to([
                    '/crm/private-customer/index',
                    'menu'=>Json::encode([
                        'menu-1-customer',
                        'menu-2-private-customer'
                    ]),
                    'tab'=>'touch-record-list',
                ])?>">
                    <div>
                        跟进记录
                    </div>
                </a>
            </li>
            <?php endif;?>
            <?php
            if(Yii::$app->authManager->checkAccess(
                Yii::$app->user->getIdentity()->getId(),
                PermissionManager::ProjectMenu
            )) :?>
            <li>
                <a href="<?= Url::to([
                    '/crm/project/index',
                    'menu'=>Json::encode([
                        'menu-1-project',
                        'menu-2-project'
                    ]),
                ])?>" class="btn  btn-inverse">
                    <div>
                        我的项目
                    </div>
                </a>
                <a class="btn  btn-inverse" href="<?= Url::to([
                    '/crm/project/index',
                    'menu'=>Json::encode([
                        'menu-1-project',
                        'menu-2-project'
                    ]),
                    'tab'=>'contract-list'
                ])?>">
                    <div>
                        我的合同
                    </div>
                </a>
                <a class="btn  btn-inverse" href="<?= Url::to([
                    '/crm/project/index',
                    'menu'=>Json::encode([
                        'menu-1-project',
                        'menu-2-project'
                    ]),
                    'tab'=>'stamp-list'
                ])?>">
                    <div>
                        我的开票
                    </div>
                </a>
            </li>
            <li>
                <a href="<?= Url::to([
                    '/crm/project-apply-payment/index',
                    'menu'=>Json::encode([
                        'menu-1-project',
                        'menu-2-apply-payment'
                    ]),
                ])?>" class="btn  btn-inverse">
                    <div>
                        项目类申请付款
                    </div>
                </a>
                <a class="btn  btn-inverse" href="#">
                    <div>
                    </div>
                </a>
                <a class="btn  btn-inverse" href="<?= Url::to([
                    '/crm/project-apply-check-stamp/index',
                    'menu'=>Json::encode([
                        'menu-1-project',
                        'menu-2-apply-check-stamp'
                    ]),
                ])?>">
                    <div>
                        项目类提交发票
                    </div>
                </a>
            </li>
            <?php endif;?>
            <li>
                <a href="<?= Url::to([
                    '/daily/apply-payment/index',
                    'menu'=>Json::encode([
                        'menu-1-com',
                        'menu-2-apply-payment'
                    ]),
                    'tab'=>'add-payment'
                ])?>" class="btn  btn-inverse">
                    <div>
                        日常类申请付款
                    </div>
                </a>
                <a class="btn  btn-inverse" href="#">
                    <div>
                    </div>
                </a>
                <a class="btn  btn-inverse" href="<?= Url::to([
                    '/daily/apply-check-stamp/index',
                    'menu'=>Json::encode([
                        'menu-1-com',
                        'menu-2-apply-check-stamp'
                    ]),
                ])?>">
                    <div>
                        日常类提交发票
                    </div>
                </a>
            </li>
            <?php
            if(Yii::$app->authManager->checkAccess(
                Yii::$app->user->getIdentity()->getId(),
                PermissionManager::SupplierMenu
            )) :?>
            <li>
                <a class="btn  btn-inverse" href="<?= Url::to([
                    '/crm/supplier/index',
                    'menu'=>Json::encode([
                        'menu-1-supplier',
                        'menu-2-supplier'
                    ]),
                ])?>">
                    <div>
                        供应商
                    </div>
                </a>
                <a href="<?= Url::to([
                    '/crm/supplier-apply-payment/index',
                    'menu'=>Json::encode([
                        'menu-1-supplier',
                        'menu-2-apply-payment',
                    ]),
                ])?>" class="btn  btn-inverse">
                    <div>
                        媒介类申请付款
                    </div>
                </a>
                <a class="btn  btn-inverse" href="<?= Url::to([
                    '/crm/supplier-apply-check-stamp/index',
                    'menu'=>Json::encode([
                        'menu-1-supplier',
                        'menu-2-apply-check-stamp'
                    ]),
                ])?>">
                    <div>
                        媒介类提交发票
                    </div>
                </a>
            </li>
            <?php endif;?>
            <li>
                <a href="<?= Url::to([
                    '/hr/ask-for-leave/index',
                    'menu'=>Json::encode([
                        'menu-1-com',
                        'menu-2-ask-for-leave'
                    ]),
                ])?>" class="btn  btn-inverse">
                    <div>
                        请假列表
                    </div>
                </a>
                <a class="btn  btn-inverse" href="#">
                    <div>
                    </div>
                </a>
                <a class="btn  btn-inverse" href="<?= Url::to([
                    '/hr/ask-for-leave/index',
                    'menu'=>Json::encode([
                        'menu-1-com',
                        'menu-2-ask-for-leave'
                    ]),
                    'tab'=>'add'
                ])?>">
                    <div>
                        我要请假
                    </div>
                </a>
            </li>
            <li>
                <a href="<?= Url::to([
                    '/daily/regulation/index',
                    'menu'=>Json::encode([
                        'menu-1-com',
                        'menu-2-regulation'
                    ]),
                    'ser_filter'=>serialize(['type'=>RegulationConfig::TypeRegulation]),
                ])?>" class="btn  btn-inverse">
                    <div>
                        公司制度
                    </div>
                </a>
                <a class="btn  btn-inverse" href="#">
                    <div>
                    </div>
                </a>
                <a class="btn  btn-inverse" href="<?= Url::to([
                    '/daily/regulation/index',
                    'menu'=>Json::encode([
                        'menu-1-com',
                        'menu-2-regulation'
                    ]),
                    'ser_filter'=>serialize(['type'=>RegulationConfig::TypeExecuteRule]),
                ])?>">
                    <div>
                        执行规范
                    </div>
                </a>
            </li>
            <li>
                <a href="#" class="btn  btn-inverse">
                    <div>
                        通讯录
                    </div>
                </a>
                <a class="btn  btn-inverse" href="#">
                    <div>
                    </div>
                </a>
                <a class="btn  btn-inverse" href="#">
                    <div>
                        组织结构
                    </div>
                </a>
            </li>
            <?php
            if(Yii::$app->authManager->checkAccess(
                Yii::$app->user->getIdentity()->getId(),
                PermissionManager::FinancialMenu
            )) :?>
            <li>
                <a href="#" class="btn  btn-inverse">
                    <div>
                        预约面试
                    </div>
                </a>
                <a class="btn  btn-inverse" href="#">
                    <div>
                    </div>
                </a>
                <a class="btn  btn-inverse" href="#">
                    <div>
                        员工转正
                    </div>
                </a>
            </li>
            <?php endif;?>
            <?php
            if(Yii::$app->authManager->checkAccess(
                Yii::$app->user->getIdentity()->getId(),
                PermissionManager::FinancialMenu
            )) :?>
            <li>
                <a href="<?= Url::to([
                    '/accountReceivable/account-receivable/index',
                    'menu'=>Json::encode([
                        'menu-1-fin',
                        'menu-2-account-receivable',
                        'menu-3-account-receivable'
                    ]),
                ])?>" class="btn  btn-inverse">
                    <div>
                        应收款
                    </div>
                </a>
                <a class="btn  btn-inverse" href="#">
                    <div>
                    </div>
                </a>
                <a class="btn  btn-inverse" href="<?= Url::to([
                    '/payment/payment/index',
                    'menu'=>Json::encode([
                        'menu-1-fin',
                        'menu-2-payment',
                        'menu-3-payment'
                    ]),
                ])?>">
                    <div>
                        应付款
                    </div>
                </a>
            </li>
            <?php endif;?>
        </ul>
    </div>
</div>
<div class="panel col-md-3" style="margin-top: 10px">
    <div class="panel-heading">
        <h4 class="panel-title">公告</h4>
    </div>
</div>