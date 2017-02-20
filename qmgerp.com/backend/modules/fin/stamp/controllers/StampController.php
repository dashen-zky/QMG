<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/19 0019
 * Time: 下午 9:06
 */

namespace backend\modules\fin\stamp\controllers;


use backend\models\BackEndBaseController;
use backend\modules\fin\stamp\models\Stamp;
use Yii;
use yii\web\UploadedFile;
use backend\models\UploadForm;
use backend\models\CompressHtml;
class StampController extends BackEndBaseController
{
    protected $stamp;

    public function index($stampListFunction) {
        if(Yii::$app->request->isAjax) {
            return $this->pagination($stampListFunction);
        }
        $stampList = $this->stamp->$stampListFunction();
        return $this->render('index',[
            'stampList' => $stampList,
        ]);
    }

    public function pagination($stampListFunction) {
        if(!Yii::$app->request->isAjax) {
            return null;
        }
        $stampList = $this->stamp->$stampListFunction();
        return CompressHtml::compressHtml($this->renderPartial('list', [
            'stampList'=>$stampList,
        ]));
    }
    
    public function actionUpdate() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post('StampForm');
        $model = New UploadForm();
        $formData['attachment'] = UploadedFile::getInstances($model, 'file');
        $stamp = new Stamp();
        if($stamp->updateRecord($formData)) {
            return $this->redirect(['index']);
        }
    }

    public function show($action) {
        if(!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return $this->redirect(['index']);
        }

        $stamp = new Stamp();
        $formData = $stamp->getRecordFromUuid($uuid);
        return CompressHtml::compressHtml($this->renderPartial('form',[
            'formData'=>$formData,
            'show'=>true,
            'action'=>$action,
        ]));
    }

    public function actionDisable() {
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['index']);
        }
        
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return $this->redirect(['index']);
        }
        
        $stamp = new Stamp();
        if($stamp->disable($uuid)) {
            return $this->redirect(['index']);
        }
    }

    public function actionAttachmentDownload() {
        $path = Yii::$app->request->get("path");
        $path = iconv("UTF-8", "GBK", $path);
        if (empty($path)) {
            $this->redirect(['index']);
        }
        $file_name = Yii::$app->request->get('file_name');
        $path = Yii::getAlias("@app") . $path;
        Yii::$app->response->sendFile($path, $file_name);
    }

    public function actionAttachmentDelete() {
        if(Yii::$app->request->isAjax) {
            $uuid = Yii::$app->request->get('uuid');
            $path = Yii::$app->request->get('path');
            $stamp = new Stamp();
            return $stamp->deleteAttachment($uuid, $path);
        }
    }

    public function actionListFilter()
    {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }

        $this->stamp->clearEmptyField($filter);
        $stampList = $this->stamp->listFilter($filter);
        return CompressHtml::compressHtml($this->renderPartial('list',[
            'stampList'=>$stampList,
            'ser_filter'=>serialize($filter),
        ]));
    }
}