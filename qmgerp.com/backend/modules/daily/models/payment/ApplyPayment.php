<?php
namespace backend\modules\daily\models\payment;
use backend\models\interfaces\PrimaryTable;
use backend\models\interfaces\RecordOperator;
use backend\modules\fin\payment\models\Payment;
use backend\modules\fin\payment\models\PaymentConfig;
use Yii;
use yii\db\Exception;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/23 0023
 * Time: 下午 10:32
 */
class ApplyPayment implements RecordOperator, PrimaryTable
{
    /**
     * status 1 表示审核通过，2表示审核不通过
     * 将审核人的uuid写入到付款申请中
     */
    public function assess($formData) {
        if(empty($formData)) {
            return true;
        }

        $payment = new Payment();
        return $payment->assess($formData);
    }

    public function insertRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }

        if(!isset($formData['type']) || empty($formData['type'])) {
            $formData['type'] = PaymentConfig::PaymentForManage;
        }
        // 初始化发票验收状态
        $formData['stamp_status'] = PaymentConfig::StampWaitCheck;
        $payment = new Payment();
        return $payment->insertRecord($formData);
    }

    public function clearEmptyField(&$formData) {
        $payment = new Payment();
        $payment->clearEmptyField($formData);
    }

    public function updateRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }

        $payment = new Payment();
        return $payment->updateRecord($formData);
    }
    
    public function applyCheckStamp($formData) {
        if(empty($formData)) {
            return true;
        }

        $uuids = explode(',', trim($formData['uuid'],','));
        // 表示多笔流水一起付款
        if(count($uuids) > 1) {
            $formData['checked_stamp_money'] = Payment::FullPaied;
        }
        $formData['stamp_status'] = PaymentConfig::StampChecking;
        $payment = new Payment();
        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach ($uuids as $uuid) {
                $formData['uuid'] = $uuid;
                $payment->applyCheckStamp($formData);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            throw  $e;
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function getRecordByUuid($uuid)
    {
        if(empty($uuid)) {
            return false;
        }

        $payment = new Payment();
        return $payment->paymentList(
            [
                'payment'=>[
                    '*'
                ],
                'created'=>[
                    'name',
                ],
                'paied'=>[
                    'name',
                ],
                'checked_stamp'=>[
                    'name',
                ],
                'stamp'=>[
                    'series_number',
                ],
                'first_assess'=>[
                    'name',
                ],
                'second_assess'=>[
                    'name'
                ],
                'third_assess'=>[
                    'name'
                ],
                'fourth_assess'=>[
                    'name'
                ],
            ],
            [
                [
                    '=',
                    $payment->aliasMap['payment'] . '.uuid',
                    $uuid
                ],
                [
                    '=',
                    $payment->aliasMap['payment'] . '.type',
                    PaymentConfig::PaymentForManage,
                ]
            ],
            true
        );
    }

    public function deleteRecordByUuid($uuid)
    {
        // TODO: Implement deleteRecordByUuid() method.
    }

    public function myPaymentList($condition = null) {
        $payment = new Payment();
        $condition = [
            $condition,
            [
                '=',
                't1.created_uuid',
                Yii::$app->user->getIdentity()->getId(),
            ],
            [
                '=',
                $payment->aliasMap['payment'] . '.type',
                PaymentConfig::PaymentForManage,
            ],
        ];
        $list = $payment->paymentList(
            [
                'payment'=>[
                    '*'
                ]
            ],
            $condition
        );
        return $list;
    }

    public function listFilter($filter) {
        $isApplyCheckStampList = isset($filter['is_apply_check_stamp_list'])?$filter['is_apply_check_stamp_list']:false;
        if($isApplyCheckStampList) unset($filter['is_apply_check_stamp_list']);
        if(empty($filter)) {
            return $this->myPaymentList($isApplyCheckStampList?[
                'and',
                [
                    '=',
                    't1.with_stamp',
                    PaymentConfig::WithStamp,
                ],
                [
                    'in',
                    't1.status',
                    [
                        PaymentConfig::StatusWithoutPaied,
                        PaymentConfig::StatusPartPaied,
                        PaymentConfig::StatusSuccess,
                    ],
                ]
            ]:null);
        }

        if(isset($filter['code']) && !empty($filter['code'])) {
            preg_match('/([a-zA-Z]*)(\d+)/',$filter['code'], $match);
            if($match[1] === PaymentConfig::CodePrefix) {
                $filter['code'] = $match[2];
            }
        }

        $payment = new Payment();
        $payment->handlerFormDataTime($filter, 'min_expect_time');
        $payment->handlerFormDataTime($filter, 'max_expect_time');
        $condition = [
            [
                '=',
                $payment->aliasMap['payment'] . '.created_uuid',
                Yii::$app->user->getIdentity()->getId(),
            ],
            [
                '=',
                $payment->aliasMap['payment'] . '.type',
                PaymentConfig::PaymentForManage,
            ]
        ];
        if($isApplyCheckStampList) {
            $condition[] = [
                'and',
                [
                    '=',
                    't1.with_stamp',
                    PaymentConfig::WithStamp,
                ],
                [
                    'in',
                    't1.status',
                    [
                        PaymentConfig::StatusWithoutPaied,
                        PaymentConfig::StatusPartPaied,
                        PaymentConfig::StatusSuccess,
                    ],
                ]
            ];
        }

        $map = [
            'code'=>[
                'like',
                'payment',
                'code',
            ],
            'purpose'=>[
                '=',
                'payment',
                'purpose'
            ],
            'with_stamp'=>[
                '=',
                'payment',
                'with_stamp'
            ],
            'status'=>[
                '=',
                'payment',
                'status'
            ],
            'stamp_status'=>[
                '=',
                'payment',
                'stamp_status',
            ],
            'receiver_account_type'=>[
                '=',
                'payment',
                'receiver_account_type'
            ],
            'min_expect_time'=>[
                '>=',
                'payment',
                'expect_time'
            ],
            'max_expect_time'=>[
                '<=',
                'payment',
                'expect_time'
            ],
            'min_money'=>[
                '>=',
                'payment',
                'actual_money'
            ],
            'max_money'=>[
                '<=',
                'payment',
                'actual_money'
            ],
        ];
        foreach($filter as $key=>$value) {
            $condition[] = [
                $map[$key][0],
                $payment->aliasMap[$map[$key][1]] . '.' . $map[$key][2],
                $value
            ];
        }

        $list = $payment->paymentList(
            [
                'payment'=>[
                    '*'
                ]
            ],
            $condition
        );
        return $list;
    }
}