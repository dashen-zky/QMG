<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/17 0017
 * Time: 上午 11:09
 */

namespace backend\modules\fin\stamp\controllers;
use backend\modules\fin\stamp\models\ExportStamp;
use Yii;
use backend\models\UploadForm;
use yii\web\UploadedFile;
use backend\models\CompressHtml;
class ExportStampController extends StampController
{
    public function init()
    {
        $this->stamp = new ExportStamp();
    }

    public function actionIndex() {
        return $this->index('exportStampList');
    }

    public function actionDisable() {
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['index']);
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return $this->redirect(['index']);
        }

        $stamp = new ExportStamp();
        if($stamp->disable($uuid)) {
            return $this->redirect(['index']);
        }
    }

    public function actionShow() {
        return $this->show(['/stamp/export-stamp/update']);
    }

    public function actionAdd() {
        if(Yii::$app->request->isAjax) {
            return $this->actionAjaxAdd();
        }

        $formData = Yii::$app->request->post('StampForm');
        $exportStamp = new ExportStamp();
        $model = New UploadForm();
        $formData['attachment'] = UploadedFile::getInstances($model, 'file');
        $flag = $exportStamp->insertStamp($formData);
        if($flag === ExportStamp::SeriesValidateError) {
            unset($formData['attachment']);
            $stampList = $exportStamp->exportStampList();
            return $this->render('index',[
                'formData'=>$formData,
                'series_validate_error'=>true,
                'tab'=>'add',
                'stampList'=>$stampList,
            ]);
        } elseif($flag) {
            return $this->redirect(['index']);
        }
    }

    public function actionAjaxAdd() {
        $formData = Yii::$app->request->post('StampForm');
        $model = New UploadForm();
        $formData['attachment'] = UploadedFile::getInstances($model, 'file');
        $exportStamp = new ExportStamp();
        return $exportStamp->ajaxInsertStamp($formData);
    }
}