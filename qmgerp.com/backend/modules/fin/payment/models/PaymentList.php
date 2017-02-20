<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/12 0012
 * Time: 上午 12:34
 */

namespace backend\modules\fin\payment\models;


use backend\modules\crm\models\project\model\ProjectForm;
use backend\models\BaseRecord;
use backend\models\MyPagination;
use backend\modules\system\models\payment_assess_config\PayConfig;
use Yii;
use backend\modules\crm\models\supplier\model\SupplierForm;
use backend\modules\crm\models\part_time\model\PartTimeForm;

class PaymentList
{
    private  $aliasMap = [
        'payment' => 't1',
        'project_payment_map'=>'t2',
        'project'=>'t3',
        'created'=>'t4',
        'supplier_payment_map'=>'t5',
        'supplier'=>'t6',
        'part_time'=>'t7',
    ];
    const ProjectPaymentEntrance = 100;
    const IsCheckStampEntrance = 101;

    public function checkStampList() {
        return $this->paymentAssessList(
            [
                'payment'=>[
                    '*'
                ],
                'project'=>[
                    'code',
                    'name'
                ],
                'created'=> [
                    'name'
                ]
            ],
            [
                'and',
                [
                    '=',
                    $this->aliasMap['payment'] . '.with_stamp',
                    PaymentConfig::WithStamp,
                ],
                [
                    'in',
                    $this->aliasMap['payment'] . '.status',
                    [
                        PaymentConfig::StatusWithoutPaied,
                        PaymentConfig::StatusPartPaied,
                        PaymentConfig::StatusSuccess,
                    ]
                ],
                [
                    'in',
                    $this->aliasMap['payment'] . '.stamp_status',
                    [
                        PaymentConfig::StampChecking,
                        PaymentConfig::StampChecked,
                    ]
                ]
            ]
        );
    }

    /**
     * 一级审核
     * 根据人找到负责的部门，
     * 将所有负责部门的人找出来
     * 将这些人的付款申请的列表列出来
     * 二/三级审核
     * 根据系统配置的信息，
     * 得到付款申请的列表
     *
     */
    public function assessList($entrance = 0) {
        $condition = $this->buildCondition($entrance);
        return $this->paymentAssessList(
            [
                'payment'=>[
                    '*'
                ],
                'project'=>[
                    'code',
                    'name'
                ],
                'created'=> [
                    'name'
                ]
            ],
            $condition
        );
    }

    public function moneyStatistic() {
        return Payment::find()->select([
            'sum(actual_money) actual_money_amount',
            'sum(paied_money) paied_money_amount'
        ])->asArray()->one();
    }

    protected function buildCondition($entrance) {
        switch ($entrance) {
            case self::IsCheckStampEntrance:
                return [
                    'and',
                    [
                        '=',
                        $this->aliasMap['payment'] . '.with_stamp',
                        PaymentConfig::WithStamp,
                    ],
                    [
                        'in',
                        $this->aliasMap['payment'] . '.status',
                        [
                            PaymentConfig::StatusWithoutPaied,
                            PaymentConfig::StatusPartPaied,
                            PaymentConfig::StatusSuccess,
                        ]
                    ],
                    [
                        'in',
                        $this->aliasMap['payment'] . '.stamp_status',
                        [
                            PaymentConfig::StampChecking,
                            PaymentConfig::StampChecked,
                        ]
                    ]
                ];
            case self::ProjectPaymentEntrance:
                return [
                    'and',
                    [
                        'in',
                        $this->aliasMap['payment'] . '.status',
                        [
                            PaymentConfig::StatusWithoutPaied,
                            PaymentConfig::StatusPartPaied,
                            PaymentConfig::StatusSuccess,
                        ]
                    ],
                    [
                        'in',
                        $this->aliasMap['payment'] . '.type',
                        [
                            PaymentConfig::PaymentForProjectExecute,
                            PaymentConfig::PaymentForProjectMedia,
                        ]
                    ]
                ];
            default:
                return [
                    'in',
                    $this->aliasMap['payment'] . '.status',
                    [
                        PaymentConfig::StatusWithoutPaied,
                        PaymentConfig::StatusPartPaied,
                        PaymentConfig::StatusSuccess,
                    ]
                ];
        }
    }


    public  function canPay($record) {
        $condition = $this->buildPaymentCondition();
        foreach ($condition as $item) {
            if(!isset($record[$item[1]])) {
                continue;
            }

            switch ($item[0]) {
                case '==':
                    if($record[$item[1]] == $item[2] && in_array($record['status'], [
                            PaymentConfig::StatusWithoutPaied,
                            PaymentConfig::StatusPartPaied
                        ])) {
                        return true;
                    }
                    break;
                case '>':
                    if($record[$item[1]] > $item[2] && in_array($record['status'], [
                            PaymentConfig::StatusWithoutPaied,
                            PaymentConfig::StatusPartPaied
                        ])) {
                        return true;
                    }
                    break;
                case '<':
                    if($record[$item[1]] < $item[2] && in_array($record['status'], [
                            PaymentConfig::StatusWithoutPaied,
                            PaymentConfig::StatusPartPaied
                        ])) {
                        return true;
                    }
                    break;
                case '!=':
                    if($record[$item[1]] != $item[2] && in_array($record['status'], [
                            PaymentConfig::StatusWithoutPaied,
                            PaymentConfig::StatusPartPaied
                        ])) {
                        return true;
                    }
                    break;
            }
        }

        return false;
    }

    protected  function buildPaymentCondition() {
        $payConfig = new PayConfig();
        $config = $payConfig->generateConfigForShow();
        $condition = [];
        foreach($config as $item) {
            if(!$this->isAssessor($item['assess_uuid'])) {
                continue;
            }
            $condition[] = $this->transformCondition($item);
        }

        return $condition;
    }

    protected  function isAssessor($assessUuid) {
        if(strpos($assessUuid, Yii::$app->getUser()->getIdentity()->getId()) !== false) {
            return true;
        }

        return false;
    }

    protected  function transformCondition($condition) {
        if(empty($condition)) {
            return null;
        }

        $_condition = [];

        $paymentConfig = new PaymentConfig();
        $field_map = $paymentConfig->getList('filed_map');
        $field = $field_map[$condition['type']];
        switch($field) {
            case 'with_stamp':
            case 'purpose':
                $_condition = [
                    '==',
                    $field,
                    $condition['purpose'],
                ];
                break;

            // 金额是个区级，大于小的，小于大的
            case 'actual_money':
                preg_match_all('/\d+\.?\d*/', $condition['condition_item'], $match);
                if(empty($match)) {
                    break;
                }

                $_condition = [
                    '>',
                    $field,
                    $match[0][0],
                ];
                // 表明有个最大值
                if(isset($match[0][1])) {
                    $_condition = [$_condition];
                    array_unshift($_condition, 'and');
                    $_condition[] = [
                        '<',
                        $field,
                        $match[0][1],
                    ];
                }
                break;

            default:
                break;
        }

        return $_condition;
    }

    public function paymentAssessList($selects = null, $condition = null) {
        $selector = [];
        if (!empty($selects)) {
            foreach($this->aliasMap as $key => $alias) {
                if (isset($selects[$key])) {
                    foreach($selects[$key] as $select) {
                        if(in_array($key, [
                            'project',
                            'created',
                            'supplier',
                            'part_time'
                        ])) {
                            $select = trim($select);
                            $selector[] = $alias ."." . $select . " " . $key . "_" .$select;
                        } elseif ($key === 'payment') {
                            $select = trim($select);
                            $selector[] = $alias ."." . $select;
                        }
                    }
                }
            }
        }
        // 得到这个人所负责的所有的部门的uuid
        $query = Payment::find()
            ->alias('t1')
            ->select($selector)
            ->leftJoin(BaseRecord::CRMProjectPaymentMap . ' t2', 't1.uuid = t2.payment_uuid')
            ->leftJoin(BaseRecord::CRMProject . ' t3', 't2.project_uuid = t3.uuid')
            ->leftJoin(BaseRecord::EmployeeBasicInformationTableName . ' t4', 't1.created_uuid = t4.uuid')
            ->leftJoin(BaseRecord::CRMSupplierPaymentMap . ' t5', 't1.uuid = t5.payment_uuid')
            ->leftJoin(BaseRecord::CRMSupplier . ' t6', 't6.uuid = t5.supplier_uuid')
            ->leftJoin(BaseRecord::CRMPartTime . ' t7', 't7.uuid = t5.supplier_uuid');
        if(!empty($condition)) {
            $query->andWhere($condition);
        }
        $pagination = new MyPagination([
            'totalCount'=>$query->count(),
            'pageSize'=>BaseRecord::PageSize,
        ]);
        $list = $query->orderBy([
            't1.status' => SORT_ASC,
            't1.created_time' => SORT_ASC,
        ])->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();

        return [
            'list'=>$list,
            'pagination'=>$pagination,
        ];
    }

    public function listFilter($filter) {
        $entrance = isset($filter['entrance'])?$filter['entrance']:0;
        unset($filter['entrance']);
        if(empty($filter)) {
            return $this->assessList($entrance);
        }

        if(isset($filter['code']) && !empty($filter['code'])) {
            preg_match('/([a-zA-Z]*)(\d+)/',$filter['code'],$match);
            if($match[1] === PaymentConfig::CodePrefix) {
                $filter['code'] = $match[2];
            }
        }

        if(isset($filter['project_code']) && !empty($filter['project_code'])) {
            preg_match('/([a-zA-Z]*)(\d+)/',$filter['project_code'],$match);
            if($match[1] === ProjectForm::codePrefix) {
                $filter['project_code'] = $match[2];
            }
        }

        if(isset($filter['supplier_code']) && !empty($filter['supplier_code'])) {
            preg_match('/([a-zA-Z]*)(\d+)/',$filter['supplier_code'],$match);
            if(in_array($match[1],[
                SupplierForm::codePrefix,
                PartTimeForm::codePrefix
            ])) {
                $filter['supplier_code'] = $match[2];
            }
        }
        // 处理期望时间
        $helper = new BaseRecord();
        $helper->handlerFormDataTime($filter, 'min_expect_time');
        $helper->handlerFormDataTime($filter, 'max_expect_time');
        $helper->handlerFormDataTime($filter, 'max_created_time');
        $helper->handlerFormDataTime($filter, 'min_created_time');

        $map = [
            'code'=>[
                'like',
                'payment',
                'code',
            ],
            'project_name'=>[
                'like',
                'project',
                'name',
            ],
            'project_code'=>[
                'like',
                'project',
                'code',
            ],
            'created_name'=>[
                'like',
                'created',
                'name',
            ],
            'supplier_name'=>[
                'like',
                [
                    'supplier',
                    'part_time'
                ],
                'name',
            ],
            'supplier_code'=>[
                'like',
                [
                    'supplier',
                    'part_time'
                ],
                'code',
            ],
            'stamp_status'=>[
                '=',
                'payment',
                'stamp_status',
            ],
            'min_checked_stamp_money'=>[
                '>=',
                'payment',
                'checked_stamp_money',
            ],
            'max_checked_stamp_money'=>[
                '<=',
                'payment',
                'checked_stamp_money',
            ],
            'purpose'=>[
                '=',
                'payment',
                'purpose',
            ],
            'with_stamp'=>[
                '=',
                'payment',
                'with_stamp',
            ],
            'status'=>[
                '=',
                'payment',
                'status',
            ],
            'receiver_account_type'=>[
                '=',
                'payment',
                'receiver_account_type',
            ],
            'max_created_time'=>[
                '<=',
                'payment',
                'created_time',
            ],
            'min_created_time'=>[
                '>=',
                'payment',
                'created_time',
            ],
            'max_expect_time'=>[
                '<=',
                'payment',
                'expect_time',
            ],
            'min_expect_time'=>[
                '>=',
                'payment',
                'expect_time',
            ],
            'min_money'=>[
                '>=',
                'payment',
                'actual_money',
            ],
            'max_money'=>[
                '<=',
                'payment',
                'actual_money',
            ],
            ''
        ];
        $condition = [
            'and',
        ];

        foreach($filter as $key=>$value) {
            if(!is_array($map[$key][1])) {
                $condition[] = [
                    $map[$key][0],
                    $this->aliasMap[$map[$key][1]] . '.' . $map[$key][2],
                    trim($value, ' ')
                ];
                continue;
            }

            $tempCondition = [
                'or',
            ];
            foreach ($map[$key][1] as $item) {
                $tempCondition[] = [
                    $map[$key][0],
                    $this->aliasMap[$item] . '.' . $map[$key][2],
                    trim($value, ' ')
                ];
            }
            $condition[] = $tempCondition;
        }

        $condition[] = $this->buildCondition($entrance);
        return $this->paymentAssessList(
            [
                'payment'=>[
                    '*'
                ],
                'project'=>[
                    'code',
                    'name',
                ],
                'created'=> [
                    'name',
                ]
            ],
            $condition
        );
    }
}