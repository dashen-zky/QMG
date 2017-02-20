<?php
namespace backend\modules\daily\models\transaction;
use backend\models\BaseRecord;
use backend\models\interfaces\DeleteRecordOperator;
use backend\models\UUID;
use backend\modules\daily\models\week_report\WeekReportConfig;
use Yii;
use yii\db\Exception;
use backend\models\MyPagination;
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-11-28
 * Time: 下午3:52
 */
class Transaction extends BaseRecord implements DeleteRecordOperator
{
    public static $aliasMap = [
        'transaction'=>'t1',
        'transaction_week_report_map'=>'t2',
    ];

    public static function tableName()
    {
        return self::DailyTransaction;
    }

    public function setTop($uuid) {
        if (empty($uuid)) {
            return true;
        }

        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if(empty($record)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            self::updateAllCounters(['order'=>-1], [
                '<>',
                'order',
                0
            ]);
            $record->order = TransactionConfig::Top;
            $record->update();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function unfinishedTransactionList() {
        return $this->transactionList(
            [
                'transaction'=>[
                    '*'
                ],
                'transaction_week_report_map'=>[
                    'transaction_uuid'
                ]
            ],
            [
                'and',
                [
                    '=',
                    self::$aliasMap['transaction'] . '.execute_uuid',
                    Yii::$app->user->getIdentity()->getId()
                ],
                [
                    '=',
                    self::$aliasMap['transaction'] . '.status',
                    TransactionConfig::StatusUnfinished,
                ]
            ]
        );
    }

    public function getRecord($uuid) {
        return $this->transactionList(
            [
                'transaction'=>[
                    '*'
                ]
            ],
            [
                '=',
                self::$aliasMap['transaction'] . '.uuid',
                $uuid
            ],true);
    }

    // 有效是指没有被放弃的事项
    public function effectiveTransactionList() {
        return $this->transactionList(
            [
                'transaction'=>[
                    '*'
                ],
                'transaction_week_report_map'=>[
                    'transaction_uuid'
                ]
            ],
            [
                'and',
                [
                    '=',
                    self::$aliasMap['transaction'] . '.execute_uuid',
                    Yii::$app->user->getIdentity()->getId()
                ],
                [
                    'or',
                    [
                        '=',
                        self::$aliasMap['transaction'] . '.status',
                        TransactionConfig::StatusUnfinished,
                    ],
                    [
                        '=',
                        self::$aliasMap['transaction'] . '.status',
                        TransactionConfig::StatusFinished,
                    ],
                       
                ]
            ]
        );
    }



    public function finishedTransactionList() {
        return $this->transactionList(
            [
                'transaction'=>[
                    '*'
                ]
            ],
            [
                'and',
                [
                    '=',
                    self::$aliasMap['transaction'] . '.execute_uuid',
                    Yii::$app->user->getIdentity()->getId()
                ],
                [
                    'in',
                    self::$aliasMap['transaction'] . '.status',
                    [
                        TransactionConfig::StatusFinished,
                        TransactionConfig::StatusDropped,
                    ]
                ]
            ]
        );
    }


    public function effectiveListFilter($filter) {
        if (empty($filter)) {
            return $this->effectiveTransactionList();
        }

        $this->handlerFormDataTime($filter, 'min_expect_finish_time');
        $this->handlerFormDataTime($filter, 'max_expect_finish_time');

        $map = [
            'title'=>[
                'like',
                self::$aliasMap['transaction'] . '.title',
            ],
            'status'=>[
                '=',
                self::$aliasMap['transaction'] . '.status',
            ],
            'min_expect_finish_time'=>[
                '>=',
                self::$aliasMap['transaction'] . '.expect_finish_time',
            ],
            'max_expect_finish_time'=>[
                '<=',
                self::$aliasMap['transaction'] . '.expect_finish_time',
            ],
        ];

        $condition = [
            'and',
            [
                '=',
                self::$aliasMap['transaction'] . '.execute_uuid',
                Yii::$app->user->getIdentity()->getId()
            ],
            [
                'or',
                [
                    '=',
                    self::$aliasMap['transaction'] . '.status',
                    TransactionConfig::StatusUnfinished,
                ],
                [
                    'and',
                    [
                        '=',
                        self::$aliasMap['transaction'] . '.status',
                        TransactionConfig::StatusFinished,
                    ],
                    [
                        'or',
                        [
                            'and',
                            [
                                '=',
                                self::$aliasMap['transaction_week_report_map'] . '.is_current_week_transaction',
                                WeekReportConfig::IsNextWeekReport,
                            ],
                            [
                                'is not',
                                self::$aliasMap['transaction_week_report_map'] . '.transaction_uuid',
                                null,
                            ],
                        ],
                        [
                            'is',
                            self::$aliasMap['transaction_week_report_map'] . '.transaction_uuid',
                            null,
                        ],
                    ]
                ]
            ]
        ];
        foreach ($filter as $index => $value) {
            if (!isset($map[$index])) {
                continue;
            }
            $condition[] = [
                $map[$index][0],
                $map[$index][1],
                $value
            ];
        }

        return $this->transactionList(
            [
                'transaction'=>[
                    '*'
                ],
                'transaction_week_report_map'=>[
                    'transaction_uuid'
                ]
            ],
            $condition
        );
    }

    public function unfinishedListFilter($filter) {
        if (empty($filter)) {
            return $this->unfinishedTransactionList();
        }

        $this->handlerFormDataTime($filter, 'min_expect_finish_time');
        $this->handlerFormDataTime($filter, 'max_expect_finish_time');

        $map = [
            'title'=>[
                'like',
                self::$aliasMap['transaction'] . '.title',
            ],
            'min_expect_finish_time'=>[
                '>=',
                self::$aliasMap['transaction'] . '.expect_finish_time',
            ],
            'max_expect_finish_time'=>[
                '<=',
                self::$aliasMap['transaction'] . '.expect_finish_time',
            ],
        ];

        $condition = [
            'and',
            [
                '=',
                self::$aliasMap['transaction'] . '.execute_uuid',
                Yii::$app->user->getIdentity()->getId()
            ],
            [
                '=',
                self::$aliasMap['transaction'] . '.status',
                TransactionConfig::StatusUnfinished
            ]
        ];
        foreach ($filter as $index => $value) {
            $condition[] = [
                $map[$index][0],
                $map[$index][1],
                $value
            ];
        }

        return $this->transactionList(
            [
                'transaction'=>[
                    '*'
                ],
                'transaction_week_report_map'=>[
                    'transaction_uuid'
                ]
            ],
            $condition
        );
    }

    public function finishedListFilter($filter) {
        if (empty($filter)) {
            return $this->finishedTransactionList();
        }

        $this->handlerFormDataTime($filter, 'min_expect_finish_time');
        $this->handlerFormDataTime($filter, 'max_expect_finish_time');

        $map = [
            'title'=>[
                'like',
                self::$aliasMap['transaction'] . '.title',
            ],
            'status'=>[
                '=',
                self::$aliasMap['transaction'] . '.status',
            ],
            'min_expect_finish_time'=>[
                '>=',
                self::$aliasMap['transaction'] . '.expect_finish_time',
            ],
            'max_expect_finish_time'=>[
                '<=',
                self::$aliasMap['transaction'] . '.expect_finish_time',
            ],
        ];

        $condition = [
            'and',
            [
                '=',
                self::$aliasMap['transaction'] . '.execute_uuid',
                Yii::$app->user->getIdentity()->getId()
            ],
            [
                'in',
                self::$aliasMap['transaction'] . '.status',
                [
                    TransactionConfig::StatusFinished,
                    TransactionConfig::StatusDropped,
                ]
            ]
        ];
        foreach ($filter as $index => $value) {
            $condition[] = [
                $map[$index][0],
                $map[$index][1],
                $value
            ];
        }

        return $this->transactionList(
            [
                'transaction'=>[
                    '*'
                ]
            ],
            $condition
        );
    }

    public function getTransactionListByWeekReportUuid($week_report_uuid, $is_current_week_transaction) {
        return $this->transactionList(
            [
                'transaction'=>[
                    '*'
                ]
            ],
            [
                'and',
                [
                    '=',
                    self::$aliasMap['transaction_week_report_map'] . '.week_report_uuid',
                    $week_report_uuid,
                ],
                [
                    '=',
                    self::$aliasMap['transaction_week_report_map'] . '.is_current_week_transaction',
                    $is_current_week_transaction,
                ]
            ]
        );
    }

    public function transactionList($selects, $conditions = null,$fetchOne = false) {
        $selector = [];

        if (!empty($selects)) {
            foreach(self::$aliasMap as $key=>$alias) {
                if (isset($selects[$key])) {
                    foreach($selects[$key] as $select) {
                        if ($key === 'transaction') {
                            $select = trim($select);
                            $selector[] = $alias ."." . $select;
                        } else {
                            $select = trim($select);
                            $selector[] = $alias ."." . $select ;
                        }
                    }
                }
            }
        }

        // var_dump($selector);die;

        $query = self::find()
            ->alias('t1')
            ->select($selector)
            ->leftJoin(self::DailyWeekReportTransactionMap . ' t2', 't1.uuid = t2.transaction_uuid');
        if(!empty($conditions)) {
            $query->andWhere($conditions);
        }

        // echo "<pre>";
        // var_dump($query);die;
        var_dump($query->createCommand()->getRawSql());die;


        if ($fetchOne) {
            $record = $query->asArray()->one();
            return $record;
        }

        $pagination = new MyPagination([
            'totalCount'=>$query->count(),
            'pageSize' => self::PageSize,
        ]);
        $list = $query->orderBy([
            't1.order' => SORT_DESC,
            't1.expect_finish_time' => SORT_DESC
        ])->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data = [
            'pagination' => $pagination,
            'list'=> $list,
        ];
        return $data;
    }

    public function formDataPreHandler(&$formData, $record)
    {
        if(empty($record)) {
            if (!isset($formData['uuid']) || empty($formData['uuid'])) {
                $formData['uuid'] = UUID::getUUID();
            }
        }
        $this->handlerFormDataTime($formData, 'expect_finish_time');
        parent::formDataPreHandler($formData, $record); // TODO: Change the autogenerated stub
    }

    public function recordPreHandler(&$formData, $record = null)
    {
        if(empty($record)) {
            $this->setOldAttribute('title', null);
            $this->setOldAttribute('expect_finish_time', null);
            $this->setOldAttribute('execute_name', null);
            $this->setOldAttribute('content', null);
            $this->setOldAttribute('created_uuid', null);
        }
        parent::recordPreHandler($formData, $record); // TODO: Change the autogenerated stub
    }

    // 在添加的事项的时候会根据指定的人，指定一个人就会生成一条记录
    public function add($formData) {
        if(empty($formData)) {
            return true;
        }

        $execute_uuids = array_merge([
            Yii::$app->user->getIdentity()->getId()
        ],explode(',', trim($formData['execute_uuid'])));
        $formData['execute_name'] = empty($formData['execute_name'])?
            Yii::$app->user->getIdentity()->getEmployeeName() :
            Yii::$app->user->getIdentity()->getEmployeeName() . ',' . $formData['execute_name'];


        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($execute_uuids as $item) {
                $formData['execute_uuid'] = $item;
                $this->insertRecord($formData);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function insertRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }

        if(!$this->updatePreHandler($formData)) {
            return true;
        }

        return $this->insert();
    }

    public function deleteRecord($uuid)
    {
        if(empty($uuid)) {
            return true;
        }

        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if (empty($record)) {
            return true;
        }

        return $record->delete();
    }

    public function updateRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }
        $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
        if(empty($record) || !$this->updatePreHandler($formData, $record)) {
            return true;
        }

        $record->update();
        return true;
    }
}