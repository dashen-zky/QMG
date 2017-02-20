<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-12-21
 * Time: 下午9:39
 */

namespace backend\modules\crm\models\project\record;


use backend\models\BaseRecord;
use backend\models\interfaces\DeleteRecordOperator;
use Yii;
use backend\models\helper\file\UploadFileHelper;
use yii\db\Exception;
use backend\modules\rbac\model\PermissionManager;
use backend\models\MyPagination;

class ProjectMediaBrief extends BaseRecord implements DeleteRecordOperator
{
    public static $aliasMap = [
        'brief'=>'t1',
        'project'=>'t2',
        'project_manager'=>'t3',
        'project_member_map'=>'t4',
        'project_member'=>'t5',
        'project_customer_map'=>'t6',
        'customer'=>'t7',
        'customer_advance'=>'t8',
        'sales'=>'t9',
        'created'=>'t10'
    ];

    public static function tableName()
    {
        return self::CRMProjectMediaBrief;
    }

    public static function canAssess($status) {
        if($status != ProjectMediaBriefConfig::StatusApplying) {
            return false;
        }

        return Yii::$app->authManager->checkAccess(
            Yii::$app->user->getIdentity()->getId(),
            PermissionManager::ProjectMediaBriefAssess
        );
    }

    public function formDataPreHandler(&$formData, $record)
    {
        parent::formDataPreHandler($formData, $record);
        if (empty($record)) {
            $formData['status'] = ProjectMediaBriefConfig::StatusApplying;
        }
    }

    public function insertRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }

        if(!$this->updatePreHandler($formData)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            UploadFileHelper::uploadWhileInsert($this,
                isset($formData['file'])?$formData['file']:null,
                '/upload/project_media_brief/'.$formData['uuid']);
            $this->insert();
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        } catch (\Exception $e) {
            $transaction->rollBack();
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
        if (empty($record) || !$this->updatePreHandler($formData, $record)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            UploadFileHelper::uploadWhileUpdate(
                $record,
                isset($formData['file'])?$formData['file']:null,
                '/upload/project_media_brief/'.$formData['uuid']
            );
            $record->update();
        }catch (Exception $e) {
            $transaction->rollBack();
            return false;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function deleteRecord($uuid)
    {
        if (empty($uuid)) {
            return true;
        }

        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if (empty($record)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            UploadFileHelper::deleteAllAttachments($record->path);
            $record->delete();
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function listForMedia() {
        return $this->briefList(
            [
                'brief'=>[
                    '*'
                ],
                'project'=>[
                    'name',
                ],
                'customer'=>[
                    'name',
                ],
                'project_manager'=>[
                    'name',
                ],
                'sales' => [
                    'name',
                ],
                'created'=> [
                    'name'
                ]
            ],
            [
                '=',
                self::$aliasMap['project'] . '.enable',
                Project::Enable,
            ]
        );
    }

    public function getBriefListByProjectUuid($uuid) {
        if (empty($uuid)) {
            return null;
        }

        return $this->briefList(
            [
                'brief'=>[
                    '*'
                ],
                'project'=>[
                    'name',
                ],
                'customer'=>[
                    'name',
                ],
                'project_manager'=>[
                    'name',
                ],
                'sales' => [
                    'name',
                ],
                'created'=> [
                    'name'
                ]
            ],
            [
                '=',
                self::$aliasMap['project'] . '.uuid',
                $uuid
            ],false, false
        );
    }

    public function briefList($selects, $conditions = null,$fetchOne = false, $enablePage = true) {
        $selector = [];

        if (!empty($selects)) {
            foreach(self::$aliasMap as $key=>$alias) {
                if (isset($selects[$key])) {
                    foreach($selects[$key] as $select) {
                        if ($key === 'brief') {
                            $select = trim($select);
                            $selector[] = $alias ."." . $select;
                        } else if ($key == 'project_member'){
                            $select = trim($select);
                            $selector[] = "group_concat(".$alias ."." . $select .") " . $key . "_" .$select;
                        } else {
                            $selector[] = $alias ."." . $select . " " . $key . "_" .$select;
                        }
                    }
                }
            }
        }

        $query = self::find()
            ->alias('t1')
            ->select($selector)
            ->leftJoin(self::CRMProject . ' t2', 't1.project_uuid = t2.uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t3', 't2.project_manager_uuid = t3.uuid')
            ->leftJoin(self::CRMProjectMemberMap . ' t4', 't4.project_uuid = t2.uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t5','t5.uuid = t4.member_uuid')
            ->leftJoin(self::CRMCustomerProjectMap . ' t6', 't6.project_uuid = t2.uuid')
            ->leftJoin(self::CRMCustomerBasic . ' t7', 't7.uuid = t6.customer_uuid')
            ->leftJoin(self::CRMCustomerAdvance . ' t8', 't8.customer_uuid = t7.uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t9', 't9.uuid = t8.sales_uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t10', 't10.uuid = t1.created_uuid')
            ->groupBy('t1.uuid');
        if(!empty($conditions)) {
            $query->andWhere($conditions);
        }

        if ($fetchOne) {
            $record = $query->asArray()->one();
            return $record;
        }

        if (!$enablePage) {
            return [
                'list'=>$query->orderBy([
                    't1.id'=>SORT_DESC
                ])->asArray()->all(),
            ];
        }

        $pagination = new MyPagination([
            'totalCount'=>$query->count(),
            'pageSize' => self::PageSize,
        ]);
        $list = $query->orderBy([
            't1.id' => SORT_DESC,
        ])->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        return [
            'pagination' => $pagination,
            'list'=> $list,
        ];
    }

    public function listFilterFormMedia($filter) {
        if(empty($filter)) {
            return $this->listForMedia();
        }

        $map = [
            'title'=>[
                'like',
                self::$aliasMap['brief'] . '.title'
            ],
            'created_name'=>[
                'like',
                self::$aliasMap['created'] . '.name',
            ],
            'project_name'=>[
                'like',
                self::$aliasMap['project'] . '.name',
            ],
            'customer_name'=>[
                'like',
                self::$aliasMap['customer'] . '.name',
            ],
        ];

        $condition = [
            'and',
            [
                '=',
                self::$aliasMap['project'] . '.enable',
                Project::Enable,
            ]
        ];
        foreach ($filter as $index => $value) {
            $condition[] = [
                $map[$index][0],
                $map[$index][1],
                trim($value),
            ];
        }

        return $this->briefList(
            [
                'brief'=>[
                    '*'
                ],
                'project'=>[
                    'name',
                ],
                'customer'=>[
                    'name',
                ],
                'project_manager'=>[
                    'name',
                ],
                'sales' => [
                    'name',
                ],
                'created'=> [
                    'name'
                ]
            ],
            $condition
        );
    }

    public function getRecord($uuid) {
        if (empty($uuid)) {
            return null;
        }

        return $this->briefList(
            [
                'brief'=>[
                    '*'
                ],
                'project'=>[
                    'uuid',
                    'name'
                ],
                'customer'=>[
                    'name',
                ],
                'project_manager'=>[
                    'name',
                ],
                'project_member'=>[
                    'name'
                ],
                'sales' => [
                    'name',
                ],
                'created'=> [
                    'name'
                ]
            ],
            [
                '=',
                self::$aliasMap['brief'] . '.uuid',
                $uuid
            ],true
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