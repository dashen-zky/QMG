<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/17 0017
 * Time: 下午 8:31
 */

namespace backend\modules\crm\models\customer\record;

use backend\models\interfaces\DeleteRecordOperator;
use backend\models\interfaces\PrimaryTable;
use backend\models\interfaces\RecordOperator;
use backend\models\MyPagination;
use backend\modules\crm\models\customer\model\ContactForm;
use backend\modules\crm\models\project\record\ProjectContactMap;
use backend\modules\crm\models\supplier\record\SupplierContactMap;
use Yii;
use backend\modules\crm\models\customer\record\CustomerBaseRecord;
use yii\data\Pagination;
use yii\db\Exception;
use backend\models\UUID;
use yii\helpers\Json;
use backend\modules\crm\models\customer\record\CustomerContactMap;

class Contact extends CustomerBaseRecord implements RecordOperator,PrimaryTable,DeleteRecordOperator
{
    public $type;
    public $objectType;
    static public function tableName()
    {
        return self::CRMContact;
    }

    public function getRecordByUuid($uuid)
    {
        if(empty($uuid)) {
            return null;
        }

        return self::find()->andWhere(['uuid'=>$uuid])->asArray()->one();
    }

    public function recordPreHandler(&$formData, $record = null)
    {
        if(empty($record)) {
            $this->setOldAttribute('created_uuid',null);
        }
    }

    public function insertContact($formData) {
        if(empty($formData)) {
            return false;
        }

        if(!$this->updatePreHandler($formData)) {
            return false;
        }
        return $this->insert();
    }

    public function formDataPreHandler(&$formData, $record)
    {
        if(empty($record)) {
            if(!isset($formData['uuid']) || empty($formData['uuid'])) {
                $formData['uuid'] = UUID::getUUID();
            }
            $this->clearEmptyField($formData);
        }
        parent::formDataPreHandler($formData, $record);
    }

    public function insertRecord($formData)
    {
        // 如果设置了objectUuid表示编辑对象，表示我们要对map表进行操作
        if(isset($formData['objectUuid'])) {
            $objectType = $formData['objectType'];
            $objectUuid = $formData['objectUuid'];
            unset($formData['objectUuid']);
            unset($formData['objectType']);
        }
        if(!$this->updatePreHandler($formData) || empty($formData['name'])) {
            return false;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            // 插入数据库
            $this->uuid = UUID::getUUID();
            parent::insert();
            // 如果对象的uuid不为空的话，我们需要将联系人和对象建立关系
            if (isset($objectUuid) && !empty($objectUuid)) {
                if ($objectType === 'customer') {
                    //对象是客户
                    $customerContactMap = new CustomerContactMap();
                    $customerContactMap->insertSingleRecord([
                        'customer_uuid'=>$objectUuid,
                        'contact_uuid'=>$this->uuid,
                        'type'=>$formData['type'],
                    ]);
                } elseif($objectType === 'supplier') {
                    // 对象为供应商
                    $supplierContactMap = new SupplierContactMap();
                    $supplierContactMap->insertSingleRecord([
                        'supplier_uuid'=>$objectUuid,
                        'contact_uuid'=>$this->uuid,
                        'type'=>$formData['type'],
                    ]);

                }
            }
        }catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    // 在更新之前预处理formData里面的一些数据
    protected function prepareFromDataBeforeUpdate(&$formData,$index,$default = 0) {
        $value = $default;
        if (isset($formData[$index])) {
            if(!empty($formData[$index])) {
                $value = $formData[$index];
            }
            unset($formData[$index]);
        }
        return $value;
    }

    public function updateRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }
        // olduuids和uuids是为了区别 这条记录是被删除了还是类型改变到时从当前页面消失
        //oldUuids 存放的是旧的，上一次的uuids
        // uuids存放的是变更后的uuids
        // oldUuids的type根据页面的type来
        // uuids的type根据记录具体的type来
        $objectType = $this->prepareFromDataBeforeUpdate($formData,'objectType',0);
        $this->objectType = $objectType;
        $objectUuid = $this->prepareFromDataBeforeUpdate($formData,'objectUuid',0);
        $oldUuids = Json::decode($this->prepareFromDataBeforeUpdate($formData,'oldUuids',Json::encode(
            [
                ContactForm::CustomerContact=>[],
                ContactForm::CustomerDuty=>[]
            ]
        )));
        // $type 记录的当前的联系人，还是负责人
        $type = intval($this->prepareFromDataBeforeUpdate($formData, 'type',0));
        $otherType = (ContactForm::CustomerContact + ContactForm::CustomerDuty) - $type;
        // 如果有设置另外一个类型，将其记录下了
        if(isset($oldUuids[$otherType])) {
            $uuids[$otherType] = $oldUuids[$otherType];
        } else {
            $uuids[$otherType] = [];
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(!empty($formData)) {
                foreach($formData as $index => $record) {
                    $this->clearEmptyField($record);
                    // 已经有uuid的表示，这条数据已经在数据中存在，我们只能跟新，不能插入
                    if (isset($record['uuid']) && !empty($record['uuid'])) {
                        // 如果类型是相同的，那么将这个记录记录下来，
                        // 如果类型不相同，说明类型变了，所以不用记录下了
                        $uuids[$record['type']][$record['uuid']]=$index;

                        // 用来记录是否有联系人被删除的
                        // 旧的uuids的type是当前页面的type 而新的uuids是存放的是变更后的uuids所以根据记录的type来
                        unset($oldUuids[$type][$record['uuid']]);
                        $contact = self::find()->andWhere(['uuid'=>$record['uuid']])->one();
                        // 跟新map表
                        if ($objectType === 'customer' && intval($record['type']) !== $type) {
                            $customerContactMap = new CustomerContactMap();
                            $customerContactMap->updateSingleRecord([
                                'type'=>$record['type'],
                                'customer_uuid'=>$objectUuid,
                                'contact_uuid'=>$record['uuid'],
                            ]);
                        } elseif ($objectType === 'supplier' && intval($record['type']) !== $type) {
                            $supplierContact = new SupplierContactMap();
                            $supplierContact->updateSingleRecord([
                                'type'=>$record['type'],
                                'supplier_uuid'=>$objectUuid,
                                'contact_uuid'=>$record['uuid'],
                            ]);
                        }
                        if (!empty($contact) && $this->updateRecordBuilder($record,$contact)) {
                            $contact->update();
                        }
                        continue;
                    }
                    // 插入数据
                    // 如果有uuid那么为插入到map中做准备
                    if($objectUuid) {
                        $record['objectType'] = $objectType;
                        $record['objectUuid'] = $objectUuid;
                    }
                    // 如果对象的uuid没有的话，表示这个对象是新建的，所以我们只需要对contact表进行操作就行了
                    // 如果对象的uuid有的话，表示编辑这个对象，出了对contact表进行操作，还要操作Map表
                    if($this->insertRecord($record)) {
                        $uuids[$record['type']][$this->uuid] = $index;
                    }

                }
            }

            if(!empty($oldUuids[$type])) {
                foreach($oldUuids[$type] as $uuid => $index) {
                    $this->deleteRecordByUuid($uuid);
                    unset($oldUuids[$type][$uuid]);
                }
            }
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return $uuids;
    }

    public function deleteRecordByUuid($uuid)
    {
        if (empty($uuid) || $uuid == '') {
            return true;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 将map表里面的数据也要删除
            if($this->objectType === 'customer') {
                // 将客户Map表的记录删除
                CustomerContactMap::deleteRecordByContactUuid($uuid);
                // 将项目map表的记录删除
                ProjectContactMap::deleteRecordByContactUuid($uuid);
            } elseif($this->objectType === 'supplier') {
                // 将supplier map表里面的数据删除
                SupplierContactMap::deleteRecordByContactUuid($uuid);
            }

            $this->deleteAll(['uuid'=>$uuid]);
        }catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    public function getContactListByObjectUuid($uuid, $type = 'customer') {
        // 联系人列表在客户及项目里面呈现的样式是不一样的
        $tableMap = [
            'customer'=>[
                'tableName'=>self::CRMCustomerContactMap,
                'key_uuid'=>'customer_uuid',
            ],
            'project'=>[
                'tableName'=>self::CRMCustomerContactMap,
                'key_uuid'=>'customer_uuid',
            ],
            'supplier'=>[
                'tableName'=>self::CRMSupplierContactMap,
                'key_uuid'=>'supplier_uuid',
            ],
        ];
        $query =  self::find()
            ->select([
                't1.*',
                't2.type'
            ])
            ->alias('t1')
            ->leftJoin($tableMap[$type]['tableName'] . ' t2', 't1.uuid = t2.contact_uuid')
            ->andWhere(['t2.'. $tableMap[$type]['key_uuid'] =>$uuid]);
        // 联系人列表在，在客户及项目里面呈现的样式是不一样的
        if ($type === 'project') {
            $pagination = new MyPagination([
                'totalCount' => $query->count(),
                'pageSize' => self::PageSize,
            ]);
            $list = $query->offset($pagination->offset)
                    ->limit($pagination->limit)
                    ->asArray()->all();
            return [
                'contactList' => $list,
                'pagination' => $pagination,
            ];
        }
        $list = $query->asArray()->all();
        // 將客戶联系人，客户负责人，项目联系人区分开来

        $contactList = [];
        $customerDutyList = [];
        for($i = 0; $i < count($list); $i++) {
            if (intval($list[$i]['type']) === 1) {
                $contactList[] = $list[$i];
            } else {
                $customerDutyList[] = $list[$i];
            }
        }

        // 初始化oldUuid,用来记录最开始的时候的状态
        $contactList['oldUuids'] = $this->initOldUuids([
            ContactForm::CustomerContact=>$contactList,
            ContactForm::CustomerDuty=>$customerDutyList,
        ]);
        $customerDutyList['oldUuids'] = $contactList['oldUuids'];
        return  [
            'contactList'=>$contactList,
            'customerDutyList'=>$customerDutyList,
        ];
    }

    public function initOldUuids($lists) {
        if (empty($lists)) {
            return Json::encode('');
        }

        $uuids = [];
        foreach($lists as $type => $list) {
            foreach($list as $index=>$value) {
                $uuids[$type][$value['uuid']] = $index;
            }
        }
        return Json::encode($uuids);
    }

    // 来比较一下联系人的状态是不是变化了，
    /**
     * 客户联系人
     * 客户负责人
     * 返回true表示状态不相同，不用删除数据
     * 返回false表示状态项目，要删除数据
     */
    public function differType($type, $uuid) {
        $recode = self::find()
            ->alias('t1')
            ->leftJoin(self::CRMCustomerContactMap . " t2","t1.uuid=t2.contact_uuid")
            ->andWhere(['uuid'=>$uuid])->asArray()->one();
        if ($recode['type'] !== $type) {
            return true;
        }

        return false;
    }

    public function deleteRecord($uuid)
    {
        if(empty($uuid)) {
            return  false;
        }

        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if(empty($record)) {
            return false;
        }

        return $record->delete();
    }

    public function updateContact($formData) {
        if(empty($formData)) {
            return false;
        }
        $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
        if(empty($record)) {
            return false;
        }

        if(!$this->updatePreHandler($formData, $record)) {
            return false;
        }
        $values = $record->getDirtyAttributes();
        if(empty($values)) {
            return true;
        }

        return $record->update();
    }
}