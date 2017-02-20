<?php
namespace backend\modules\fin\stamp\controllers;
use backend\modules\fin\stamp\models\ImportStamp;
use backend\models\UploadForm;
use yii\web\UploadedFile;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/17 0017
 * Time: 上午 11:07
 */
use Yii;
use backend\models\CompressHtml;
class ImportStampController extends StampController
{
    public function init()
    {
        $this->stamp = new ImportStamp();
    }

    public function actionIndex() {
        return $this->index('importStampList');
    }

    public function actionAdd() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post('StampForm');
        $model = New UploadForm();
        $formData['attachment'] = UploadedFile::getInstances($model, 'file');
        $importStamp = new ImportStamp();
        $flag = $importStamp->insertStamp($formData);
        if($flag === ImportStamp::SeriesValidateError) {
            $stampList = $importStamp->importStampList();
            return $this->render('index',[
                'formData'=>$formData,
                'series_validate_error'=>true,
                'tab'=>'add',
                'stampList' => $stampList,
            ]);
        } elseif($flag) {
            return $this->redirect(['index']);
        }
    }

    public function actionShow() {
        return $this->show(['/stamp/import-stamp/update']);
    }

    public function actionSelectListFilter()
    {
        $checked = [];
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
            $checked = explode(',', trim($filter['stamp_uuid'], ', '));
            unset($filter['stamp_uuid']);
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $stamp = new ImportStamp();
        $stamp->clearEmptyField($filter);
        $stampList = $stamp->listFilter($filter);
        return CompressHtml::compressHtml($this->renderPartial('stamp-select-list',[
            'stampList'=>$stampList,
            'ser_filter'=>serialize($filter),
            'checked'=>$checked
        ]));
    }
}