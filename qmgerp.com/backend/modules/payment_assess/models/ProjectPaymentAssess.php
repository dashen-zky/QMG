<?php
namespace backend\modules\payment_assess\models;
use backend\models\BaseRecord;
use backend\modules\fin\payment\models\Payment;
use backend\modules\fin\payment\models\PaymentConfig;
use backend\modules\system\models\payment_assess_config\DailyPaymentAssessConfig;
use backend\modules\system\models\payment_assess_config\ProjectPaymentAssessConfig;
use Yii;
use backend\models\MyPagination;
use backend\modules\crm\models\project\model\ProjectForm;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/30
 */
class ProjectPaymentAssess extends PaymentAssess
{
    private $aliasMap = [
        'payment' => 't1',
        'project_payment_map'=>'t2',
        'project'=>'t3',
        'created'=>'t4',
    ];


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
    public function assessList($status = 1) {
        $condition = $this->buildCondition($status);
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

    protected function buildCondition($status) {
        $condition = $this->buildSecondAndThirdAndFourthCondition();
        $_condition = [
            'second' => [],
            'third' => [],
            'fourth' => [],
        ];
        foreach($condition as $index => $item) {
            if(empty($item)) {
                continue;
            }
            $_condition[$index] = [
                'and',
                $condition[$index],
                [
                    'in',
                    $this->aliasMap['payment'] . '.status',
                    $this->statusMap[$index][$status],
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
        }

        return [
            'or',
            [
                'and',
                [
                    '=',
                    $this->aliasMap['project'] . '.project_manager_uuid',
                    Yii::$app->getUser()->getIdentity()->getId(),
                ],
                [
                    'in',
                    $this->aliasMap['payment'] . '.status',
                    $this->statusMap['first'][$status],
                ],
                [
                    'in',
                    $this->aliasMap['payment'] . '.type',
                    [
                        PaymentConfig::PaymentForProjectExecute,
                        PaymentConfig::PaymentForProjectMedia,
                    ]
                ]
            ],
            $_condition['second'],
            $_condition['third'],
            $_condition['fourth'],
        ];
    }

    protected function buildSecondAndThirdAndFourthCondition() {
        $projectPaymentAssessConfig = new ProjectPaymentAssessConfig();
        $config = $projectPaymentAssessConfig->generateConfigForShow();
        $condition = [
            'second' => [],
            'third' => [],
            'fourth'=>[],
        ];
        foreach($config as $item) {
            if(!$this->isAssessor($item['assess_uuid'])) {
                continue;
            }

            if($item['step'] === 'step-2') {
                $condition['second'][] = $this->transformCondition($item);
                continue;
            }

            if($item['step'] === 'step-3') {
                $condition['third'][] = $this->transformCondition($item);
                continue;
            }

            if($item['step'] === 'step-4') {
                $condition['fourth'][] = $this->transformCondition($item);
                continue;
            }
        }

        if(count($condition['second']) > 1) {
            array_unshift($condition['second'], 'or');
        } else {
            $condition['second'] = array_shift($condition['second']);
        }

        if(count($condition['third']) > 1) {
            array_unshift($condition['third'], 'or');
        } else {
            $condition['third'] = array_shift($condition['third']);
        }

        if(count($condition['fourth']) > 1) {
            array_unshift($condition['fourth'], 'or');
        } else {
            $condition['fourth'] = array_shift($condition['fourth']);
        }

        return $condition;
    }

    protected function isAssessor($assessUuid) {
        if($assessUuid === Yii::$app->getUser()->getIdentity()->getId()) {
            return true;
        }

        return false;
    }

    protected function transformCondition($condition) {
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
                    '=',
                    $this->aliasMap['payment'] . '.' . $field,
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
                    '>=',
                    $this->aliasMap['payment'] . '.' . $field,
                    $match[0][0],
                ];
                // 表明有个最大值
                if(isset($match[0][1])) {
                    $_condition = [$_condition];
                    array_unshift($_condition, 'and');
                    $_condition[] = [
                        '<',
                        $this->aliasMap['payment'] . '.' . $field,
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
            ->leftJoin(BaseRecord::EmployeeBasicInformationTableName . ' t4', 't1.created_uuid = t4.uuid');
        if(!empty($condition)) {
            $query->andWhere($condition);
        }
        $pagination = new MyPagination([
            'totalCount'=>$query->count(),
            'pageSize'=>BaseRecord::PageSize,
        ]);
        $list = $query->orderBy([
            't1.id'=>SORT_DESC
        ])->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        return [
            'list'=>$list,
            'pagination'=>$pagination,
        ];
    }

    public function listFilter($filter) {
        $entrance = $filter['entrance'];
        unset($filter['entrance']);
        if(empty($filter)) {
            return $this->assessList($entrance);
        }

        if(isset($filter['project_code']) && !empty($filter['project_code'])) {
            preg_match('/([a-zA-Z]*)(\d+)/',$filter['project_code'],$match);
            if($match[1] === ProjectForm::codePrefix) {
                $filter['project_code'] = $match[2];
            }
        }

        $map = [
            'payment'=>[
                'status' => '=',
                'purpose' => '=',
                'with_stamp' => '=',
                'receiver_account_type' => '=',
            ],
            'project'=>[
                'code' => 'like',
                'name' => 'like',
            ],
            'created'=>[
                'name' => 'like',
            ]
        ];

        $condition = [
            'and',
        ];
        // 处理期望时间
        $helper = new BaseRecord();
        $helper->handlerFormDataTime($filter, 'payment_expect_time_min');
        $helper->handlerFormDataTime($filter, 'payment_expect_time_max');
        if(isset($filter['payment_expect_time_max'])) {
            $condition[] = [
                '<',
                $this->aliasMap['payment'] . '.expect_time',
                $filter['payment_expect_time_max'],
            ];
            unset($filter['payment_expect_time_max']);
        }

        if(isset($filter['payment_expect_time_min'])) {
            $condition[] = [
                '>',
                $this->aliasMap['payment'] . '.expect_time',
                $filter['payment_expect_time_min'],
            ];
            unset($filter['payment_expect_time_min']);
        }

        foreach($filter as $key => $value) {
            foreach($map as $k => $item) {
                if(strpos($key, $k) !== false) {
                    // 把map里面的key值的长度拿出来
                    $len = strlen($k);
                    // 截取字符串，找到我们需要的field值
                    $filed = substr($key, $len + 1);
                    $condition[] = [
                        $item[$filed],
                        $this->aliasMap[$k] . '.' . $filed,
                        $value,
                    ];
                    break;
                }
            }
        }
        $condition[] = $this->buildCondition($entrance);

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
}