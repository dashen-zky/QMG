<?php
namespace backend\modules\data_migration\controllers;
use backend\models\BackEndBaseController;
use backend\modules\data_migration\models\MigrateCustomer;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/31 0031
 * Time: 下午 5:45
 */
class MigrateCustomerController extends BackEndBaseController
{
    public function actionMigrate() {
        return  ;
        $migrateCustomer = new MigrateCustomer();
        if($migrateCustomer->migrate()) {
            $this->redirect(['/crm/public-customer/index']);
        } else {
            var_dump('update failed!!');die;
        }
    }
}