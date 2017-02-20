<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'qm-erp-backend',
    'aliases'=> [
        '@hr'=>"@yii/../../../backend/modules/hr",
        '@crm'=>"@yii/../../../backend/modules/crm",
        '@fin'=>"@yii/../../../backend/modules/fin",
        '@statistic'=>"@yii/../../../backend/modules/statistic",
        '@stamp'=>"@yii/../../../backend/modules/fin/stamp",
        '@payment'=>"@yii/../../../backend/modules/fin/payment",
        '@accountReceivable'=>"@yii/../../../backend/modules/fin/accountReceivable",
        '@project-contract'=>"@yii/../../../backend/modules/project-contract",
    ],
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'hr' => [
            'class' => 'backend\modules\hr\Module'
        ],
        'crm' => [
            'class' => 'backend\modules\crm\Module'
        ],
        'fin' => [
            'class' => 'backend\modules\fin\Module'
        ],
        'payment' => [
            'class' => 'backend\modules\fin\payment\Module'
        ],
        'accountReceivable' => [
            'class' => 'backend\modules\fin\accountReceivable\Module'
        ],
        'stamp' => [
            'class' => 'backend\modules\fin\stamp\Module'
        ],
        'rbac' => [
            'class' => 'backend\modules\rbac\Module'
        ],
        'daily' => [
            'class' => 'backend\modules\daily\Module'
        ],
        'system' => [
            'class' => 'backend\modules\system\Module'
        ],
        'payment_assess' => [
            'class' => 'backend\modules\payment_assess\Module'
        ],
        'message_queue' => [
            'class' => 'backend\modules\message_queue\Module'
        ],
        'data_migration' => [
            'class' => 'backend\modules\data_migration\Module'
        ],
        'statistic' => [
            'class' => 'backend\modules\statistic\Module'
        ],
        'wom_data' => [
            'class' => 'backend\modules\wom_data\Module'
        ],
        'recruitment' => [
            'class' => 'backend\modules\hr\recruitment\Module'
        ],
    ],
    'components' => [
        'user' => [
            'identityClass' => 'backend\models\Account',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
        'authManager'=>[
            'class'=>'backend\modules\rbac\model\RBACManager',
            'itemTable' => 'com_auth_item',
            'assignmentTable' => 'com_auth_assignment',
            'itemChildTable' => 'com_auth_item_child',
            'ruleTable'=>'com_auth_rule',
            'cache'=>'cache',
        ]
    ],
    'params' => $params,
];
