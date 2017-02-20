<?php
/**
 * @copyright Copyright (c) 2016 谦玛网络科技
 * @create: 5/19/16 10:52 AM
 */

namespace backend\modules\payment_assess;

/**
 * Class Module
 * @package backend\modules\crm
 * @author Pony Gu <pony@51wom.com>
 * @since 1.0
 */
use Yii;
use yii\filters\AccessControl;
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