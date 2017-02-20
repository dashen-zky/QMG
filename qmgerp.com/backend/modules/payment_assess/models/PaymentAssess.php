<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/10 0010
 * Time: 下午 9:09
 */

namespace backend\modules\payment_assess\models;

use backend\modules\fin\payment\models\PaymentConfig;
use backend\modules\fin\payment\models\Payment;
use Yii;
class PaymentAssess
{
    const WaitingAssess = Payment::WaitingAssess;
    const AssessSucceed = Payment::AssessSucceed;
    const AssessRefused = Payment::AssessRefused;
    // 根据审核通过，不通过，待审核对应到payment的在一级审核，二级审核，三级审核下的不同的状态
    protected $statusMap = [
        'first' => [
            self::WaitingAssess => [
                PaymentConfig::StatusWaitFirstAssess,
            ],
            self::AssessRefused => [
                PaymentConfig::StatusFirstAssessRefuse,
            ],
            self::AssessSucceed => [
                PaymentConfig::StatusWaitSecondAssess,
                PaymentConfig::StatusSecondAssessRefuse,
                PaymentConfig::StatusWaitThirdAssess,
                PaymentConfig::StatusThirdAssessRefuse,
                PaymentConfig::StatusWaitFourthAssess,
                PaymentConfig::StatusFourthAssessRefused,
                PaymentConfig::StatusWithoutPaied,
                PaymentConfig::StatusPartPaied,
                PaymentConfig::StatusSuccess,
            ]
        ],
        'second' => [
            self::WaitingAssess => [
                PaymentConfig::StatusWaitSecondAssess,
            ],
            self::AssessRefused => [
                PaymentConfig::StatusSecondAssessRefuse,
            ],
            self::AssessSucceed => [
                PaymentConfig::StatusWaitThirdAssess,
                PaymentConfig::StatusThirdAssessRefuse,
                PaymentConfig::StatusWaitFourthAssess,
                PaymentConfig::StatusFourthAssessRefused,
                PaymentConfig::StatusWithoutPaied,
                PaymentConfig::StatusPartPaied,
                PaymentConfig::StatusSuccess,
            ]
        ],
        'third'=>[
            self::WaitingAssess => [
                PaymentConfig::StatusWaitThirdAssess,
            ],
            self::AssessRefused => [
                PaymentConfig::StatusThirdAssessRefuse,
            ],
            self::AssessSucceed => [
                PaymentConfig::StatusWaitFourthAssess,
                PaymentConfig::StatusFourthAssessRefused,
                PaymentConfig::StatusWithoutPaied,
                PaymentConfig::StatusPartPaied,
                PaymentConfig::StatusSuccess,
            ]
        ],
        'fourth'=>[
            self::WaitingAssess => [
                PaymentConfig::StatusWaitFourthAssess,
            ],
            self::AssessRefused => [
                PaymentConfig::StatusFourthAssessRefused,
            ],
            self::AssessSucceed => [
                PaymentConfig::StatusWithoutPaied,
                PaymentConfig::StatusPartPaied,
                PaymentConfig::StatusSuccess,
            ]
        ]
    ];

    /**
     * // 在审核成功的页面。是否可以审核
     * 一级审核人员在审核通过的入口里面只能再次审核 一级审核通过等待二级审核的状态
     */
    public static function canAssessInSucceedEntrance($assessor, $status) {
        for($i = count($assessor) - 1; $i > -1; $i--) {
            if($assessor[$i] == Yii::$app->user->getIdentity()->getId()) {
                break;
            }
        }
        if($i === -1) {
            return false;
        }

        $map = [
            0 => PaymentConfig::StatusWaitSecondAssess,
            1 => PaymentConfig::StatusWaitThirdAssess,
            2 => PaymentConfig::StatusWithoutPaied,
        ];
        return $status == $map[$i];
    }
}