<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/1 0001
 * Time: 下午 4:56
 */

namespace backend\controllers;


use backend\models\Trigger;
use backend\modules\crm\models\customer\model\CustomerConfig;
use backend\modules\crm\models\customer\record\PrivateCustomer;
use backend\modules\crm\models\customer\record\PublicCustomer;
use backend\modules\hr\models\Position;
use backend\modules\rbac\model\PermissionManager;
use backend\modules\rbac\model\RoleManager;
use backend\modules\rbac\model\Rule;
use yii\base\ExitException;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use Yii;
use backend\modules\rbac\model\RuleManager;

class UpdateController extends Controller
{
    public function behaviors()
    {
        return [
            'assess'=>[
                'class'=>AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles'=>[
                            RoleManager::Admin,
                        ],
                    ],
                ],
            ],
        ];
    }

    // 非阻塞试编程
    public function actionEventTest() {
        $trigger = new Trigger();
        $trigger->on('ss', function ($str) {
            var_dump($str);
        });
        $trigger->on('ss', function () {
            var_dump('ddddd');
        });
        sleep(5);
        $trigger->trigger('ss', ['arg1'=>'nimeia']);
    }

    public function actionUpdate() {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            Yii::$app->db->createCommand('alter table crm_customer_basic add drop_reason text')->execute();
            Yii::$app->db->createCommand('alter table crm_project add failed_reason text')->execute();
        } catch (ExitException $e) {
            $transaction->rollBack();
            throw $e;
        }
        $transaction->commit();
        return $this->redirect(['site/index']);
    }

    public function actionUpdate2017011801() {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            Yii::$app->db->createCommand('alter table daily_week_report add next_content text')->execute();
        } catch (ExitException $e) {
            $transaction->rollBack();
            throw $e;
        }
        $transaction->commit();
        return $this->redirect(['site/index']);
    }

    public function actionUpdate20170118() {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            Yii::$app->db->createCommand('ALTER table fin_payment add fourth_assess_uuid varchar(45)')->execute();
            Yii::$app->db->createCommand('UPDATE fin_payment set fourth_assess_uuid=third_assess_uuid')->execute();
            Yii::$app->db->createCommand('alter table fin_payment add assessor_remind text')->execute();
        } catch (ExitException $e) {
            $transaction->rollBack();
            throw $e;
        }
        $transaction->commit();
        return $this->redirect(['site/index']);
    }

    public function actionUpdate20170116() {
        return ;
        Yii::$app->db->createCommand('alter table crm_project add budget_attachment text')->execute();
        return $this->redirect(['site/index']);
    }

    public function actionUpdate2016122601() {
        return ;
        Yii::$app->db->createCommand('alter table daily_regulation add enable tinyint unsigned not null default 127')->execute();
        return $this->redirect(['site/index']);
    }

    public function actionUpdate20161226() {
        $authManager = Yii::$app->authManager;
        $projectMediaBriefAssess = $authManager->createPermission(PermissionManager::ProjectMediaBriefAssess);
        $authManager->add($projectMediaBriefAssess);

        $mediaDirector = $authManager->getRole(RoleManager::MediaDirector);
        if ($authManager->canAddChild($mediaDirector, $projectMediaBriefAssess)) {
            $authManager->addChild($mediaDirector, $projectMediaBriefAssess);
        }

        return $this->redirect(['site/index']);
    }

    public function actionUpdate161220() {
        return ;
        $authManager = Yii::$app->authManager;
        // 创建审核角色

        $projectAssessor = $authManager->createRole(RoleManager::ProjectAssessor);
        $authManager->add($projectAssessor);

        // 创建审核权限，并且将审核规则指定为canAssessRecruit
        $projectAssess = $authManager->createPermission(PermissionManager::ProjectAssess);
        $authManager->add($projectAssess);

        // 将审核员和审核权限关联上
        if ($authManager->canAddChild($projectAssessor, $projectAssess)) {
            $authManager->addChild($projectAssessor, $projectAssess);
        }

        // 将审核员角色放在ceo角色下面
        $ceo = $authManager->getRole('ceo');
        if ($authManager->canAddChild($ceo, $projectAssessor)) {
            $authManager->addChild($ceo, $projectAssessor);
        }

        return $this->redirect(['site/index']);
    }

    /**
     * 添加审核招聘需求的角色以及权限
     */
    public function actionUpdate20161214() {
        return ;
        $authManager = Yii::$app->authManager;
        // 创建审核角色

        $applyRecruitAssessor = $authManager->createRole(RoleManager::applyRecruitAssessor);
        $authManager->add($applyRecruitAssessor);

//        // 创建审核规则
//        $canAssessRecruit = new Rule([
//            'name'=>RuleManager::canAssessRecruit,
//            'data'=>'判断审核人是不是招聘需求提供者的上司',
//        ]);
//        $authManager->add($canAssessRecruit);

        // 创建审核权限，并且将审核规则指定为canAssessRecruit
        $applyRecruitAssess = $authManager->createPermission(PermissionManager::applyRecruitAssess);
//        $applyRecruitAssess->ruleName = RuleManager::canAssessRecruit;
        $authManager->add($applyRecruitAssess);

        // 将审核员和审核权限关联上
        if ($authManager->canAddChild($applyRecruitAssessor, $applyRecruitAssess)) {
            $authManager->addChild($applyRecruitAssessor, $applyRecruitAssess);
        }

        // 将审核员角色放在ceo角色下面
        $ceo = $authManager->getRole('ceo');
        if ($authManager->canAddChild($ceo, $applyRecruitAssessor)) {
            $authManager->addChild($ceo, $applyRecruitAssessor);
        }

        return $this->redirect(['site/index']);
    }
    /**
     * 统计职位的在职人数
     */
    public function actionUpdate2() {
        return ;
        $sql = 'SELECT COUNT(t3.uuid) number_of_active, t1.uuid uuid FROM hr_position t1 
                LEFT JOIN hr_employee_position_map t2  on t2.position_uuid = t1.uuid 
                LEFT JOIN hr_employee_basic_information t3 on t3.uuid = t2.em_uuid AND t3.`status` in (2,3,5) 
                GROUP BY t1.uuid';
        Yii::$app->db->open();
        $records = Yii::$app->db->createCommand($sql)->queryAll();
        $position = new Position();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($records as $record) {
                $position->updateRecord([
                    'uuid'=>$record['uuid'],
                    'number_of_active'=>$record['number_of_active'],
                ]);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            var_dump($e);
            return false;
        }
        $transaction->commit();
        Yii::$app->db->close();
        return true;
    }
    /**
     * rebuild 客户的状态以及客户的级别
     */
    public function actionUpdate1() {
        return ;
        // 更新客户的状态
        $publicCustomerConfig = new CustomerConfig();
        $config = $publicCustomerConfig->generateConfig();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $customerList = PublicCustomer::find()->all();
            foreach ($customerList as $customer) {
                if(!isset($config['status'][$customer->status])) {
                    $customer->status = CustomerConfig::StatusWaitingTouch;
                    $customer->update();
                    continue;
                }
                switch ($config['status'][$customer->status]) {
                    case '待跟进':
                        $customer->status = CustomerConfig::StatusWaitingTouch;
                        break;
                    case '跟进中':
                        $customer->status = CustomerConfig::StatusTouching;
                        break;
                    case '合作中':
                        $customer->status = CustomerConfig::StatusCooperating;
                        break;
                    case '已结案':
                        $customer->status = CustomerConfig::StatusDone;
                        break;
                    default:
                        $customer->status = CustomerConfig::StatusWaitingTouch;
                        break;
                }
                $customer->update();
            }
            $customerList = PrivateCustomer::find()->all();
            foreach ($customerList as $customer) {
                if(!isset($config['level'][$customer->level])) {
                    $customer->level = CustomerConfig::PotentialLevel;
                    $customer->update();
                    continue;
                }
                switch ($config['level'][$customer->level]) {
                    case '潜在客户':
                        $customer->level = CustomerConfig::PotentialLevel;
                        break;
                    case '普通客户':
                        $customer->level = CustomerConfig::GeneralLevel;
                        break;
                    case '重点客户':
                        $customer->level = CustomerConfig::ImportantLevel;
                        break;
                    case 'KA客户':
                        $customer->level = CustomerConfig::KALevel;
                        break;
                    default:
                        $customer->level = CustomerConfig::PotentialLevel;
                        break;
                }
                $customer->update();
            }
            unset($config['status']);
            unset($config['level']);
            $publicCustomerConfig->updateDateConfigByJsonString(Json::encode($config));
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        $transaction->commit();
        return $this->redirect(['/site/index']);
    }
}