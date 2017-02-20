<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/27 0027
 * Time: 下午 3:22
 */

namespace backend\modules\crm\controllers;


use backend\models\BackEndBaseController;
use backend\models\CompressHtml;
use backend\modules\crm\models\project\record\ProjectApplyStamp;
use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use backend\modules\crm\models\stamp\Stamp;
use backend\modules\crm\models\project\record\ProjectCustomerMap;
use backend\models\ViewHelper;
use backend\modules\crm\models\project\model\ProjectForm;
use backend\modules\crm\models\project\record\Project;
class ProjectApplyStampController extends BackEndBaseController
{
    public function actionApply() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['/crm/project/index']);
        }
        
        $formData = Yii::$app->request->post('ApplyStampForm');
        $projectApplyStamp = new ProjectApplyStamp();
        if($projectApplyStamp->insertRecord($formData)) {
            return $this->redirect(['/crm/project/index']);
        }
    }

    public function actionBillingRecordList() {
        if(!Yii::$app->request->isAjax) {
            return null;
        }
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return null;
        }

        $projectApplyStamp = new ProjectApplyStamp();
        $projectApplyStampList = $projectApplyStamp->getListByProjectUuid($uuid);
        return CompressHtml::compressHtml($this->renderPartial('/project-apply-billing/list',[
            'projectApplyStampList'=>$projectApplyStampList,
        ]));
    }

    public function actionLoadStampList() {
        if(!Yii::$app->request->isAjax) {
            return ;
        }

        $uuid = Yii::$app->request->get('uuid');
        $projectCustomerMap = new ProjectCustomerMap();
        return Html::dropDownList('ApplyStampForm[stamp_message_uuid]', null,
            ViewHelper::appendElementOnDropDownList($projectCustomerMap->loadStampListByProjectUuid($uuid)), [
                'class'=>'form-control',
                'data-parsley-required'=>"true",
                'url'=>Url::to([
                    '/crm/project-apply-stamp/load-stamp-message'
                ]),
            ]);
    }

    public function actionLoadStampMessage() {
        if(!Yii::$app->request->isAjax) {
            return ;
        }

        $uuid = Yii::$app->request->get('uuid');
        $stamp = new Stamp();
        return Json::encode($stamp->getRecord($uuid));
    }

    public function actionListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['/crm/project/index','tab'=>'contact-list']);
            }
            $filter = unserialize($ser_filter);
        }
        $projectApplyStamp = new ProjectApplyStamp();
        $projectApplyStamp->clearEmptyField($filter);
        $projectApplyStampList = $projectApplyStamp->listFilter($filter);

        $model = new ProjectForm();
        $project = new Project();
        $projectList = $project->myProjectList();
        return $this->render('/project/index',[
            'projectApplyStampList'=>$projectApplyStampList,
            'stamp_list_ser_filter'=>serialize($filter),
            'tab'=>'contract-list',
            'model'=>$model,
            'projectList'=>$projectList,
        ]);
    }
}