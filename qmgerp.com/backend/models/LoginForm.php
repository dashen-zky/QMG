<?php

/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 2016/7/1 0001
 * Time: 下午 11:11
 */
namespace backend\models;

use Yii;
use yii\base\Model;
use backend\models\Account;

class LoginForm extends Model {
    public $username;
    public $password;
    public $captcha;
    public $rememberMe = true;
    private $_account;

    public function rules() {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params) {
        if (!$this->hasErrors()) {
            $account = $this->getAccount();
            if (!$account || !$account->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login() {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getAccount(), $this->rememberMe ? 3600*12 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getAccount() {
        if ($this->_account === null) {
            $this->_account = Account::findByUsername($this->username);
        }

        return $this->_account;
    }
}