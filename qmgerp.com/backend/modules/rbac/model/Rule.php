<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/3 0003
 * Time: 下午 10:16
 */

namespace backend\modules\rbac\model;
class Rule extends \yii\rbac\Rule
{
    public $data;
    public function execute($user, $item, $params)
    {
        $ruleManager = new RuleManager();
        $ruleName = $item->ruleName;
        return $ruleManager->$ruleName($user, $item, $params);
    }
}