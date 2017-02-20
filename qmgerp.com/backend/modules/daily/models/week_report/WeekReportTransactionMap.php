<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-11-28
 * Time: 下午4:47
 */

namespace backend\modules\daily\models\week_report;

use backend\models\BaseRecord;
use backend\models\interfaces\DeleteMapRecord;

class WeekReportTransactionMap extends BaseRecord implements DeleteMapRecord
{
    public static function tableName()
    {
        return self::DailyWeekReportTransactionMap;
    }

    public function recordPreHandler(&$formData, $record = null)
    {
        if (empty($record)) {
            $this->setOldAttribute('week_report_uuid', null);
            $this->setOldAttribute('transaction_uuid', null);
            $this->setOldAttribute('is_current_week_transaction', null);
        }
    }

    public function insertSingleRecord($formData)
    {
        if (empty($formData)) {
            return true;
        }

        if (!$this->updatePreHandler($formData)) {
            return true;
        }

        return $this->insert();
    }

    public function updateSingleRecord($formData)
    {
        // TODO: Implement updateSingleRecord() method.
    }

    public function deleteSingleRecord($uuid1, $uuid2)
    {
        // TODO: Implement deleteSingleRecord() method.
    }

    public function deleteRecordByWeekReportUuid($uuid) {
        if (empty($uuid)) {
            return true;
        }

        return self::deleteAll(['week_report_uuid'=>$uuid]);
    }
}