<?php
namespace backend\modules\hr\recruitment\controllers;
use backend\models\BackEndBaseController;
use backend\models\CompressHtml;
use backend\modules\hr\models\Position;
use backend\modules\hr\recruitment\models\ApplyRecruit;
use backend\modules\hr\recruitment\models\ApplyRecruitConfig;
use Yii;
use yii\helpers\Json;

/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-12-8
 * Time: 下午11:46
 */
class ApplyRecruitController extends BackEndBaseController
{
    public function actionIndex() {
        return $this->render('index');
    }

    public function actionAssess() {
        return $this->render('assess');
    }

    public function actionAssessSucceed() {
        if (!Yii::$app->request->isGet) {
            return $this->redirect(['index']);
        }

        $uuid = Yii::$app->request->get('uuid');
        if (empty($uuid)) {
            return $this->redirect(['assess']);
        }

        $applyRecruit = new ApplyRecruit();
        if ($applyRecruit->updateRecord([
            'uuid'=>$uuid,
            'status'=>ApplyRecruitConfig::StatusRecruiting,
            'assess_uuid'=>Yii::$app->getUser()->getIdentity()->getId()
        ])) {
            return $this->redirect(['assess']);
        }
    }

    public function actionAssessRefused() {
        if (!Yii::$app->request->isPost) {
            return $this->redirect(['assess']);
        }

        $formData = Yii::$app->request->post();

        $applyRecruit = new ApplyRecruit();
        if ($applyRecruit->updateRecord([
            'uuid'=>$formData['uuid'],
            'refuse_reason'=>$formData['refuse_reason'],
            'status'=>ApplyRecruitConfig::StatusAssessRefused,
            'assess_uuid'=>Yii::$app->getUser()->getIdentity()->getId()
        ])) {
            return $this->redirect(['assess']);
        }
    }

    public function actionAchieved() {
        if (!Yii::$app->request->isGet) {
            return $this->redirect(['index']);
        }

        $uuid = Yii::$app->request->get('uuid');
        if (empty($uuid)) {
            return $this->redirect(['assess']);
        }

        $applyRecruit = new ApplyRecruit();
        if ($applyRecruit->updateRecord([
            'uuid'=>$uuid,
            'status'=>ApplyRecruitConfig::StatusAchieved,
        ])) {
            return $this->redirect(['index']);
        }
    }

    public function actionAdd() {
        if (!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post('ApplyRecruitForm');
        $applyRecruit = new ApplyRecruit();
        if ($applyRecruit->insertRecord($formData)) {
            return $this->redirect(['index']);
        }
    }

    public function actionUpdate() {
        if (!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post('ApplyRecruitForm');
        $applyRecruit = new ApplyRecruit();
        if ($applyRecruit->updateRecord($formData)) {
            return $this->redirect(['index']);
        }
    }

    public function actionDel() {
        if (!Yii::$app->request->isGet) {
            return $this->redirect(['index']);
        }
        $uuid = Yii::$app->request->get('uuid');
        if (empty($uuid)) {
            return $this->redirect(['index']);
        }

        $applyRecruit = new ApplyRecruit();
        if ($applyRecruit->deleteRecord($uuid)) {
            return $this->redirect(['index']);
        }
    }

    public function actionShow() {
        if (!Yii::$app->request->isGet) {
            return $this->redirect(['index']);
        }
        $uuid = Yii::$app->request->get('uuid');
        if (empty($uuid)) {
            return $this->redirect(['index']);
        }

        $applyRecruit = new ApplyRecruit();
        $formData = $applyRecruit->getRecord($uuid);
        return CompressHtml::compressHtml($this->renderPartial('form',[
            'formData'=>$formData,
            'action'=>['/recruitment/apply-recruit/update'],
            'edit'=>$this->getParam('edit', false) && $formData['status'] == ApplyRecruitConfig::StatusApplying,
            'show'=>true,
        ]));
    }

    public function actionLoadPositionInformation() {
        if (!Yii::$app->request->isAjax) {
            return null;
        }

        $position_uuid = Yii::$app->request->get('position_uuid');
        if (empty($position_uuid)) {
            return null;
        }

        $position = new Position();
        $formData = $position->getRecordByUuid($position_uuid);
        return Json::encode([
            'rest_number' => $formData['members_limit'] - $formData['number_of_active'],
            'position_requirement'=>$formData['requirement'],
        ]);
    }

    public function actionListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $applyRecruit = new ApplyRecruit();
        $applyRecruit->clearEmptyField($filter);
        $applyRecruitList = $applyRecruit->listFilter($filter);

        return $this->render('index', [
            'applyRecruitList'=>$applyRecruitList,
            'ser_filter'=>serialize($filter),
        ]);
    }

    public function actionAssessListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $applyRecruit = new ApplyRecruit();
        $applyRecruit->clearEmptyField($filter);
        $applyRecruitList = $applyRecruit->assessListFilter($filter);

        return $this->render('index', [
            'applyRecruitList'=>$applyRecruitList,
            'ser_filter'=>serialize($filter),
        ]);
    }
}