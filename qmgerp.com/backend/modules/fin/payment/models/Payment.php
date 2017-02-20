<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/23 0023
 * Time: 下午 10:29
 */

namespace backend\modules\fin\payment\models;


use backend\models\helper\file\UploadFileHelper;
use backend\models\interfaces\DeleteRecordOperator;
use backend\models\UUID;
use backend\modules\crm\models\supplier\record\SupplierPaymentMap;
use backend\modules\fin\models\FINBaseRecord;
use backend\models\MyPagination;
use Yii;
use yii\db\Exception;
use backend\modules\fin\payment\models\PaymentConfig;
use backend\modules\system\models\payment_assess_config\DailyPaymentAssessConfig;
use backend\modules\system\models\payment_assess_config\ProjectPaymentAssessConfig;
use yii\helpers\Json;
use backend\modules\hr\models\EmployeeBasicInformation;

class Payment extends FINBaseRecord implements DeleteRecordOperator
{
    const WaitingAssess = 1;
    const AssessSucceed = 2;
    const AssessRefused = 3;
    const FullPaied = 'full_paied';
    const FullChecked = 'full_checked';
    public $aliasMap;

    public function init()
    {
        $this->aliasMap = [
            'payment'=>'t1',
            'created'=>'t2',
            'first_assess'=>'t3',
            'second_assess'=>'t4',
            'third_assess'=>'t5',
            'fourth_assess'=>'t15',
            'project_payment_map'=>'t6',
            'project'=>'t7',
            'supplier_payment_map'=>'t8',
            'supplier'=>'t9',
            'part_time'=>'t10',
            'paied'=>'t11',
            'payment_stamp_map'=>'t12',
            'stamp'=>'t13',
            'checked_stamp'=>'t14',
        ];
        parent::init();
    }

    public function getAliasMap() {
        return $this->aliasMap;
    }

    public static function tableName()
    {
        return self::FinPayment;
    }

    /**
     * status 1 表示审核通过，2表示审核不通过
     * 将审核人的uuid写入到付款申请中
     */
    public function assess($formData) {
        if(empty($formData)
            || !isset($formData['uuid']) || empty($formData['uuid'])
            || !isset($formData['status']) || empty($formData['status'])) {
            return true;
        }

        $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
        if(empty($record)) {
            return true;
        }
        /**
         * 根据入口（待审核，审核通过，审核不通过）不同，选择审核通过的状态变化是不一样的
         * 在待审核页面 点击审核通过，会进入到下一级的审核中，点击审核不通过，会进入到审核拒绝的状态
         * 在审核通过的页面，点击审核通过，状态不变，点击审核不通过，会回到上一级的审核中
         * 在审核不通过的页面，点击审核通过，会进入到下一级的审核状态中，点击审核不通过，状态不变
         */
        switch($record->status) {
            case PaymentConfig::StatusWaitFirstAssess:
                $record->status =
                    ($formData['status'] == self::AssessSucceed)?
                        PaymentConfig::StatusWaitSecondAssess : PaymentConfig::StatusFirstAssessRefuse;
                $record->first_assess_uuid = Yii::$app->user->getIdentity()->getId();
            break;

            case PaymentConfig::StatusFirstAssessRefuse:
                if($formData['entrance'] == self::AssessRefused) {
                    $record->status = ($formData['status'] == self::AssessRefused)?
                        $record->status:PaymentConfig::StatusWaitSecondAssess;
                }
            break;

            case PaymentConfig::StatusWaitSecondAssess:
                if($formData['entrance'] == self::WaitingAssess) {
                    $record->status = ($formData['status'] == self::AssessSucceed)?
                        PaymentConfig::StatusWaitThirdAssess:PaymentConfig::StatusSecondAssessRefuse;
                    $record->second_assess_uuid = Yii::$app->user->getIdentity()->getId();
                    break;
                }

                if($formData['entrance'] == self::AssessSucceed) {
                    $record->status = ($formData['status'] == self::AssessSucceed)?
                        $record->status:PaymentConfig::StatusFirstAssessRefuse;
                    break;
                }
            break;

            case PaymentConfig::StatusSecondAssessRefuse:
                if($formData['entrance'] == self::AssessRefused) {
                    $record->status = ($formData['status'] == self::AssessSucceed)?
                        PaymentConfig::StatusWaitThirdAssess:$record->status;
                    break;
                }
            break;

            case PaymentConfig::StatusWaitThirdAssess:
                if($formData['entrance'] == self::WaitingAssess) {
                    $record->status = ($formData['status'] == self::AssessSucceed)?
                        PaymentConfig::StatusWaitFourthAssess:PaymentConfig::StatusThirdAssessRefuse;
                    $record->third_assess_uuid = Yii::$app->user->getIdentity()->getId();
                    break;
                }

                if($formData['entrance'] == self::AssessSucceed) {
                    $record->status = ($formData['status'] == self::AssessSucceed)?
                        $record->status:PaymentConfig::StatusSecondAssessRefuse;
                    break;
                }
            break;

            case PaymentConfig::StatusThirdAssessRefuse:
                if($formData['entrance'] == self::AssessRefused) {
                    $record->status = ($formData['status'] == self::AssessSucceed)?
                        PaymentConfig::StatusWaitFourthAssess:$record->status;
                    break;
                }
            break;

            case PaymentConfig::StatusWaitFourthAssess:
                if($formData['entrance'] == self::WaitingAssess) {
                    $record->status = ($formData['status'] == self::AssessSucceed)?
                        PaymentConfig::StatusWithoutPaied:PaymentConfig::StatusFourthAssessRefused;
                    $record->fourth_assess_uuid = Yii::$app->user->getIdentity()->getId();
                    break;
                }

                if($formData['entrance'] == self::AssessSucceed) {
                    $record->status = ($formData['status'] == self::AssessSucceed)?
                        $record->status:PaymentConfig::StatusThirdAssessRefuse;
                    break;
                }
                break;

            case PaymentConfig::StatusFourthAssessRefused:
                if($formData['entrance'] == self::AssessRefused) {
                    $record->status = ($formData['status'] == self::AssessSucceed)?
                        PaymentConfig::StatusWithoutPaied:$record->status;
                    break;
                }
                break;

            case PaymentConfig::StatusWithoutPaied:
                if($formData['entrance'] == self::AssessSucceed) {
                    $record->status = ($formData['status'] == self::AssessSucceed)?
                        $record->status:PaymentConfig::StatusFourthAssessRefused;
                    break;
                }
            break;

            default:
                return true;
        }

        $record->refuse_reason =
            isset($formData['refuse_reason'])?$formData['refuse_reason'] : '';
        $record->remarks = isset($formData['remarks'])?$formData['remarks'] : '';

        $values = $record->getDirtyAttributes();
        if(empty($values)) {
            return true;
        }
        return $record->update();
    }

    public function paymentList($selects, $conditions = null,$fetchOne = false) {
        $aliasMap = $this->aliasMap;
        $selector = [];


        if (!empty($selects)) {
            foreach($aliasMap as $key=>$alias) {
                if (isset($selects[$key])) {
                    foreach($selects[$key] as $select) {
                        if(in_array($key, [
                            'created',
                            'project',
                            'supplier',
                            'part_time',
                            'supplier_payment_map',
                            'first_assess',
                            'second_assess',
                            'third_assess',
                            'fourth_assess',
                            'paied',
                            'checked_stamp',
                        ])) {
                            $select = trim($select);
                            $selector[] = $alias ."." . $select . " " . $key . "_" .$select;
                        } elseif ($key === 'payment') {
                            $select = trim($select);
                            $selector[] = $alias ."." . $select;
                        } else {
                            $select = trim($select);
                            $selector[] = "group_concat(".$alias ."." . $select .") " . $key . "_" . $select;
                        }
                    }
                }
            }
        }

        $query = self::find()
            ->alias('t1')
            ->select($selector)
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t2', 't1.created_uuid = t2.uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t3', 't1.first_assess_uuid = t3.uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t4', 't1.second_assess_uuid = t4.uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t5', 't1.third_assess_uuid = t5.uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t15', 't1.fourth_assess_uuid = t15.uuid')
            ->leftJoin(self::CRMProjectPaymentMap . ' t6', 't1.uuid = t6.payment_uuid')
            ->leftJoin(self::CRMProject . ' t7', 't6.project_uuid = t7.uuid')
            ->leftJoin(self::CRMSupplierPaymentMap . ' t8', 't8.payment_uuid = t1.uuid')
            ->leftJoin(self::CRMSupplier . ' t9', 't9.uuid = t8.supplier_uuid and t8.supplier_type = '. SupplierPaymentMap::Supplier)
            ->leftJoin(self::CRMPartTime . ' t10', 't10.uuid = t8.supplier_uuid and t8.supplier_type = '. SupplierPaymentMap::PartTime)
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t11', 't11.uuid = t1.paied_uuid')
            ->leftJoin(self::FINPaymentStampMap . ' t12', 't12.payment_uuid = t1.uuid')
            ->leftJoin(self::FINStamp . ' t13', 't12.stamp_uuid = t13.uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t14', 't1.checked_stamp_uuid = t14.uuid');
        if(!empty($conditions)) {
            if(!is_array($conditions)) {
                $query->andWhere($conditions);
            } else {
                foreach($conditions as $key => $condition) {
                    if(empty($condition)) {
                        continue;
                    }
                    $query->andWhere($condition);
                }
            }
        }


        if ($fetchOne) {
            $record = $query->asArray()->one();
            return $record;
        }

        $pagination = new MyPagination([
            'totalCount'=>$query->count(),
            'pageSize' => self::PageSize,
        ]);
        $list = $query->orderBy('t1.created_time desc')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data = [
            'pagination' => $pagination,
            'list'=> $list,
        ];
        return $data;
    }

    public function formDataPreHandler(&$formData, $record)
    {
        if(!isset($formData['uuid']) || empty($formData['uuid'])) {
            $formData['uuid'] = UUID::getUUID();
        }
        if(empty($record)) {
            $formData['created_time'] = time();
        }

        if(isset($formData['paied_money']) && $formData['paied_money'] != self::FullPaied) {
            $formData['paied_money'] += $record->paied_money;
        }
        if(isset($formData['checked_stamp_money']) && $formData['checked_stamp_money'] != self::FullChecked) {
            $formData['checked_stamp_money'] += $record->checked_stamp_money;
        }
        $this->handlerFormDataTime($formData, 'expect_time');
        parent::formDataPreHandler($formData, $record);
    }
    
    public function recordPreHandler(&$formData, $record = null)
    {
        if(!empty($record)) {
            if($record->paied_money == self::FullPaied) {
                $record->paied_money = $record->actual_money;
            }
            if($record->paied_money && $record->paied_money == $record->actual_money) {
                $record->status = PaymentConfig::StatusSuccess;
            } elseif($record->paied_money != 0 && $record->paied_money != 0.00) {
                $record->status = PaymentConfig::StatusPartPaied;
            }

            if($record->checked_stamp_money == self::FullChecked) {
                $record->checked_stamp_money = $record->actual_money;
            }
            
            $record->assessor_remind = $this->generateAssessorRemind($record);
        } else {
            $this->assessor_remind = $this->generateAssessorRemind($this);
        }
        
        parent::recordPreHandler($formData, $record); // TODO: Change the autogenerated stub
    }


    public function generateAssessorRemind($record) {
        if ($record->type == PaymentConfig::PaymentForManage) {
            $config = (new DailyPaymentAssessConfig())->generateConfig();
            $message = '一级审核人：部门负责人;';
        } else {
            $config = (new ProjectPaymentAssessConfig())->generateConfig();
            $message = '一级审核人：项目经理;';
        }
        
        foreach ($config as $item) {
            $message .= $this->generateRemindByItem($record, $item);
        }
        return $message;
    }

    protected function generateRemindByItem($record, $item) {
        if(empty($item)) {
            return null;
        }

        $map = [
            PaymentConfig::StampCondition => 'with_stamp',
            PaymentConfig::MoneyCondition => 'assess_money',
            PaymentConfig::PurposeCondition => 'purpose',
        ];

        $_filed = null;
        foreach ($map as $key=>$field) {
            if ($item['type'] == $key) {
                $_filed = $field;
                break;
            }
        }

        $_message = null;
        $config = (new PaymentConfig())->generateConfig();
        switch ($_filed) {
            case 'with_stamp':
                $_message = $this->getAssessorByWithStamp($record->with_stamp, $item);
                break;
            case 'assess_money':
                $_message = $this->getAssessorByAssessMoney($record->actual_money, $item, $config);
                break;
            case 'purpose':
                $_message = $this->getAssessorByPurpose($record->purpose, $item, $config);
                break;
            default:
                new \yii\base\Exception('invalid filed');
                break;
        }
        if (empty($_message)) {
            return null;
        }

        switch ($item['step']) {
            case 'step-2':
                $_message = '二级审核人：' . $_message . ';';
                break;
            case 'step-3':
                $_message = '三级审核人：' . $_message . ';';
                break;
            case 'step-4':
                $_message = '四级审核人：' . $_message . ';';
                break;
        }
        return $_message;
    }

    protected function getAssessorByWithStamp($with_stamp, $item) {
        if ($with_stamp != $item['purpose']) {
            return null;
        }

        $record = $this->getEmployeeRecord($item['assess_uuid']);
        return $record->name;
    }

    protected function getAssessorByAssessMoney($money, $item, $config) {
        $money_range = $config['assess_money'];
        if (!isset($money_range[$item['purpose']])) {
            return null;
        }

        $money_range = $money_range[$item['purpose']];
        preg_match_all('/\d+\.?\d*/', $money_range, $match);
        if (empty($match)) {
            return null;
        }

        $match = $match[0];
        if (!isset($match[1]) && $money >= $match[0]) {
            $record = $this->getEmployeeRecord($item['assess_uuid']);
            return $record->name;
        }

        if ($money >= $match[0] && $money < $match[1]) {
            $record = $this->getEmployeeRecord($item['assess_uuid']);
            return $record->name;
        }

        return null;
    }

    protected function getAssessorByPurpose($purpose, $item, $config) {
        $_purpose = $config['payment_for_manage'] + $config['payment_for_project_execute'] + $config['payment_for_project_media'];
        if (empty($_purpose)) {
            return null;
        }

        foreach ($_purpose as $index => $value) {
            if ($index == $purpose && $item['purpose'] == $purpose) {
                $record = $this->getEmployeeRecord($item['assess_uuid']);
                return $record->name;
            }
        }

        return null;
    }

    protected function getEmployeeRecord($uuid) {
        return EmployeeBasicInformation::find()->where(['uuid'=>$uuid])->one();
    }

    public function insertRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }

        if(!$this->updatePreHandler($formData)) {
            return false;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->updateCodeToConfig();
            $this->insert();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw  $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    /**获取code,并且将最新的code写入到数据库
     * @return bool|int
     */
    protected function updateCodeToConfig() {
        // 将payment code存入到配置文件里面
        $paymentConfig = new PaymentConfig();
        $this->code = $paymentConfig->generatePaymentCode();
        $config = $paymentConfig->generateConfig();
        $config['payment_code'] = $this->code + 1;
        return $paymentConfig->updateDateConfigByJsonString(Json::encode($config));
    }

    public function updateRecord($formData)
    {
        if(empty($formData) || !isset($formData['uuid']) || empty($formData['uuid'])) {
            return true;
        }

        $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
        if(empty($record) || !$this->updatePreHandler($formData, $record)) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            UploadFileHelper::uploadWhileUpdate(
                $record,
                isset($formData['attachment'])?$formData['attachment']:null,
                "/upload/payment/".$formData['uuid'],
                'evidence',
                isset($formData['deleteTempFile'])?$formData['deleteTempFile']:true
            );
            $record->update();
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
        
        $transaction->commit();
        return true;
    }

    public function applyCheckStamp($formData) {
        if(empty($formData) || !isset($formData['uuid']) || empty($formData['uuid'])) {
            return true;
        }

        $record = self::find()->andWhere(['uuid' => $formData['uuid']])->one();
        if(empty($record)) {
            return true;
        }

        if(!$this->updatePreHandler($formData, $record)) {
            return true;
        }

        return $record->update();
    }

    public function deleteRecord($uuid)
    {
        // TODO: Implement deleteRecord() method.
    }

    public function pay($formData) {
        if(empty($formData)) {
            return true;
        }

        $uuids = explode(',', trim($formData['uuid'],','));
        // 表示多笔流水一起付款
        if(count($uuids) > 1) {
            $formData['paied_money'] = self::FullPaied;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $lastIndex = count($uuids) - 1;

            foreach ($uuids as $index => $uuid) {
                $formData['uuid'] = $uuid;
                $formData['paied_uuid'] = Yii::$app->user->getIdentity()->getId();
                $formData['paied_time'] = time();
                // 如果不是最后一个的话，就不需要删除临时文件
                if($index !== $lastIndex) {
                    $formData['deleteTempFile'] = false;
                } else {
                    $formData['deleteTempFile'] = true;
                }

                $this->updateRecord($formData);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
        $transaction->commit();
        return true;
    }
}