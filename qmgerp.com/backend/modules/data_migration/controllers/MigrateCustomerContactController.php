<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/1 0001
 * Time: 上午 10:02
 */

namespace backend\modules\data_migration\controllers;


use backend\models\BackEndBaseController;
use backend\modules\data_migration\models\MigrateCustomerContact;

class MigrateCustomerContactController extends BackEndBaseController
{
    public function actionMigrate() {
        return ;
        $migrateCustomerContact = new MigrateCustomerContact();
        if($migrateCustomerContact->migrate()) {
            $this->redirect(['/crm/public-customer/index']);
        } else {
            var_dump('update failed!!');die;
        }
    }
}