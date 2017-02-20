<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-11-22
 * Time: 下午9:10
 */

namespace backend\modules\statistic\controllers;


use backend\models\BackEndBaseController;
use backend\modules\statistic\models\ProjectAnniversaryAchievement;
use backend\modules\statistic\models\ProjectStatistic;
use Yii;
use backend\models\CompressHtml;

class ProjectStatisticController extends BackEndBaseController
{
    public function actionIndex() {
        return $this->render('index', [
            'tab'=>$this->getParam('tab','project'),
        ]);
    }

    public function actionProjectStatisticListFilter() {

        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $projectStatistic = new ProjectStatistic();
        $projectStatistic->clearEmptyField($filter);
        $projectStatisticList = $projectStatistic->listFilter($filter);
        return $this->render('index',[
            'project_statistic_ser_filter'=>serialize($filter),
            'projectStatisticList'=>$projectStatisticList,
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
        $salesAchievement = new ProjectAnniversaryAchievement();
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

        $salesAchievement = new ProjectAnniversaryAchievement();
        $formData = $salesAchievement->getRecord($uuid);
        return CompressHtml::compressHtml($this->renderPartial('anniversary-achievement-form', [
            'formData'=>$formData,
            'action'=>['/statistic/project-statistic/update-anniversary-achievement'],
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
        $salesAchievement = new ProjectAnniversaryAchievement();
        if($salesAchievement->updateRecord($formData)) {
            return $this->redirect([
                'index',
                'tab'=>'achievement',
            ]);
        }
    }

    public function actionAddAnniversaryAchievement() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect([
                'index',
                'tab'=>'achievement',
            ]);
        }

        $formData = Yii::$app->request->post('AnniversaryAchievementForm');
        $projectAchievement = new ProjectAnniversaryAchievement();
        if($projectAchievement->insertRecord($formData)) {
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

        $manager_uuid = Yii::$app->request->get('manager_uuid');
        $year = Yii::$app->request->get('year');
        if(empty($manager_uuid) || empty($year)) {
            return ProjectAnniversaryAchievement::ValidateSucceed;
        }

        $projectAnniversary = new ProjectAnniversaryAchievement();
        return $projectAnniversary->validateTarget($manager_uuid, $year);
    }
}