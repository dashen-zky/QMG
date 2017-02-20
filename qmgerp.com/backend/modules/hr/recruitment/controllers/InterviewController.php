<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-12-13
 * Time: 下午5:14
 */

namespace backend\modules\hr\recruitment\controllers;


use backend\models\BackEndBaseController;
use Yii;
use backend\modules\hr\recruitment\models\RecruitCandidateMap;
use backend\models\CompressHtml;

class InterviewController extends BackEndBaseController
{
    public function actionIndex() {
        return $this->render('index');
    }

    public function actionHrIndex() {
        return $this->render('hr-index');
    }

    public function actionAssessIndex() {
        return $this->render('assess-index');
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
            'action'=>['/recruitment/interview/update-candidate'],
            'edit'=>true,
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
            ]);
        }
    }

    public function actionHire() {
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['assess-index']);
        }

        $id = Yii::$app->request->get('id');
        if (empty($id)) {
            return $this->redirect(['assess-index']);
        }
        $candidate = new RecruitCandidateMap();
        if($candidate->hire($id)) {
            return $this->redirect(['assess-index']);
        }
    }

    public function actionInterviewCancel() {
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['hr-index']);
        }

        $id = Yii::$app->request->get('id');
        if(empty($id)) {
            return $this->redirect(['hr-index']);
        }

        $candidate = new RecruitCandidateMap();
        if($candidate->interViewCancel($id)) {
            return $this->redirect(['hr-index']);
        }
    }

    public function actionDisHire() {
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['assess-index']);
        }

        $id = Yii::$app->request->get('id');
        if (empty($id)) {
            return $this->redirect(['assess-index']);
        }

        $candidate = new RecruitCandidateMap();
        if($candidate->disHire($id)) {
            return $this->redirect(['assess-index']);
        }
    }

    public function actionPushToTalent() {
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['assess-index']);
        }

        $id = Yii::$app->request->get('id');
        if (empty($id)) {
            return $this->redirect(['assess-index']);
        }

        $candidate = new RecruitCandidateMap();
        if($candidate->pushToTalent($id)) {
            return $this->redirect(['assess-index']);
        }
    }

    public function actionPushToBlackList() {
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['assess-index']);
        }

        $id = Yii::$app->request->get('id');
        if (empty($id)) {
            return $this->redirect(['assess-index']);
        }

        $candidate = new RecruitCandidateMap();
        if($candidate->pushToBlackList($id)) {
            return $this->redirect(['assess-index']);
        }
    }

    public function actionMyListFilter() {
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
        $candidateList = $candidate->myInterviewCandidateListFilter($filter);

        return CompressHtml::compressHtml($this->renderPartial('list',[
            'candidateList'=>$candidateList,
            'ser_filter'=>serialize($filter),
        ]));
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
        $candidate = new RecruitCandidateMap();
        $candidate->clearEmptyField($filter);
        $candidateList = $candidate->assessInterviewCandidateListFilter($filter);

        return CompressHtml::compressHtml($this->renderPartial('assess-list',[
            'candidateList'=>$candidateList,
            'ser_filter'=>serialize($filter),
        ]));
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
        $candidateList = $candidate->interviewCandidateListFilter($filter);

        return CompressHtml::compressHtml($this->renderPartial('list',[
            'candidateList'=>$candidateList,
            'ser_filter'=>serialize($filter),
        ]));
    }
}