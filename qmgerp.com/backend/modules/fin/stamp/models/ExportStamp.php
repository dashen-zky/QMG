<?php
namespace backend\modules\fin\stamp\models;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/17 0017
 * Time: 上午 11:11
 */
use backend\modules\crm\models\project\record\ProjectApplyStampMap;
use Yii;
use backend\models\UUID;
use yii\db\Exception;

class ExportStamp extends Stamp
{
    public function exportStampList($condition = null) {
        $stampList = (!empty($condition))?$this->stampList(
            [
                'stamp'=>[
                    '*',
                ],
                'created'=>[
                    'name'
                ],
            ],
            [
                'and',
                $condition,
                [
                    '=',
                    $this->aliasMap['stamp'].'.type',
                    StampConfig::ExportStamp,
                ]
            ]
        ):$this->stampList(
            [
                'stamp'=>[
                    '*',
                ],
                'created'=>[
                    'name'
                ],
            ],
            [
                '=',
                $this->aliasMap['stamp'].'.type',
                StampConfig::ExportStamp,
            ]
        );
        return $stampList;
    }
    
    public function insertStamp($formData) {
        if(empty($formData)) {
            return true;
        }

        $formData['type'] = StampConfig::ExportStamp;
        $formData['uuid'] = UUID::getUUID();
        if($this->insertRecord($formData) === self::SeriesValidateError) {
            return self::SeriesValidateError;
        }
        return true;
    }

    /**
     * 销项发票作废流程
     * 1.将销项发票变成disable
     * 2.将销项发票和开票申请的匹配记录删除
     * 3. 将销项发票匹配上的对应的开票申请的记录的匹配金额减去, 根据金额，将开票申请的状态作相对应的变化
     * 4. 将该销项发票开给项目的金额减去
     */
    public function disable($uuid) {
        if(empty($uuid)) {
            return true;
        }

        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if(empty($record)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $record->enable  = StampConfig::Disable;
            $record->update();
            $projectApplyStamp = new ProjectApplyStampMap();
            $projectApplyStamp->stampDisable($uuid);
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    public function ajaxInsertStamp($formData) {
        if(empty($formData)) {
            return '小朋友，' . '提交的数据不能为空哦！';
        }

        $requiredField = [
            'series_number'=>'发票编号',
            'made_time'=>'开票日期',
            'money'=>'金额',
            'tax_point'=>'税点',
            'tax_money'=>'税费',
            'before_tax_money'=>'税前金额',
            'receiver'=>'收票方',
            'provider'=>'开票方',
        ];
        foreach ($requiredField as $index=>$value) {
            if(!isset($formData[$index]) || empty($formData[$index])) {
                return '小朋友，' . $value . '不能为空！';
            }
        }

        $formData['type'] = StampConfig::ExportStamp;
        $formData['uuid'] = UUID::getUUID();
        if($this->insertRecord($formData) === self::SeriesValidateError) {
            return '小朋友，你输入的发票编号已经存在了，请检查清楚哦';
        }
        return '不得不夸夸你，太棒了，录入发票成功！！！';
    }

    public function listFilter($filter) {
        if(empty($filter)) {
            return $this->exportStampList();
        }

        $this->handlerFormDataTime($filter, 'min_made_time');
        $this->handlerFormDataTime($filter, 'max_made_time');

        $condition = [
            'and',
            [
                '=',
                $this->aliasMap['stamp'].'.type',
                StampConfig::ExportStamp,
            ],
        ];

        $map = [
            'series_number'=>[
                'like',
                'stamp',
                'series_number',
            ],
            'service_type'=>[
                '=',
                'stamp',
                'service_type'
            ],
            'status'=>[
                '=',
                'stamp',
                'status',
            ],
            'min_money'=>[
                '>=',
                'stamp',
                'money',
            ],
            'max_money'=>[
                '<=',
                'stamp',
                'money',
            ],
            'min_made_time'=>[
                '>=',
                'stamp',
                'made_time',
            ],
            'max_made_time'=>[
                '<=',
                'stamp',
                'made_time',
            ],
            'provider'=>[
                'like',
                'stamp',
                'provider',
            ],
            'receiver'=>[
                'like',
                'stamp',
                'receiver',
            ],
        ];

        foreach ($filter as $index => $item) {
            $condition[] = [
                $map[$index][0],
                $this->aliasMap[$map[$index][1]] . '.' . $map[$index][2],
                $item
            ];
        }

        return $this->stampList(
            [
                'stamp'=>[
                    '*',
                ],
                'created'=>[
                    'name'
                ],
            ],
            $condition
        );
    }

    public function getRecordBySeriesNumber($seriesNumber) {
        if(empty($seriesNumber)) {
            return null;
        }

        return $this->getRecordByCondition([
            'and',
            [
                '=',
                'series_number',
                $seriesNumber,
            ],
            [
                '=',
                'enable',
                StampConfig::Enable,
            ],
            [
                '=',
                'type',
                StampConfig::ExportStamp,
            ]
        ]);
    }
    
    // 开票
    public function billing($formData) {
        if(empty($formData) || !isset($formData['uuid']) || empty($formData['uuid'])) {
            return true;
        }
        
        $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
        if(empty($record)) {
            return true;
        }
        
        $record->checked_money += $formData['checked_money'];
        return $record->update();
    }
}