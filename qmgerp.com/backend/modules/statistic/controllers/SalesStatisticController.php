<?php
namespace backend\modules\statistic\controllers;
use backend\models\BackEndBaseController;
use backend\models\CompressHtml;
use backend\modules\statistic\models\SalesAnniversaryAchievementStatistic;
use backend\modules\statistic\models\SalesCustomerStatistic;
use Yii;
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-11-21
 * Time: 下午1:12
 */
class SalesStatisticController extends BackEndBaseController
{
    public function actionIndex() {
        return $this->render('index', [
            'tab'=>$this->getParam('tab','customer'),
        ]);
    }

    public function actionCustomerStatisticListFilter() {

        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $customerStatistic = new SalesCustomerStatistic();
        $customerStatistic->clearEmptyField($filter);
        $customerStatisticList = $customerStatistic->listFilter($filter);
        return $this->render('index',[
            'customer_statistic_ser_filter'=>serialize($filter),
            'customerStatisticList'=>$customerStatisticList,
        ]);
    }

    public function actionAnniversaryAchievementListFilter() {

        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $salesAchievement = new SalesAnniversaryAchievementStatistic();
        $salesAchievement->clearEmptyField($filter);
        $anniversaryAchievementList = $salesAchievement->listFilter($filter);
        return $this->render('index',[
            'anniversary_achievement_ser_filter'=>serialize($filter),
            'anniversaryAchievementList'=>$anniversaryAchievementList,
        ]);
    }

    public function actionEditAnniversaryAchievement() {
        if(!Yii::$app->request->isAjax) {
            return '';
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return '';
        }

        $salesAchievement = new SalesAnniversaryAchievementStatistic();
        $formData = $salesAchievement->getRecord($uuid);
        return CompressHtml::compressHtml($this->renderPartial('anniversary-achievement-form', [
            'formData'=>$formData,
            'action'=>['/statistic/sales-statistic/update-anniversary-achievement'],
            'show'=>true,
        ]));
    }

    public function actionUpdateAnniversaryAchievement() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect([
                'index',
                'tab'=>'achievement',
            ]);
        }

        $formData = Yii::$app->request->post('AnniversaryAchievementForm');
        $salesAchievement = new SalesAnniversaryAchievementStatistic();
        if($salesAchievement->updateRecord($formData)) {
            return $this->redirect([
                'index',
                'tab'=>'achievement',
            ]);
        }
    }

    public function actionValidateAnniversaryAchievement() {
        if(!Yii::$app->request->isAjax) {
            return ;
        }

        $sales_uuid = Yii::$app->request->get('sales_uuid');
        $year = Yii::$app->request->get('year');
        if(empty($sales_uuid) || empty($year)) {
            return SalesAnniversaryAchievementStatistic::ValidateSucceed;
        }

        $salesAnniversary = new SalesAnniversaryAchievementStatistic();
        return $salesAnniversary->validateTarget($sales_uuid, $year);
    }

    public function actionAddAnniversaryAchievement() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect([
                'index',
                'tab'=>'achievement',
            ]);
        }

        $formData = Yii::$app->request->post('AnniversaryAchievementForm');
        $salesAchievement = new SalesAnniversaryAchievementStatistic();
        if($salesAchievement->insertRecord($formData)) {
            return $this->redirect([
                'index',
                'tab'=>'achievement',
            ]);
        }
    }
}