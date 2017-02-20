<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/10 0010
 * Time: 下午 1:11
 */

namespace backend\modules\fin\accountReceivable\controllers;


use backend\models\BackEndBaseController;
use backend\modules\fin\accountReceivable\models\ReceiveMoneyCompany;
use Yii;
use yii\helpers\Json;

class ReceiveCompanyController extends BackEndBaseController
{
    public function actionAdd() {
        if(!Yii::$app->request->isPost) {
            return ;
        }
        
        $formData = Yii::$app->request->post('ReceiveCompanyForm');
        $receiveMoneyCompany = new ReceiveMoneyCompany();
        if($receiveMoneyCompany->insertRecord($formData)) {
            return $this->redirect([
                '/accountReceivable/receive-money/index',
                'tab'=>'receive-company',
            ]);
        }
    }
    
    public function actionDel() {
        if(!Yii::$app->request->isGet) {
            return ;
        }
        
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return ;
        }

        $receiveMoneyCompany = new ReceiveMoneyCompany();
        if($receiveMoneyCompany->deleteRecord($uuid)) {
            return $this->redirect([
                '/accountReceivable/receive-money/index',
                'tab'=>'receive-company',
            ]);
        }
    }

    public function actionLoadReceiveCompanyInformation() {
        if(!Yii::$app->request->isAjax) {
            return '';
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return '';
        }
        
        $receiveMoneyCompany = new ReceiveMoneyCompany();
        return Json::encode($receiveMoneyCompany->getRecord($uuid));
    }
}