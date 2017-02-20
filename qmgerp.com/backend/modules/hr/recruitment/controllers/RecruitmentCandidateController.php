<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-12-12
 * Time: 下午4:40
 */

namespace backend\modules\hr\recruitment\controllers;


use backend\models\BackEndBaseController;
use backend\modules\hr\recruitment\models\CandidateConfig;
use backend\modules\hr\recruitment\models\RecruitCandidateMap;
use Yii;
use backend\models\CompressHtml;

class RecruitmentCandidateController extends BackEndBaseController
{
    public function actionIndex() {
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['index']);
        }

        $uuid = Yii::$app->request->get('uuid');
        if (empty($uuid)) {
            return $uuid;
        }

        return $this->render('index',[
            'recruit_uuid'=>$uuid,
        ]);
    }

    public function actionDropRecruitAndCandidateRelation() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['/recruitment/apply-recruit/index']);
        }

        $formData = Yii::$app->request->post('RecruitmentCandidateForm');
        $recruitCandidateMap = new RecruitCandidateMap();
        if($recruitCandidateMap->dropRecruitAndCandidateRelation($formData)) {
            return $this->redirect(['/recruitment/apply-recruit/index']);
        }
    }

    public function actionNotifyInterview() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['/recruitment/apply-recruit/index']);
        }

        $formData = Yii::$app->request->post('RecruitmentCandidateForm');
        $recruitCandidateMap = new RecruitCandidateMap();
        if($recruitCandidateMap->notifyInterView($formData)) {
            return $this->redirect(['/recruitment/interview/index']);
        }
    }

    public function actionShowCandidate() {
        if(!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }

        $recruit_uuid = Yii::$app->request->get('recruit_uuid');
        $candidate_uuid = Yii::$app->request->get('candidate_uuid');
        if(empty($recruit_uuid) || empty($candidate_uuid)) {
            return $this->redirect(['index']);
        }

        $recruitCandidateMap = new RecruitCandidateMap();
        $formData = $recruitCandidateMap->getCandidate($recruit_uuid, $candidate_uuid);
        return CompressHtml::compressHtml($this->renderPartial('candidate',[
            'formData'=>$formData,
            'action'=>['/recruitment/recruitment-candidate/update-candidate'],
            'edit'=>$formData['status'] >= CandidateConfig::StatusNotifyInterView,
        ]));
    }

    public function actionUpdateCandidate() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post('CandidateForm');
        $recruitCandidateMap = new RecruitCandidateMap();
        if($recruitCandidateMap->updateSingleRecord($formData)) {
            return $this->redirect([
                'index',
                'uuid'=>$formData['recruit_uuid'],
            ]);
        }
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
        $candidate = new RecruitCandidateMap();
        $candidate->clearEmptyField($filter);
        $candidateList = $candidate->recruitmentCandidateListFilter($filter);

        return CompressHtml::compressHtml($this->renderPartial('list',[
            'candidateList'=>$candidateList,
            'ser_filter'=>serialize($filter),
            'recruit_uuid'=>$filter['recruit_uuid'],
        ]));
    }
}