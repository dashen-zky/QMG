<?php
return [
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'bootstrap' => ['log'],
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=rdsd5rgwug1jpio8wbt4.mysql.rds.aliyuncs.com;dbname=dev_erp',//51wom开发环境数据库
            'username' => 'guxu',
            'password' => 'MIFANiopjklnm123',
            'charset' => 'utf8',
            'schemaMap' => [
                'pgsql' => 'yii\db\pgsql\Schema', // PostgreSQL
                'mysqli' => 'yii\db\mysql\Schema', // MySQL
                'mysql' => 'backend\models\system_overwrite\Schema', // MySQL
                'sqlite' => 'yii\db\sqlite\Schema', // sqlite 3
                'sqlite2' => 'yii\db\sqlite\Schema', // sqlite 2
                'sqlsrv' => 'yii\db\mssql\Schema', // newer MSSQL driver on MS Windows hosts
                'oci' => 'yii\db\oci\Schema', // Oracle driver
                'mssql' => 'yii\db\mssql\Schema', // older MSSQL driver on MS Windows hosts
                'dblib' => 'yii\db\mssql\Schema', // dblib drivers on GNU/Linux (and maybe other OSes) hosts
                'cubrid' => 'yii\db\cubrid\Schema', // CUBRID
            ],
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => 'redis'
        ],
        'session' => [
            'class' => 'yii\web\CacheSession',
            'cache' => 'cache'
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0
        ]
    ]
];
