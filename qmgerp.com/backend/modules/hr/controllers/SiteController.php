<?php
/**
 * @copyright Copyright (c) 2016 谦玛网络科技
 * @create: 5/19/16 10:52 AM
 * johnny
 */
namespace backend\modules\hr\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
