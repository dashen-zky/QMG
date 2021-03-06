<?php
namespace backend\modules\fin\accountReceivable\models;
use backend\models\BaseRecord;
use backend\models\helper\file\UploadFileHelper;
use backend\models\interfaces\DeleteRecordOperator;
use backend\models\UUID;
use backend\modules\crm\models\project\record\ProjectAccountReceivableMap;
use Yii;
use yii\db\Exception;
use backend\models\MyPagination;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/26 0026
 * Time: 上午 11:38
 */
class AccountReceivable extends BaseRecord implements DeleteRecordOperator
{
    public $file;
    public $aliasMap;
    const Enable = 1;
    const Disable = 2;

    public function rules()
    {
        return [
            [['money', 'bank_series_number', 'payment'],'required'],
            [['money'],  'number'],
            [['file'], 'file', 'maxFiles'=>1, 'extensions' => ['png', 'jpg', 'gif']],
            [['bank_series_number'],'unique','message'=>'陛下，这个银行流水已经被使用了哦，:-（', 'on'=>'add'],
        ];
    }

    public function init()
    {
        $this->aliasMap = [
            'account_receivable'=>'t1',
            'created'=>'t2',
            'receive_money_company'=>'t3',
        ];
        parent::init(); // TODO: Change the autogenerated stub
    }

    public static function tableName()
    {
        return self::FINAccountReceivable;
    }

    public function formDataPreHandler(&$formData, $record)
    {
        if(empty($record)) {
            $formData['time'] = time();
            $formData['uuid'] = UUID::getUUID();
            $formData['enable'] = self::Enable;
        }
        $this->handlerFormDataTime($formData, 'receive_time');
        parent::formDataPreHandler($formData, $record); // TODO: Change the autogenerated stub
    }

    public function insertRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }

        if(!$this->updatePreHandler($formData)) {
            return true;
        }
        
        if(!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            UploadFileHelper::uploadWhileInsert(
                $this,
                $formData['file'],
                '/upload/account-receivable/'.$formData['uuid']);
            $this->insert();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw  $e;
            return false;
        }

        $transaction->commit();
        return true;
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

        $value = $record->getDirtyAttributes();
        if(empty($value)) {
            return true;
        }

        UploadFileHelper::uploadWhileUpdate($record,
            $formData['file'],
            '/upload/account-receivable/'.$formData['uuid']);
        $record->update();
        return true;
    }
    
    public function receiveMoney($formData) {
        if(empty($formData)) {
            return true;
        }

        $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
        if(empty($record)) {
            return true;
        }
        $record->distributed_money += $formData['distributed_money'];
        return $record->update();
    }
    
    public function deleteRecord($uuid)
    {
        if(empty($uuid)) {
            return true;
        }
        
        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if(empty($record)) {
            return true;
        }
        $record->enable = self::Disable;
        return $record->update();
    }

    public function allList() {
        return $this->accountReceivableList([
            'account_receivable'=>[
                '*'
            ],
            'created'=>[
                'name',
            ]
        ]);
    }
    
    public function getRecordByUuid($uuid) {
        if(empty($uuid)) {
            return null;
        }
        
        return $this->accountReceivableList(
            [
                'account_receivable'=>[
                '*'
                ],
                'created'=>[
                    'name',
                ],
                'receive_money_company'=>[
                    'account',
                    'bank_of_deposit'
                ]
            ],
            [
                '=',
                $this->aliasMap['account_receivable'] . '.uuid',
                $uuid
            ],
            true
        );
    }
    
    public function getRecordByBankSeriesNumber($bankSeriesNumber) {
        if(empty($bankSeriesNumber)) {
            return null;
        }
        
        return self::findOne([
            'bank_series_number'=>$bankSeriesNumber,
            'enable'=>self::Enable,
        ]);
    }
    
    public function accountReceivableList($selects, $conditions = null,$fetchOne = false) {
        $aliasMap = $this->aliasMap;
        $selector = [];

        if (!empty($selects)) {
            foreach($aliasMap as $key=>$alias) {
                if (isset($selects[$key])) {
                    foreach($selects[$key] as $select) {
                        if(in_array($key, [
                            'created',
                            'receive_money_company',
                        ])) {
                            $select = trim($select);
                            $selector[] = $alias ."." . $select . " " . $key . "_" .$select;
                        } elseif ($key === 'account_receivable') {
                            $select = trim($select);
                            $selector[] = $alias ."." . $select;
                        }
                    }
                }
            }
        }

        $query = self::find()
            ->alias('t1')
            ->select($selector)
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t2', 't1.created_uuid = t2.uuid')
            ->leftJoin(self::FINReceiveMoneyCompany . ' t3', 't3.uuid = t1.receive_company_uuid');
        
        if(!empty($conditions)) {
            $query->andWhere($conditions);
        }

        if ($fetchOne) {
            $record = $query->asArray()->one();
            return $record;
        }

        $pagination = new MyPagination([
            'totalCount'=>$query->count(),
            'pageSize' => self::PageSize,
        ]);
        $list = $query->orderBy('t1.time desc')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data = [
            'pagination' => $pagination,
            'list'=> $list,
        ];
        return $data;
    }

    public function listFilter($filter) {
        if(empty($filter)) {
            return $this->allList();
        }
        
        $this->handlerFormDataTime($filter, 'min_time');
        $this->handlerFormDataTime($filter, 'max_time');
        $this->handlerFormDataTime($filter, 'min_receive_time');
        $this->handlerFormDataTime($filter, 'max_receive_time');

        $map = [
            'bank_series_number'=>[
                'like',
                'account_receivable',
                'bank_series_number',
            ],
            'payment'=>[
                'like',
                'account_receivable',
                'payment',
            ],
            'min_money'=>[
                '>=',
                'account_receivable',
                'money',
            ],
            'max_money'=>[
                '<=',
                'account_receivable',
                'money',
            ],
            'min_time'=>[
                '>=',
                'account_receivable',
                'time',
            ],
            'max_time'=>[
                '<=',
                'account_receivable',
                'time',
            ],
            'min_receive_time'=>[
                '>=',
                'account_receivable',
                'receive_time',
            ],
            'max_receive_time'=>[
                '<=',
                'account_receivable',
                'receive_time',
            ],
            'created_name'=>[
                'like',
                'created',
                'name',
            ],
        ];
        
        $condition = [
            'and',
        ];
        foreach ($filter as $index => $item) {
            $condition[] = [
                $map[$index][0],
                $this->aliasMap[$map[$index][1]] . '.' . $map[$index][2],
                trim($item),
            ];
        }
        
        return $this->accountReceivableList(
            [
                'account_receivable'=>[
                    '*'
                ],
                'created'=>[
                    'name',
                ]
            ],
            $condition
        );
    }

    public function deleteAttachment($uuid, $path) {
        if(empty($uuid) || empty($path)) {
            return false;
        }

        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if(empty($record)) {
            return false;
        }

        $attachments = unserialize($record->path);
        foreach($attachments as $index => $item) {
            if($item === $path) {
                unset($attachments[$index]);
                break;
            }
        }

        $record->path = serialize($attachments);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $realPath = Yii::getAlias("@app").iconv("UTF-8", "GBK", $path);
            if(file_exists($realPath)) {
                unlink($realPath);
            }

            $record->update();
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }
}