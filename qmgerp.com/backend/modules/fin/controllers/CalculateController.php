<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-12-27
 * Time: 下午4:47
 */

namespace backend\modules\fin\controllers;


use backend\models\BackEndBaseController;

class CalculateController extends BackEndBaseController
{
    public function actionIndex() {
        $this->layout = 'calculate';
        return $this->render('index');
    }
}