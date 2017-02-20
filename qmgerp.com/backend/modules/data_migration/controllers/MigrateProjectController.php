<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/1 0001
 * Time: 下午 12:01
 */

namespace backend\modules\data_migration\controllers;


use backend\models\BackEndBaseController;
use backend\modules\data_migration\models\MigrateProject;

class MigrateProjectController extends BackEndBaseController
{
    public function actionMigrate() {
        return ;
        $migrateProject = new MigrateProject();
        if($migrateProject->migrate()) {
            $this->redirect(['/crm/project/index']);
        } else {
            var_dump('update failed!!');die;
        }
    }
}