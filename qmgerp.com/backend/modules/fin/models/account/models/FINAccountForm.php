<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/5 0005
 * Time: 下午 6:13
 */
namespace backend\modules\fin\models\account\models;
use backend\modules\fin\models\FINBaseRecord;

class FINAccountForm extends FINBaseRecord
{
    public $uuid;
    public $name;
    public $type; // 类型
    public $bank_of_deposit;  // 开户行信息
    public $account; // 账户号码
    public $object_uuid; // 是那个对象的账户
    public $typeList = [
        1=>'银行',
        2=>'支付宝',
    ];

    public static function tableName()
    {
        return self::FINAccount;
    }

    public function rules()
    {
        return [
            [['account'],'unique','message'=>'这个账号已经被别人使用了，宝宝不开心:-（'],
        ];
    }

    public function typeList() {
        return $this->typeList;
    }

    public function getType($index) {
        if(isset($this->typeList[$index])) {
            return $this->typeList[$index];
        }
    }
}