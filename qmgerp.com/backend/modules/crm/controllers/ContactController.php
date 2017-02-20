<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/16 0016
 * Time: 上午 12:03
 */

namespace backend\modules\crm\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use backend\modules\crm\models\customer\record\Contact;

class ContactController extends Controller
{
    public function actionUpdate() {
        $formData = Yii::$app->request->post('ContactForm');
        if (empty($formData)) {
            return "";
        }
        $uuid = Yii::$app->request->get('uuid');
        // 没有uuid表示是对象新建，所以不需要对map表操作
        if (!empty($uuid)) {
            // 看看是项目还是客户
            $object = Yii::$app->request->get('type');
            $formData['objectUuid'] = $uuid;
            $formData['objectType'] = $object;
        }

        $contact = new Contact();
        $uuids = $contact->updateRecord($formData);
        if ($uuids && !empty($uuids)) {
            return Json::encode($uuids);
        } else {

        }
    }
}