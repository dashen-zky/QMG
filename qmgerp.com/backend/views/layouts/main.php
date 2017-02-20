<?php $this->beginContent('@app/views/layouts/base.php');?>
<?php
use yii\helpers\Url;
use yii\helpers\Json;
use backend\modules\rbac\model\PermissionManager;
use backend\modules\rbac\model\RoleManager;
?>

<?php

/* @var $this yii\web\View */
$this->title = 'erp';
?>
<?php
$JS = <<<JS
$(function() {
    (function ($) {
        $.getUrlParam = function (name) {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
            var r = window.location.search.substr(1).match(reg);
            if (r != null) return unescape(r[2]); return null;
        }
    })(jQuery);
    var menu = JSON.parse($.getUrlParam('menu'));
    if(!menu && typeof(menu)!="undefined" && menu!=0) {
        menu = JSON.parse($('input.menu').val());
    }
    for(var i = 0, len = menu.length; i < len; i++) {
        var selector = '.nav';
        for(var j = 0; j <= i; j++) {
            selector += " ." + menu[j];
        }
        $(selector).addClass('active');
    }

    $("form").on('click','.editForm',function() {
        var form = $(this).parents('form');
        form.find('.enableEdit').attr("disabled",false);
        form.find('.displayBlockWhileEdit').css('display','block');
    });
    
    $('.panel').on('click','.show-new-tab',function() {
        var url = $(this).attr('url');
        window.open(url);
    })
})
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>
<style type="text/css">
    .form-group {
        /* margin-bottom: 15px; */
        margin-bottom: 0px;
    }

    .page-header-fixed {
        padding-top: 32px;
    }

    .page-sidebar-minified .sidebar-bg {
        width: 100px;
    }
    .page-sidebar-minified .sidebar {
        width: 100px;
        position: absolute;
    }
    .sidebar, .sidebar-bg {
        background: #2d353c;
        left: 0;
        width: 170px;
        top: 0;
        bottom: 0;
    }
    
    .page-sidebar-minified .content {
        margin-left: 100px;
        margin-top: 10px;
    }
    
    .content {
        margin-left: 170px;
        padding: 20px 25px;
    }

    .nav-tabs {
        margin-top: 20px;
    }
    
    .modal-footer {
         border-top: 0px;
    }
    
    .modal-header {
         border-bottom: 0px;
    }
    .modal-dialog {
        width: 90%;
        margin: 30px auto;
    }

    .modal {
        width: 95%;
        margin: 30px auto;
        overflow:scroll;
    }

    ul.float-left li {
        float: left;
        list-style: none;
    }

    a:hover {
        text-decoration: none;
        color: #0000aa;
    }

    span.tag-close {
        margin: -5px 5px;
        font-size: 25px;
        float: right;
    }

    span.tag-content {
        font-size: 15px;
        margin: 3px 8px;
    }

    div.home-page-nav {
        height: 50px;
    }

    div.tag {
        border: 1px solid #ccd0d4;
        border-radius:15px;
        height: 30px;
        margin-right: 15px;
    }

    /*必须导入的css样式*/
    .zUIpanelScrollBox,.zUIpanelScrollBar{
        width:10px;
        top:4px;
        right:2px;
        border-radius:5px;
    }
    .zUIpanelScrollBox{
        background:black;opacity:0.1;
        filter:alpha(opacity=10);
    }
    .zUIpanelScrollBar{
        background:#fff;opacity:0.8;
        filter:alpha(opacity=80);
    }
</style>
    <!-- begin #page-container -->
    <div id="page-container" class="fade page-sidebar-fixed page-header-fixed in page-sidebar-minified">
        <!-- begin #header -->
        <div id="header" class="header navbar navbar-default navbar-fixed-top">
            <!-- begin container-fluid -->
            <div class="container-fluid">
                <!-- begin mobile sidebar expand / collapse button -->
                <div class="navbar-header">
                    <a href="<?= Url::to(['/site/index'])?>" class="navbar-brand"><span class="navbar-logo"></span> 谦玛 ERP</a>
                    <button type="button" class="navbar-toggle" data-click="sidebar-toggled">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <!-- end mobile sidebar expand / collapse button -->

                <!-- begin header navigation right -->
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="javascript:;" data-toggle="dropdown" class="dropdown-toggle f-s-14">
                            <i class="fa fa-bell-o"></i>
                            <span class="label">5</span>
                        </a>
                        <ul class="dropdown-menu media-list pull-right animated fadeInDown">
                            <li class="dropdown-header">Notifications (5)</li>
                            <li class="media">
                                <a href="javascript:;">
                                    <div class="media-left"><i class="fa fa-bug media-object bg-red"></i></div>
                                    <div class="media-body">
                                        <h6 class="media-heading">Server Error Reports</h6>
                                        <div class="text-muted f-s-11">3 minutes ago</div>
                                    </div>
                                </a>
                            </li>
                            <li class="media">
                                <a href="javascript:;">
                                    <div class="media-left"><img src="<?= Yii::getAlias('@web')?>/img/user-1.jpg" class="media-object" alt="" /></div>
                                    <div class="media-body">
                                        <h6 class="media-heading">John Smith</h6>
                                        <p>Quisque pulvinar tellus sit amet sem scelerisque tincidunt.</p>
                                        <div class="text-muted f-s-11">25 minutes ago</div>
                                    </div>
                                </a>
                            </li>
                            <li class="media">
                                <a href="javascript:;">
                                    <div class="media-left"><img src="<?= Yii::getAlias('@web')?>/img/user-2.jpg" class="media-object" alt="" /></div>
                                    <div class="media-body">
                                        <h6 class="media-heading">Olivia</h6>
                                        <p>Quisque pulvinar tellus sit amet sem scelerisque tincidunt.</p>
                                        <div class="text-muted f-s-11">35 minutes ago</div>
                                    </div>
                                </a>
                            </li>
                            <li class="media">
                                <a href="javascript:;">
                                    <div class="media-left"><i class="fa fa-plus media-object bg-green"></i></div>
                                    <div class="media-body">
                                        <h6 class="media-heading"> New User Registered</h6>
                                        <div class="text-muted f-s-11">1 hour ago</div>
                                    </div>
                                </a>
                            </li>
                            <li class="media">
                                <a href="javascript:;">
                                    <div class="media-left"><i class="fa fa-envelope media-object bg-blue"></i></div>
                                    <div class="media-body">
                                        <h6 class="media-heading"> New Email From John</h6>
                                        <div class="text-muted f-s-11">2 hour ago</div>
                                    </div>
                                </a>
                            </li>
                            <li class="dropdown-footer text-center">
                                <a href="javascript:;">View more</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown navbar-user">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="<?= Yii::getAlias('@web')?>/img/user-13.jpg" alt="" />
                            <span class="hidden-xs"><?= Yii::$app->user->getIdentity()->getEmployeeName()?></span> <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu animated fadeInLeft">
                            <li class="arrow"></li>
                            <li><a href="javascript:;">编辑</a></li>
                            <li><a href="javascript:;"><span class="badge badge-danger pull-right">2</span>消息</a></li>
                            <li><a href="<?= Url::to(['/account/modify-password'])?>">密码管理</a></li>
                            <li><a href="javascript:;">设置</a></li>
                            <li class="divider"></li>
                            <li><a href="<?= Url::to(['/site/logout'])?>">退出</a></li>
                        </ul>
                    </li>
                </ul>
                <!-- end header navigation right -->
            </div>
            <!-- end container-fluid -->
        </div>
        <!-- end #header -->
        <!-- begin #sidebar -->
        <div id="sidebar" class="sidebar">
            <!-- begin sidebar scrollbar -->
            <div data-scrollbar="true" data-height="100%">
                <!-- begin sidebar user -->
                <ul class="nav">
                    <li class="nav-profile">
                        <div class="image">
                            <a href="javascript:;"><img src="<?= Yii::getAlias('@web')?>/img/user-13.jpg" alt="" /></a>
                        </div>
                        <div class="info">
                            <?= Yii::$app->user->getIdentity()->getEmployeeName()?>
                            <small>Front end developer</small>
                        </div>
                    </li>
                </ul>
                <!-- end sidebar user -->
                <!-- begin sidebar nav -->
                <ul class="nav">
                    <li class="has-sub menu-1-com">
                        <a href="javascript:;">
                            <b class="caret pull-right"></b>
                            日常<span>管理</span>
                        </a>
                        <ul class="sub-menu">
                            <li class="menu-2-ask-for-leave"><a href="<?= Url::to([
                                    '/hr/ask-for-leave/index',
                                    'menu'=>Json::encode([
                                        'menu-1-com',
                                        'menu-2-ask-for-leave'
                                    ]),
                                ])?>">我要请假</a></li>
                            <li class="menu-2-apply-payment"><a href="<?= Url::to([
                                    '/daily/apply-payment/index',
                                    'menu'=>Json::encode([
                                        'menu-1-com',
                                        'menu-2-apply-payment'
                                    ]),
                                ])?>">我要付款</a></li>
                            <li class="menu-2-apply-check-stamp"><a href="<?= Url::to([
                                    '/daily/apply-check-stamp/index',
                                    'menu'=>Json::encode([
                                        'menu-1-com',
                                        'menu-2-apply-check-stamp'
                                    ]),
                                ])?>">提交发票</a></li>
                            <li class="menu-2-regulation"><a href="<?= Url::to([
                                    '/daily/regulation/index',
                                    'menu'=>Json::encode([
                                        'menu-1-com',
                                        'menu-2-regulation'
                                    ]),
                                ])?>">文档管理</a></li>
                            <li class="menu-2-transaction"><a href="<?= Url::to([
                                    '/daily/transaction/index',
                                    'menu'=>Json::encode([
                                        'menu-1-com',
                                        'menu-2-transaction'
                                    ]),
                                ])?>">事项管理</a></li>
                            <li class="menu-2-week-report"><a href="<?= Url::to([
                                    '/daily/week-report/index',
                                    'menu'=>Json::encode([
                                        'menu-1-com',
                                        'menu-2-week-report'
                                    ]),
                                ])?>">周报管理</a></li>
                            <li class="menu-2-recruitment"><a href="<?= Url::to([
                                    '/recruitment/apply-recruit/index',
                                    'menu'=>Json::encode([
                                        'menu-1-com',
                                        'menu-2-recruitment'
                                    ]),
                                ])?>">我要招聘</a></li>
                            <li class="menu-2-interview"><a href="<?= Url::to([
                                    '/recruitment/interview/index',
                                    'menu'=>Json::encode([
                                        'menu-1-com',
                                        'menu-2-interview'
                                    ]),
                                ])?>">我的面试</a></li>
                            <li class="menu-3-calculate">
                                <a href="<?= Url::to([
                                    '/fin/calculate/index',
                                ])?>" target="_blank">计算器</a>
                            </li>
<!--                            <li class="menu-2-add-supplier"><a href="//= Url::to([
//                                    '/crm/supplier/recommend',
//                                    'menu'=>Json::encode([
//                                        'menu-1-com',
//                                        'menu-2-add-supplier'
//                                    ]),
//                                ])?><!--">推荐供应商</a></li>-->
<!--                            <li class="menu-2-add-part-time"><a href="//= Url::to([
//                                    '/crm/part-time/recommend',
//                                    'menu'=>Json::encode([
//                                        'menu-1-com',
//                                        'menu-2-add-part-time'
//                                    ]),
//                                ])?><!--">推介兼职</a></li>-->
                        </ul>
                    </li>

                    <li class="has-sub menu-1-assess">
                        <a href="javascript:;">
                            <b class="caret pull-right"></b>
                            审批<span>中心</span>
                        </a>
                        <ul class="sub-menu">
                            <li class="menu-2-ask-for-leave"><a href="<?= Url::to([
                                    '/hr/ask-for-leave/assess',
                                    'menu'=>Json::encode([
                                        'menu-1-assess',
                                        'menu-2-ask-for-leave',
                                    ]),
                                ])?>">请假审批</a></li>
<?php
if(Yii::$app->authManager->checkAccess(
    Yii::$app->user->getIdentity()->getId(),
    PermissionManager::applyRecruitAssess
)) :?>
                            <li class="menu-2-apply-recruitment-assess"><a href="<?= Url::to([
                                    '/recruitment/apply-recruit/assess',
                                    'menu'=>Json::encode([
                                        'menu-1-assess',
                                        'menu-2-apply-recruitment-assess',
                                    ]),
                                ])?>">招聘审批</a></li>
                            <li class="menu-2-candidate-assess"><a href="<?= Url::to([
                                    '/recruitment/interview/assess-index',
                                    'menu'=>Json::encode([
                                        'menu-1-assess',
                                        'menu-2-candidate-assess',
                                    ]),
                                ])?>">面试人审批</a></li>
<?php endif;?>
<?php
if(Yii::$app->authManager->checkAccess(
    Yii::$app->user->getIdentity()->getId(),
    PermissionManager::ProjectAssess
)) :?>
                            <li class="menu-2-project-active-assess"><a href="<?= Url::to([
                                    '/crm/project/active-assess',
                                    'menu'=>Json::encode([
                                        'menu-1-assess',
                                        'menu-2-project-active-assess',
                                    ]),
                                ])?>">项目立项审批</a></li>
                            <li class="menu-2-project-done-assess"><a href="<?= Url::to([
                                    '/crm/project/done-assess',
                                    'menu'=>Json::encode([
                                        'menu-1-assess',
                                        'menu-2-project-done-assess',
                                    ]),
                                ])?>">项目结案审批</a></li>
                            <li class="menu-2-project-brief-assess"><a href="<?= Url::to([
                                    '/crm/project-brief/assess',
                                    'menu'=>Json::encode([
                                        'menu-1-assess',
                                        'menu-2-project-brief-assess',
                                    ]),
                                ])?>">项目brief审核</a></li>
<?php endif;?>
                            <li  class="has-sub menu-2-daily">
                                <a href="javascript:;">
                                    <b class="caret pull-right"></b>
                                    日常付款审批
                                </a>
                                <ul class="sub-menu">
                                    <li class="menu-3-waiting"><a href="<?= Url::to([
                                            '/payment_assess/daily-payment-assess/waiting',
                                            'menu'=>Json::encode([
                                                'menu-1-assess',
                                                'menu-2-daily',
                                                'menu-3-waiting'
                                            ]),
                                        ])?>">待审核</a></li>
                                    <li class="menu-3-succeed"><a href="<?= Url::to([
                                            '/payment_assess/daily-payment-assess/succeed',
                                            'menu'=>Json::encode([
                                                'menu-1-assess',
                                                'menu-2-daily',
                                                'menu-3-succeed'
                                            ]),
                                        ])?>">审核通过</a></li>
                                    <li class="menu-3-refused"><a href="<?= Url::to([
                                            '/payment_assess/daily-payment-assess/refused',
                                            'menu'=>Json::encode([
                                                'menu-1-assess',
                                                'menu-2-daily',
                                                'menu-3-refused'
                                            ]),
                                        ])?>">审核未通过</a></li>
                                </ul>
                            </li>
                            <li  class="has-sub menu-2-project">
                                <a href="javascript:;">
                                    <b class="caret pull-right"></b>
                                    项目付款审批
                                </a>
                                <ul class="sub-menu">
                                    <li class="menu-3-waiting"><a href="<?= Url::to([
                                            '/payment_assess/project-payment-assess/waiting',
                                            'menu'=>Json::encode([
                                                'menu-1-assess',
                                                'menu-2-project',
                                                'menu-3-waiting'
                                            ]),
                                        ])?>">待审核</a></li>
                                    <li class="menu-3-succeed"><a href="<?= Url::to([
                                            '/payment_assess/project-payment-assess/succeed',
                                            'menu'=>Json::encode([
                                                'menu-1-assess',
                                                'menu-2-project',
                                                'menu-3-succeed'
                                            ]),
                                        ])?>">审核通过</a></li>
                                    <li class="menu-3-refused"><a href="<?= Url::to([
                                            '/payment_assess/project-payment-assess/refused',
                                            'menu'=>Json::encode([
                                                'menu-1-assess',
                                                'menu-2-project',
                                                'menu-3-refused'
                                            ]),
                                        ])?>">审核未通过</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="has-sub menu-1-customer">
                        <a href="javascript:;">
                            <b class="caret pull-right"></b>
                            客户<span>管理</span>
                        </a>
                        <ul class="sub-menu">
                            <li class="menu-2-public-customer"><a href="<?= Url::to([
                                    '/crm/public-customer/index',
                                    'menu'=>Json::encode([
                                        'menu-1-customer',
                                        'menu-2-public-customer'
                                    ]),
                                ])?>">公共客户池</a></li>
                            <?php if(Yii::$app->authManager->canAccess(PermissionManager::MyCustomerMenu)) : ?>
                            <li class="menu-2-private-customer"><a href="<?= Url::to([
                                    '/crm/private-customer/index',
                                    'menu'=>Json::encode([
                                        'menu-1-customer',
                                        'menu-2-private-customer'
                                    ]),
                                ])?>">我的客户</a></li>
                            <?php endif?>
                            <?php if(Yii::$app->authManager->isPointedRolesLead(RoleManager::Sales)) : ?>
                                <li class="menu-2-sales-statistic"><a href="<?= Url::to([
                                        '/statistic/sales-statistic/index',
                                        'menu'=>Json::encode([
                                            'menu-1-customer',
                                            'menu-2-sales-statistic'
                                        ]),
                                    ])?>">销售统计</a></li>
                            <?php endif;?>
                            <?php if(Yii::$app->user->getIdentity()->getUserName() === 'admin') :?>
                            <li class="menu-2-config"><a href="<?= Url::to([
                                    '/crm/customer-config/index',
                                    'menu'=>Json::encode([
                                        'menu-1-customer',
                                        'menu-2-config'
                                    ]),
                                ])?>">配置</a></li>
                            <?php endif?>
                        </ul>
                    </li>
<?php
if(Yii::$app->authManager->checkAccess(
    Yii::$app->user->getIdentity()->getId(),
    PermissionManager::ProjectMenu
)) :?>
                    <li class="has-sub menu-1-project">
                        <a href="javascript:;">
                            <b class="caret pull-right"></b>
                            项目<span>管理</span>
                        </a>
                        <ul class="sub-menu">
                            <li class="menu-2-project"><a href="<?= Url::to([
                                    '/crm/project/index',
                                    'menu'=>Json::encode([
                                        'menu-1-project',
                                        'menu-2-project'
                                    ]),
                                ])?>">我的项目</a></li>
                            <li class="menu-2-apply-payment"><a href="<?= Url::to([
                                    '/crm/project-apply-payment/index',
                                    'menu'=>Json::encode([
                                        'menu-1-project',
                                        'menu-2-apply-payment'
                                    ]),
                                ])?>">申请付款</a></li>
                            <li class="menu-2-apply-check-stamp"><a href="<?= Url::to([
                                    '/crm/project-apply-check-stamp/index',
                                    'menu'=>Json::encode([
                                        'menu-1-project',
                                        'menu-2-apply-check-stamp'
                                    ]),
                                ])?>">提交发票</a></li>
                            <?php if(Yii::$app->authManager->isPointedRolesLead(RoleManager::Project)) : ?>
                            <li class="menu-2-project-statistic"><a href="<?= Url::to([
                                    '/statistic/project-statistic/index',
                                    'menu'=>Json::encode([
                                        'menu-1-project',
                                        'menu-2-project-statistic'
                                    ]),
                                ])?>">项目统计</a></li>
                            <?php endif;?>
                            <?php if(Yii::$app->user->getIdentity()->getUserName() === 'admin') :?>
                                <li class="menu-2-config"><a href="<?= Url::to([
                                        '/crm/project-config/index',
                                        'menu'=>Json::encode([
                                            'menu-1-project',
                                            'menu-2-config'
                                        ]),
                                    ])?>">配置</a></li>
                            <?php endif?>
                        </ul>
                    </li>
<?php endif?>
<?php
if(Yii::$app->authManager->checkAccess(
    Yii::$app->user->getIdentity()->getId(),
    PermissionManager::SupplierMenu
)) :?>
                    <li class="has-sub menu-1-supplier">
                        <a href="javascript:;">
                            <b class="caret pull-right"></b>
                            供应商<span>&兼职</span>
                        </a>
                        <ul class="sub-menu">
                            <li class="menu-2-supplier"><a href="<?= Url::to([
                                    '/crm/supplier/index',
                                    'menu'=>Json::encode([
                                        'menu-1-supplier',
                                        'menu-2-supplier'
                                    ]),
                                ])?>">供应商</a></li>
                            <li class="menu-2-part-time"><a href="<?= Url::to([
                                    '/crm/part-time/index',
                                    'menu'=>Json::encode([
                                        'menu-1-supplier',
                                        'menu-2-part-time',
                                    ]),
                                ])?>">兼职</a></li>
                            <li class="menu-2-apply-payment">
                                <a href="<?= Url::to([
                                    '/crm/supplier-apply-payment/index',
                                    'menu'=>Json::encode([
                                        'menu-1-supplier',
                                        'menu-2-apply-payment',
                                    ]),
                                ])?>">申请付款</a>
                            </li>
                            <li class="menu-2-apply-check-stamp"><a href="<?= Url::to([
                                    '/crm/supplier-apply-check-stamp/index',
                                    'menu'=>Json::encode([
                                        'menu-1-supplier',
                                        'menu-2-apply-check-stamp'
                                    ]),
                                ])?>">提交发票</a></li>
                            <li class="menu-2-project-media-brief">
                                <a href="<?= Url::to([
                                    '/crm/project-media-brief/media-index',
                                    'menu'=>Json::encode([
                                        'menu-1-supplier',
                                        'menu-2-project-media-brief',
                                    ]),
                                ])?>">媒介brief</a>
                            </li>
                            <?php if(Yii::$app->user->getIdentity()->getUserName() === 'admin') :?>
                            <li  class="has-sub menu-2-config">
                                <a href="javascript:;">
                                    <b class="caret pull-right"></b>
                                    配置
                                </a>
                                <ul class="sub-menu">
                                        <li class="menu-3-supplier"><a href="<?= Url::to([
                                                '/crm/supplier-config/index',
                                                'menu'=>Json::encode([
                                                    'menu-1-supplier',
                                                    'menu-2-config',
                                                    'menu-3-supplier',
                                                ]),
                                            ])?>">供应商配置</a></li>
                                        <li class="menu-3-part-time"><a href="<?= Url::to([
                                                '/crm/part-time-config/index',
                                                'menu'=>Json::encode([
                                                    'menu-1-supplier',
                                                    'menu-2-config',
                                                    'menu-3-part-time',
                                                ]),
                                            ])?>">兼职配置</a></li>
                                </ul>
                            </li>
                            <?php endif?>
                        </ul>
                    </li>
<?php endif?>
<?php
if(Yii::$app->authManager->checkAccess(
    Yii::$app->user->getIdentity()->getId(),
    PermissionManager::HumanResourceMenu
)) :?>
                    <li class="has-sub menu-1-hr">
                        <a href="javascript:;">
                            <b class="caret pull-right"></b>
                            人事<span>管理</span>
                        </a>
                        <ul class="sub-menu">
                            <li class="menu-2-employee"><a href= "<?= Url::to([
                                    '/hr/employee/index',
                                    'menu'=>Json::encode([
                                        'menu-1-hr',
                                        'menu-2-employee',
                                    ]),
                                ])?>">员工管理</a></li>
                            <li class="menu-2-position"><a href= "<?= Url::to([
                                    '/hr/position/index',
                                    'menu'=>Json::encode([
                                        'menu-1-hr',
                                        'menu-2-position',
                                    ]),
                                ])?>">职位管理</a></li>
                            <li class="menu-2-department"><a href= "<?= Url::to([
                                    '/hr/department/index',
                                    'menu'=>Json::encode([
                                        'menu-1-hr',
                                        'menu-2-department',
                                    ]),
                                ])?>">部门管理</a></li>
                            <li class="menu-2-ask-for-leave"><a href= "<?= Url::to([
                                    '/hr/ask-for-leave/hr-index',
                                    'menu'=>Json::encode([
                                        'menu-1-hr',
                                        'menu-2-ask-for-leave',
                                    ]),
                                ])?>">请假管理</a></li>
                            <li class="menu-2-candidate"><a href= "<?= Url::to([
                                    '/recruitment/candidate/index',
                                    'menu'=>Json::encode([
                                        'menu-1-hr',
                                        'menu-2-candidate',
                                    ]),
                                ])?>">简历库</a></li>
                            <li class="menu-2-recommend-candidate"><a href= "<?= Url::to([
                                    '/recruitment/recommend-candidate/index',
                                    'menu'=>Json::encode([
                                        'menu-1-hr',
                                        'menu-2-recommend-candidate',
                                    ]),
                                ])?>">招聘列表</a></li>
                            <li class="menu-2-interview"><a href= "<?= Url::to([
                                    '/recruitment/interview/hr-index',
                                    'menu'=>Json::encode([
                                        'menu-1-hr',
                                        'menu-2-interview',
                                    ]),
                                ])?>">面试列表</a></li>
    <?php if(Yii::$app->user->getIdentity()->getUserName() === 'admin') :?>
                            <li  class="has-sub menu-2-config">
                                <a href="javascript:;">
                                    <b class="caret pull-right"></b>
                                    配置
                                </a>
                                <ul class="sub-menu">
                                    <li class="menu-3-basic">
                                        <a href="<?= Url::to([
                                            '/hr/employee-basic-config/index',
                                            'menu'=>Json::encode([
                                                'menu-1-hr',
                                                'menu-2-config',
                                                'menu-3-basic'
                                            ]),
                                        ])?>">基本配置</a>
                                    </li>
                                    <li class="menu-3-entry">
                                        <a href="<?= Url::to([
                                            '/hr/employee-entry-config/index',
                                            'menu'=>Json::encode([
                                                'menu-1-hr',
                                                'menu-2-config',
                                                'menu-3-entry'
                                            ]),
                                        ])?>">入职清单</a>
                                    </li>
                                    <li class="menu-3-dismiss">
                                        <a href="<?= Url::to([
                                            '/hr/employee-dismiss-config/index',
                                            'menu'=>Json::encode([
                                                'menu-1-hr',
                                                'menu-2-config',
                                                'menu-3-dismiss'
                                            ]),
                                        ])?>">离职清单</a>
                                    </li>
                                </ul>
                            </li>
    <?php endif;?>
                        </ul>
                    </li>
<?php endif?>
<?php
if(Yii::$app->authManager->checkAccess(
    Yii::$app->user->getIdentity()->getId(),
    PermissionManager::FinancialMenu
)) :?>
                    <li class="has-sub menu-1-fin">
                        <a href="javascript:;">
                            <b class="caret pull-right"></b>
                            财务<span>管理</span>
                        </a>
                        <ul class="sub-menu">
<!--                            <li  class="has-sub menu-2-contract">-->
<!--                                <a href="javascript:;">-->
<!--                                    <b class="caret pull-right"></b>-->
<!--                                    合同-->
<!--                                </a>-->
<!--                                <ul class="sub-menu">-->
<!--                                    <li class="menu-3-contract"><a href="#">合同列表</a></li>-->
<!--                                    <li class="menu-3-template"><a href="--><!--//= Url::to([
//                                            '/fin/contract-template/index',
//                                            'menu'=>Json::encode([
//                                                'menu-1-fin',
//                                                'menu-2-contract',
//                                                'menu-3-template'
//                                            ]),
//                                        ])?><!--">合同模板管理</a></li>-->
<!--                                    --><!--?php //if(Yii::$app->user->getIdentity()->getUserName() === 'admin') :?>
<!--                                        <li class="menu-3-config"><a href="--><!--?//= Url::to([
//                                                '/fin/contract-config/index',
//                                                'menu'=>Json::encode([
//                                                    'menu-1-fin',
//                                                    'menu-2-contract',
//                                                    'menu-3-config'
//                                                ]),
//                                            ])?><!--">配置</a></li>-->
<!--                                    --><!--?php //endif?>
<!--                                </ul>-->
<!--                            </li>-->
                            <li  class="has-sub menu-2-account-receivable">
                                <a href="javascript:;">
                                    <b class="caret pull-right"></b>
                                    应收款管理
                                </a>
                                <ul class="sub-menu">
                                    <li class="menu-3-account-receivable">
                                        <a href="<?= Url::to([
                                            '/accountReceivable/account-receivable/index',
                                            'menu'=>Json::encode([
                                                'menu-1-fin',
                                                'menu-2-account-receivable',
                                                'menu-3-account-receivable'
                                            ]),
                                        ])?>">应收款</a>
                                    </li>
                                    <li class="menu-3-receive-money">
                                        <a href="<?= Url::to([
                                            '/accountReceivable/receive-money/index',
                                            'menu'=>Json::encode([
                                                'menu-1-fin',
                                                'menu-2-account-receivable',
                                                'menu-3-receive-money'
                                            ]),
                                        ])?>">收款管理</a>
                                    </li>
                                    <li class="menu-3-stamp-apply-manage">
                                        <a href="<?= Url::to([
                                            '/accountReceivable/stamp-apply/index',
                                            'menu'=>Json::encode([
                                                'menu-1-fin',
                                                'menu-2-account-receivable',
                                                'menu-3-stamp-apply-manage'
                                            ]),
                                        ])?>">开票申请管理</a>
                                    </li>
                                </ul>
                            </li>
                            <li  class="has-sub menu-2-payment">
                                <a href="javascript:;">
                                    <b class="caret pull-right"></b>
                                    应付款管理
                                </a>
                                <ul class="sub-menu">
                                    <li class="menu-3-payment">
                                        <a href="<?= Url::to([
                                            '/payment/payment/index',
                                            'menu'=>Json::encode([
                                                'menu-1-fin',
                                                'menu-2-payment',
                                                'menu-3-payment'
                                            ]),
                                        ])?>">应付款</a>
                                    </li>
                                    <li class="menu-3-check-stamp">
                                        <a href="<?= Url::to([
                                            '/payment/check-stamp/index',
                                            'menu'=>Json::encode([
                                                'menu-1-fin',
                                                'menu-2-payment',
                                                'menu-3-check-stamp'
                                            ]),
                                        ])?>">发票验收</a>
                                    </li>
                                    <?php if(Yii::$app->user->getIdentity()->getUserName() === 'admin') :?>
                                        <li class="menu-3-config"><a href="<?= Url::to([
                                                '/payment/payment-config/index',
                                                'menu'=>Json::encode([
                                                    'menu-1-fin',
                                                    'menu-2-payment',
                                                    'menu-3-config'
                                                ]),
                                            ])?>">配置</a></li>
                                    <?php endif?>
                                </ul>
                            </li>
                            <li  class="has-sub menu-2-stamp">
                                <a href="javascript:;">
                                    <b class="caret pull-right"></b>
                                    发票管理
                                </a>
                                <ul class="sub-menu">
                                    <li class="menu-3-import">
                                        <a href="<?= Url::to([
                                            '/stamp/import-stamp/index',
                                            'menu'=>Json::encode([
                                                'menu-1-fin',
                                                'menu-2-stamp',
                                                'menu-3-import'
                                            ]),
                                        ])?>">进项发票</a>
                                    </li>
                                    <li class="menu-3-export">
                                        <a href="<?= Url::to([
                                            '/stamp/export-stamp/index',
                                            'menu'=>Json::encode([
                                                'menu-1-fin',
                                                'menu-2-stamp',
                                                'menu-3-export'
                                            ]),
                                        ])?>">销项发票</a>
                                    </li>
                                    <?php if(Yii::$app->user->getIdentity()->getUserName() === 'admin') :?>
                                        <li class="menu-3-config"><a href="<?= Url::to([
                                                '/stamp/stamp-config/index',
                                                'menu'=>Json::encode([
                                                    'menu-1-fin',
                                                    'menu-2-stamp',
                                                    'menu-3-config'
                                                ]),
                                            ])?>">配置</a></li>
                                    <?php endif?>
                                </ul>
                            </li>
                        </ul>
                    </li>
<?php endif?>
<?php
if(Yii::$app->authManager->checkAccess(
    Yii::$app->user->getIdentity()->getId(),
    PermissionManager::SystemMenu
)) :?>
                    <li class="has-sub menu-1-config">
                        <a href="javascript:;">
                            <b class="caret pull-right"></b>
                            系统<span>配置</span>
                        </a>
                        <ul class="sub-menu">
                            <li  class="has-sub menu-2-permission">
                                <a href="javascript:;">
                                    <b class="caret pull-right"></b>
                                    权限
                                </a>
                                <ul class="sub-menu">
                                    <li class="menu-3-assignment"><a href="<?= Url::to([
                                            '/rbac/assignment/index',
                                            'menu'=>Json::encode([
                                                'menu-1-config',
                                                'menu-2-permission',
                                                'menu-3-assignment'
                                            ]),
                                        ])?>">角色分配1</a></li>
                                    <li class="menu-3-assignment2"><a href="<?= Url::to([
                                            '/rbac/assignment/index2',
                                            'menu'=>Json::encode([
                                                'menu-1-config',
                                                'menu-2-permission',
                                                'menu-3-assignment2'
                                            ]),
                                        ])?>">角色分配2</a></li>
                                    <li class="menu-3-role"><a href="<?= Url::to([
                                            '/rbac/role/index',
                                            'menu'=>Json::encode([
                                                'menu-1-config',
                                                'menu-2-permission',
                                                'menu-3-role'
                                            ]),
                                        ])?>">角色管理</a></li>
                                    <li class="menu-3-permission"><a href="<?= Url::to([
                                            '/rbac/permission/index',
                                            'menu'=>Json::encode([
                                                'menu-1-config',
                                                'menu-2-permission',
                                                'menu-3-permission'
                                            ]),
                                        ])?>">权限管理</a></li>
                                    <li class="menu-3-rule"><a href="<?= Url::to([
                                            '/rbac/rule/index',
                                            'menu'=>Json::encode([
                                                'menu-1-config',
                                                'menu-2-permission',
                                                'menu-3-rule'
                                            ]),
                                        ])?>">规则管理</a></li>
                                </ul>
                            </li>
                            <li  class="has-sub menu-2-payment-assess">
                                <a href="javascript:;">
                                    <b class="caret pull-right"></b>
                                    付款流程
                                </a>
                                <ul class="sub-menu">
                                    <li class="menu-3-project"><a href="<?= Url::to([
                                            '/system/project-payment-assess-config/index',
                                            'menu'=>Json::encode([
                                                'menu-1-config',
                                                'menu-2-payment-assess',
                                                'menu-3-project'
                                            ]),
                                        ])?>">
                                            项目付款审批流程
                                        </a>
                                    </li>
                                    <li class="menu-3-daily"><a href="<?= Url::to([
                                            '/system/daily-payment-assess-config/index',
                                            'menu'=>Json::encode([
                                                'menu-1-config',
                                                'menu-2-payment-assess',
                                                'menu-3-daily'
                                            ]),
                                        ])?>">日常付款审批</a></li>
<!--                                    <li class="menu-3-expense">-->
<!--                                        <a href="--><!--//= Url::to([
//                                            '/system/expense-payment-assess-config/index',
//                                            'menu'=>Json::encode([
//                                                'menu-1-config',
//                                                'menu-2-payment-assess',
//                                                'menu-3-expense'
//                                            ]),
//                                        ])?><!--">-->
<!--                                        报销审批流程-->
<!--                                        </a>-->
<!--                                    </li>-->
                                    <li class="menu-3-pay">
                                        <a href="<?= Url::to([
                                            '/system/pay-config/index',
                                            'menu'=>Json::encode([
                                                'menu-1-config',
                                                'menu-2-payment-assess',
                                                'menu-3-pay'
                                            ]),
                                        ])?>">
                                            付款配置
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
<?php endif?>
                    <li><a href="javascript:;" class="sidebar-minify-btn" data-click="sidebar-minify"><i class="fa fa-angle-double-left"></i></a></li>
                    <!-- end sidebar minify button -->
                </ul>
                <!-- end sidebar nav -->
            </div>
            <!-- end sidebar scrollbar -->
        </div>
        <div class="sidebar-bg"></div>
        <!-- end #sidebar -->

        <!-- begin #content -->
        <div id="content" class="content content-full-width">
            <?=$content?>
        </div>
        <!-- end #content -->
        <!-- begin scroll to top btn -->
        <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
        <!-- end scroll to top btn -->
    </div>
    <!-- end page container -->
<?php $this->endContent();?>