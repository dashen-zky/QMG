<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/5 0005
 * Time: 下午 3:13
 */

namespace backend\models\interfaces;


use yii\web\IdentityInterface;

interface MyIdentityInterface extends IdentityInterface
{
    public function getEmployeeName();
    public function getUserName();
}