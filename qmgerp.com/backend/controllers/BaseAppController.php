<?php
/**
 * @copyright Copyright (c) 2016 谦玛网络科技
 * @create: 5/19/16 10:52 AM
 */
namespace backend\controllers;

use Yii;
use yii\web\Controller;

/**
 * Class BaseAppController
 * @package backend\controllers
 * @author Pony Gu <pony@51wom.com>
 * @since 1.0
 */
class BaseAppController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
//            'access' => [
//                'class' => AccessControl::className(),
//                'rules' => [
//                    [
//                        'actions' => ['login', 'error'],
//                        'allow' => true,
//                    ],
//                    [
//                        'actions' => ['logout', 'index'],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'logout' => ['post'],
//                ],
//            ],
        ];
    }
}
