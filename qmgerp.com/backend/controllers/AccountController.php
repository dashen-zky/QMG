<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/5 0005
 * Time: 下午 3:42
 */

namespace backend\controllers;

use backend\models\Account;
use Yii;
use backend\models\PasswordForm;
use yii\web\Controller;

class AccountController extends Controller
{
    public function actionModifyPassword() {
        $model = new PasswordForm();
        return $this->render('modify-password',[
            'model'=>$model,
        ]);
    }

    public function actionPasswordUpdate() {
        if(Yii::$app->request->isPost) {
            $model = new PasswordForm();
            $formData = Yii::$app->request->post();

            if($model->load($formData) && $model->validate()) {
                $account = new Account();
                if($account->updateRecord([
                    'old_password'=>md5($formData['PasswordForm']['old_password']),
                    'password'=>md5($formData['PasswordForm']['new_password']),
                    'em_uuid'=>Yii::$app->user->getIdentity()->getId(),
                ])) {
                    $this->redirect(['/site/index']);
                } else {
                    $model->addError('old_password','跟新密码失败，输入的密码不正确');
                    return $this->render('modify-password',[
                        'model'=>$model,
                    ]);
                }
            } else {
                return $this->render('modify-password',[
                    'model'=>$model,
                ]);
            }
        }
    }
}