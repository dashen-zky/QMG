<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/5 0005
 * Time: 下午 3:53
 */

namespace backend\models;


class PasswordForm extends BaseForm
{
    public $old_password;
    public $new_password;
    public $verify_password;

    public function rules()
    {
        return [
            // 名字和附件都需要
            [['old_password','new_password','verify_password'],'required'],
            [
                'new_password', 'compare',
                'compareAttribute' => 'old_password', 'operator' => '!==',
                'message'=>'新旧密码不应该相同',
            ],
            [
                'verify_password', 'compare',
                'compareAttribute' => 'new_password', 'operator' => '===',
                'message'=>'请保持两次输入的密码一致',
            ],

        ];
    }
}