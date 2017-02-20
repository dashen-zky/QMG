<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/2 0002
 * Time: 下午 11:25
 */

namespace backend\models;

use backend\models\interfaces\MyIdentityInterface;
use backend\models\interfaces\RecordOperator;
use backend\modules\hr\models\EmployeeAccount;
use backend\modules\hr\models\EmployeeBasicInformation;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $em_uuid
 * @property string $role_uuid
 * @property string $auth_key
 * @property string $access_token
 */
class Account extends BaseRecord implements MyIdentityInterface,RecordOperator
{
    const STATUS_WAIT_ENTRY = EmployeeAccount::STATUS_WAIT_ENTRY;// 待入职
    const STATUS_INTERN = EmployeeAccount::STATUS_INTERN; // 试用期
    const STATUS_ACTIVE = EmployeeAccount::STATUS_ACTIVE; // 在职
    const STATUS_LEAVED = EmployeeAccount::STATUS_LEAVED; // 离职
    const STATUS_TRAINEE = EmployeeAccount::STATUS_TRAINEE;
    public $name;
    // 设置数据表的名字
    static public function tableName() {
        return self::EmployeeAccountTableName;
    }

    // 通过username 查找数据库找寻到相关的信息
    public static function findByUsername($username) {
        return static::find()->andWhere([
            'and',
            'username="'.$username.'"',
            [
                'or',
                'status='.self::STATUS_INTERN,
                'status='.self::STATUS_ACTIVE,
                'status='.self::STATUS_TRAINEE,
            ]
        ])->one();
    }

    // 验证密码是否正确
    public function validatePassword($password) {
        $password_hash = md5($password);
        return Yii::$app->security->compareString($password_hash, $this->password);
    }

    public static function findIdentity($id) {
        return static::find()->andWhere([
            'and',
            "em_uuid='".$id."'",
            [
                'or',
                'status='.self::STATUS_ACTIVE,
                'status='.self::STATUS_INTERN,
                'status='.self::STATUS_TRAINEE,
            ]
        ])->one();
    }

    public function getId()
    {
        return isset($this->em_uuid) ? $this->em_uuid : null;
    }

    public function getEmployeeName()
    {
        $record = self::find()
            ->alias('t1')
            ->select([
                't2.name name'
            ])
            ->leftJoin(EmployeeBasicInformation::$tableName . ' t2','t1.em_uuid=t2.uuid')
            ->andWhere([
                't1.em_uuid'=>$this->em_uuid
            ])
            ->one();

        return isset($record->name) ? $record->name : null;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function getUserName() {
        return isset($this->username) ? $this->username : null;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function generateAuthKey()
    {
        $this->auth_key = md5($this->em_uuid);
    }

    /**
     * 根据 token 查询身份。
     *
     * @param string $token 被查询的 token
     * @return IdentityInterface|null 通过 token 得到的身份对象
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public function updateRecord($formData)
    {
        if(empty($formData) || !isset($formData['em_uuid']) || empty($formData['em_uuid'])) {
            return false;
        }
        $record = self::find()->andWhere(['em_uuid'=>$formData['em_uuid']])->one();
        if($formData['old_password'] != $record->password) {
            return false;
        }

        if(!parent::updatePreHandler($formData, $record)) {
            return false;
        }

        return $record->update();
    }

    public function insertRecord($formData)
    {
        // TODO: Implement insertRecord() method.
    }
}