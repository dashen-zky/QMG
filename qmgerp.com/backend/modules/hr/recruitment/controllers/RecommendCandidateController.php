<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-12-11
 * Time: 下午5:52
 */

namespace backend\modules\hr\recruitment\controllers;


use backend\models\BackEndBaseController;
use backend\modules\hr\recruitment\models\RecruitCandidateMap;
use Yii;
use backend\modules\hr\recruitment\models\ApplyRecruit;
use backend\modules\hr\recruitment\models\Candidate;
use backend\models\CompressHtml;

class RecommendCandidateController extends BackEndBaseController
{
    public function actionIndex() {
        return $this->render('index');
    }

    public function actionRecommendCandidate() {
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['index']);
        }

        $uuid = Yii::$app->request->get('uuid');
        if (empty($uuid)) {
            return $uuid;
        }

        return $this->render('recommend-candidate',[
            'recruit_uuid'=>$uuid,
        ]);
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
        $edit = true;

        if (empty($formData)) {
            $candidate = new Candidate();
            $formData = $candidate->getRecord($candidate_uuid);
            $edit = false;
        }
        return CompressHtml::compressHtml($this->renderPartial('candidate',[
            'formData'=>$formData,
            'action'=>['/recruitment/recommend-candidate/update-candidate'],
            'edit'=>$edit,
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
                'recommend-candidate',
                'uuid'=>$formData['recruit_uuid'],
            ]);
        }
    }

    public function actionUpdate() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post('RecommendCandidateForm');
        $recruitCandidateMap = new RecruitCandidateMap();
        if($recruitCandidateMap->updateRecord($formData)) {
            return $this->redirect(['index']);
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
        $applyRecruit = new ApplyRecruit();
        $applyRecruit->clearEmptyField($filter);
        $applyRecruitList = $applyRecruit->recommendCandidateListFilter($filter);

        return $this->render('index', [
            'applyRecruitList'=>$applyRecruitList,
            'ser_filter'=>serialize($filter),
        ]);
    }

    public function actionSelectCandidateListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $candidate = new Candidate();
        $candidate->clearEmptyField($filter);
        $recruit_uuid = $filter['recruit_uuid'];
        unset($filter['recruit_uuid']);
        $candidateList = $candidate->listFilter($filter);

        return CompressHtml::compressHtml($this->renderPartial('select-candidate-list',[
            'candidateList'=>$candidateList,
            'ser_filter'=>serialize($filter),
            'recruit_uuid'=>$recruit_uuid,
        ]));
    }
}