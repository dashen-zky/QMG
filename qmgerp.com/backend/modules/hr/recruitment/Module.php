<?php
/**
 * @copyright Copyright (c) 2016 谦玛网络科技
 * @create: 5/19/16 10:52 AM
 */

namespace backend\modules\hr\recruitment;
use Yii;
use yii\filters\AccessControl;
/**
 * Class Module
 * @package backend\modules\hr
 * @author Pony Gu <pony@51wom.com>
 * @since 1.0
 */
class Module extends \yii\base\Module
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function init()
    {
        parent::init();
    }
} 