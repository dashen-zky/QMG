<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/22 0022
 * Time: 下午 10:08
 */

namespace backend\modules\system\controllers;


use backend\models\BackEndBaseController;
use Yii;
use yii\helpers\Json;
use backend\modules\hr\models\EmployeeBasicInformation;
use backend\models\CompressHtml;
use backend\modules\fin\payment\models\PaymentConfig;
use yii\bootstrap\Html;
class PaymentAssessConfigController extends BackEndBaseController
{
    public $paymentAssessConfig;
    public $viewDir;

    public function actionIndex() {
        $config = $this->paymentAssessConfig->generateConfigForShow();
        $tab = $this->getTab(Yii::$app->request->get('tab'), 'step-2');
        return $this->render($this->viewDir.'index',[
            'config'=>$config,
            'tab'=>$tab,
        ]);
    }

    /**
     * 将流程配置信息存入到com_config表中
     * key 值为'daily-payment-assess-config'
     */
    public function actionAdd() {
        if(!Yii::$app->request->isPost) {
            return false;
        }

        $formData = Yii::$app->request->post('PaymentAssessConfigForm');
        if(empty($formData)) {
            return false;
        }

        $config = $this->paymentAssessConfig->generateConfig();
        $config[] = $formData;

        if($this->paymentAssessConfig->updateDateConfigByJsonString(Json::encode($config), $this->paymentAssessConfig->key)) {
            return $this->redirect([
                'index',
                'tab'=>$formData['step'],
            ]);
        }
    }

    public function actionDel() {
        if(!Yii::$app->request->isGet) {
            return false;
        }

        $id = Yii::$app->request->get('id');
        $config = $this->paymentAssessConfig->generateConfig();
        unset($config[$id]);
        if($this->paymentAssessConfig->updateDateConfigByJsonString(Json::encode($config), $this->paymentAssessConfig->key)) {
            return $this->redirect([
                'index',
                'tab'=>$this->getTab(Yii::$app->request->get('tab'), 'step-2'),
            ]);
        }
    }

    public function actionEmployeeList() {
        $employeeList = (new EmployeeBasicInformation())->allEmployeeList(false);
        $uuids = Yii::$app->request->get('uuids');
        $uuids = explode(',', trim($uuids, ','));
        return CompressHtml::compressHtml($this->renderPartial('@hr/views/employee/employee-select-list-advance.php',[
            'employeeList'=>$employeeList,
            'uuids'=>$uuids,
        ]));
    }

    public function actionChooseConditionType() {
        if(!Yii::$app->request->isAjax) {
            return null;
        }
        $type = Yii::$app->request->get('type');
        if(empty($type)) {
            return null;
        }
        $paymentConfig = new PaymentConfig();
        $map = $paymentConfig->getList('map');
        if(!isset($map[$type])) {
            return null;
        }
        $list = [];
        // 如果map【type】是数组表明这是一个通过'用途'来删选条件的
        // 不同的入口，筛选出来的条件也是不一样的
        if(is_array($map[$type])) {
            $entrance = Yii::$app->request->get('entrance');
            if(empty($entrance) || !isset($map[$type][$entrance])) {
                return null;
            }
            // 如果$map[$type][$entrance]是数组，表明是有多个配置字段组成的
            if(is_array($map[$type][$entrance])) {
                foreach($map[$type][$entrance] as $item) {
                    $list += $paymentConfig->getList($item);
                }
            } else {
                $list = $paymentConfig->getList($map[$type][$entrance]);
            }
        }

        if(empty($list)) {
            $list = $paymentConfig->getList($map[$type]);
        }

        $html = '';
        foreach($list as $index=>$item) {
            $html .= '<div class="col-md-3">' . Html::radio('PaymentAssessConfigForm[purpose]', false, [
                    'value'=>$index,
                ]). $item . '</div>';
        }

        return $html;
    }
}

