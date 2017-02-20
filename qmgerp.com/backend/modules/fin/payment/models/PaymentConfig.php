<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/21 0021
 * Time: 下午 12:02
 */
namespace backend\modules\fin\payment\models;
use backend\models\Config;
use backend\modules\fin\models\account\models\FINAccountForm;
use yii\helpers\Json;

class PaymentConfig  extends Config
{
    protected $extraConfig;
    const CodePrefix =  'M';
    const WithStamp = 3;
    const WithoutStamp = 4;
    // 作为付款申请的财务审核流程的配置条件，
    const StampCondition = 5;
    const MoneyCondition = 6;
    const PurposeCondition = 7;
    const StatusSave = 8; // 保存未提交
    const StatusWaitFirstAssess = 9; // 申请进入等待一级审核
    const StatusFirstAssessRefuse = 10; // 一级审核未通过
    const StatusWaitSecondAssess = 11; // 一级审核通过进入等待二级审核
    const StatusSecondAssessRefuse = 12; // 二级审核未通过
    const StatusWaitThirdAssess = 13; // 二级审核通过进入三级审核
    const StatusThirdAssessRefuse = 14; // 三级审核未通过
    const StatusWithoutPaied = 15; //三级审核通过进入未付款阶段
    const StatusPartPaied = 16;// 部门付款
    const StatusSuccess = 17; // 全额付款，表示申请付款流程成功
    const StatusWaitFourthAssess = 18; //　三级审核通过进入四级审核
    const StatusFourthAssessRefused = 19; // 四级审核未通过

    const StatusGapPass = 2; // 两级之间的gap为2，在审核过程中，状态加这个gap,表示审核通过
    const StatusGapRefuse = 1; // 等待与拒绝之间的gap为1，在审核过程中，状态加这个gap,表示审核不通过
    const PaymentForManage = 18;
    const PaymentForProjectExecute = 19;
    const PaymentForProjectMedia = 20;
    const StampWaitCheck = 21;
    const StampChecking = 22;
    const StampChecked = 23;
    public function init()
    {
        $finAccountForm = new FINAccountForm();
        $this->key = 'payment_config';
        $this->extraConfig = [
            'receiver_account_type'=>$finAccountForm->typeList(),
            'assess_condition'=>[
                self::StampCondition => '发票',
                self::MoneyCondition => '金额',
                self::PurposeCondition => '款项用途',
            ],
            'with_stamp'=>[
                self::WithStamp => '有发票',
                self::WithoutStamp => '无发票',
            ],
            'map'=>[
                self::StampCondition => 'with_stamp',
                self::MoneyCondition => 'assess_money',
                self::PurposeCondition => [
                    'daily' => 'payment_for_manage',
                    // 项目的付款申请有两个入口
                    'project' => [
                        'payment_for_project_execute',
                        'payment_for_project_media',
                    ],
                ]
            ],
            'filed_map'=>[
                self::StampCondition => 'with_stamp',
                self::MoneyCondition => 'actual_money',
                self::PurposeCondition => 'purpose',
            ],
            'status'=>[
                self::StatusSave => '保存未申请',
                self::StatusWaitFirstAssess => '等待部门审批',
                self::StatusFirstAssessRefuse => '部门审批未通过',
                self::StatusWaitSecondAssess => '等待财务审批',
                self::StatusSecondAssessRefuse => '财务审批未通过',
                self::StatusWaitThirdAssess => '等待业务负责人审批',
                self::StatusThirdAssessRefuse => '业务负责人审批未通过',
                self::StatusWaitFourthAssess => '等待公司负责人审批',
                self::StatusFourthAssessRefused => '公司负责人审批未通过',
                self::StatusWithoutPaied => '待付款',
                self::StatusPartPaied => '部分付款',
                self::StatusSuccess => '全部付款',
            ],
            'stamp_status' => [
                self::StampWaitCheck => '发票未验收',
                self::StampChecking => '发票验收中',
                self::StampChecked => '发票验收通过',
            ],
            'type'=>[
                self::PaymentForManage => '管理费用',
                self::PaymentForProjectExecute => '项目执行成本',
                self::PaymentForProjectMedia => '项目媒介成本',
            ],
            'type_purpose_map'=>[
                self::PaymentForManage => 'payment_for_manage',
                self::PaymentForProjectMedia => 'payment_for_project_media',
                self::PaymentForProjectExecute => 'payment_for_project_execute',
            ]
        ];
    }
    public function updateConfig($formData)
    {
        if(empty($formData)) {
            return false;
        }
        $config = $this->generateConfig();
        foreach($formData as $key => $item) {
            if(isset($config[$key])) unset($config[$key]);
            $value = [];
            foreach ($item as $k => $itemValue) {
                if (empty($itemValue) || $itemValue === null
                    || !isset($itemValue['key']) || empty($itemValue['key'])) {
                    unset($value[$k]);
                    continue;
                }
                $value[$itemValue['key']] = $itemValue['value'];
            }
            $config[$key] = $value;
        }

        return $this->updateRecord([
            'uuid' => md5($this->key),
            'config'=>Json::encode($config),
            'uuid_key'=>$this->key,
        ]);
    }

    // 生成payment的code
    public function generatePaymentCode() {
        if(empty($this->config)) {
            $this->config = $this->generateConfig();
        }
        $payment_code = date('Ymd', time()) . '0001';
        return (isset($this->config['payment_code']) && $this->config['payment_code'] >= $payment_code) ?
            $this->config['payment_code']:$payment_code;
    }

    public function getList($key)
    {
        $list =  parent::getList($key);
        if(empty($list)) {
            $list = isset($this->extraConfig[$key])?$this->extraConfig[$key]:[];
        }
        return $list;
    }

    public function getAppointed($key, $index)
    {
        $value =  parent::getAppointed($key, $index);
        if(empty($value)) {
            $value =  isset($this->extraConfig[$key][$index])?$this->extraConfig[$key][$index]:'';
        }
        return $value;
    }
}