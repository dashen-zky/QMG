<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/14 0014
 * Time: 下午 3:01
 */

namespace backend\models;

use backend\modules\rbac\model\RoleManager;
use Yii;
use yii\db\ActiveRecord;
use backend\modules\hr\models\Department;
use backend\modules\rbac\model\RBACManager;

class BaseRecord extends ActiveRecord
{
    const Enable = 127;
    const Disable = 126;
    //***************************tableName***************************//

    const EmployeeBasicInformationTableName = 'hr_employee_basic_information';
    const EmployeeAccountTableName = 'hr_employee_account';
    const EmployeePositionMapTableName = "hr_employee_position_map";
    const EmployeeFamilyTableName = "hr_employee_family";
    const EmployeeBasicAttachmentInformationTableName = 'hr_employee_basic_attachment_information';
    const DepartmentTableName = "hr_department";
    const DepartmentRelationTableName = "hr_department_relation";
    const PositionTableName = 'hr_position';
    const HrAskFormLeave = 'hr_ask_for_leave';
    const HrApplyRecruit = 'hr_apply_recruit';
    const HrCandidate = 'hr_candidate';
    const HrRecruitCandidateMap = 'hr_recruit_candidate_map';

    const DailyRegulation = 'daily_regulation';
    const DailyRegulationEmployeeMap = 'daily_regulation_employee_map';
    const DailyRegulationEditorMap = 'daily_regulation_editor_map';
    const DailyTransaction = 'daily_transaction';
    const DailyWeekReport = 'daily_week_report';
    const DailyWeekReportTransactionMap = 'daily_week_report_transaction_map';

    const DepartmentDutyMap = 'hr_department_duty_map';
    const SystemLog = 'system_log';
    const CRMCustomerBasic = 'crm_customer_basic';
    const CRMCustomerBusinessMap = 'crm_customer_business_map';
    const CRMCustomerAdvance = 'crm_customer_advance';
    const CRMTouchRecord = 'crm_touch_record';
    const CRMCustomerTouchRecordMap = 'crm_customer_touch_record_map';
    const CRMProjectTouchRecordMap = 'crm_project_touch_record_map';
    const CRMCustomerContactMap = 'crm_customer_contact_map';
    const CRMProjectContactMap = 'crm_project_contact_map';
    const CRMProjectMemberMap = 'crm_project_member_map';
    const CRMCustomerProjectMap = 'crm_customer_project_map';
    const CRMProjectBusinessMap = 'crm_project_business_map';
    const CRMProjectContractMap = 'crm_project_contract_map';
    const CRMContact = 'crm_contact';
    const CRMProject = 'crm_project';
    const CRMCustomerContractMap = 'crm_customer_contract_map';
    const CRMSupplier = 'crm_supplier';
    const CRMSupplierContactMap = 'crm_supplier_contact_map';
    const CRMSupplierReceiveAccountMap = 'crm_supplier_receive_account_map';
    const CRMSupplierContractMap = 'crm_supplier_contract_map';
    const CRMPartTime = 'crm_part_time';
    const CRMPartTimeReceiveAccountMap = 'crm_part_time_receive_account_map';
    const CRMStamp = 'crm_stamp';
    const CRMCustomerStampMap = 'crm_customer_stamp_map';
    const CRMProjectPaymentMap = 'crm_project_payment_map';
    const CRMSupplierPaymentMap = 'crm_supplier_payment_map';
    const CRMProjectAccountReceivable = 'crm_project_account_receivable_map';
    const CRMProjectApplyStamp = 'crm_project_apply_stamp';
    const CRMProjectApplyStampMap = 'crm_project_apply_stamp_map';
    const CRMSalesCustomerStatistic = 'crm_sales_customer_statistic';
    const CRMSalesAnniversaryAchievementStatistic = 'crm_sales_anniversary_achievement_statistic';
    const CRMProjectStatistic = 'crm_project_statistic';
    const CRMProjectAnniversaryAchievementStatistic = 'crm_project_anniversary_achievement_statistic';
    const CRMProjectBrief = 'crm_project_brief';
    const CRMProjectMediaBrief = 'crm_project_media_brief';

    const FinContract = 'fin_contract';
    const FinContractTemplate = 'fin_contract_template';
    const FINAccount = 'fin_account';
    const FinPayment = 'fin_payment';
    const FINStamp = 'fin_stamp';
    const FINPaymentStampMap = 'fin_payment_stamp_map';
    const FINAccountReceivable = 'fin_account_receivable';
    const FINReceiveMoneyCompany = 'fin_receive_money_company';
    //***************************tableName***************************//
    const PageSize = 100;
    const COMConfig = "com_config";
    public $rules;
    // 设置表单数据错误提示
    public function setError($errors) {
        foreach($errors as $index => $error) {
            $this->addError($index,$error[0]);
        }
    }
    // 忽略使其保持单一的规则，一般在跟新记录的时候会用到
    public function ignoreUniqueRules() {
        foreach($this->rules as $index=>$rule) {
            if($rule[1] === 'unique') {
                unset($this->rules[$index]);
            }
        }
    }

    public function updateRecordBuilder($formData, $record = null) {
        $columns = $this->getTableSchema()->columns;
        $flag = false;
        foreach ($columns as $key => $column) {
            if (isset($formData[$key])) {
                if ($record !== null) {
                    $record->$key = trim($formData[$key], ' ');
                } else {
                    $this->$key = trim($formData[$key], ' ');
                }
                $flag = true;
            }
        }
        return $flag;
    }

    // 在更新操作之前调用的函数
    /**
     *
     * 对formData的预处理
     * 将formData的数据写入到$record里面去，如果$record为空，那么就写入到$this
     * 对$record的操作
     */
    public function updatePreHandler(&$formData, $record = null) {
        $this->formDataPreHandler($formData, $record);
        if (!$this->updateRecordBuilder($formData, $record)) {
            return false;
        }
        $this->recordPreHandler($formData, $record);
        return true;
    }

    // 对表单数据进行预处理，用于更新和插入数据使用
    // 给所有的表都加一个created_uuid,便于后期删除test的数据
    public function formDataPreHandler(&$formData, $record) {
        if(empty($record)) {
            if (!isset($formData['uuid']) || empty($formData['uuid'])) {
                $formData['uuid'] = UUID::getUUID();
            }
            if (!isset($formData['created_time']) || empty($formData['created_time'])) {
                $formData['created_time'] = time();
            }
            if (!isset($formData['created_uuid']) || empty($formData['created_uuid'])) {
                $formData['created_uuid'] = Yii::$app->user->getIdentity()->getId();
            }
            $this->clearEmptyField($formData);
        }
    }
    // 在更新或是插入数据之前对自己的一些处理，
    public function recordPreHandler(&$formData, $record = null) {

    }

    // 将formData里面的空的地方删除掉,提升效率
    public function clearEmptyField(&$formData) {
        foreach($formData as $index => $value) {
            if(empty($formData[$index])) {
                unset($formData[$index]);
            }
        }
    }

    // 将form里面输入的时间字符串转换成时间戳，
    // 如果输入为空的话，将这个字段unset掉
    public function handlerFormDataTime(&$formData, $index) {
        if(isset($formData[$index]) && !empty($formData[$index])) {
            $formData[$index] = strtotime($formData[$index]);
        } elseif(empty($formData[$index])) {
            unset($formData[$index]);
        }
    }

    // 通过功能模块获取当前人员所有对应功能下面的员工uuids
    public function getOrdinateUuids($module) {
        $userId = Yii::$app->user->getIdentity()->getId();
        $departments = Department::getDepartmentUuidsFromUserId($userId);
        $uuids = Yii::$app->authManager->getOrdinateFromUserId(
            $userId, $module,$departments);
        array_push($uuids, $userId);
        return $uuids;
    }

    /**
     * 获取的成员不包括自己，ceo除外
     * @param $module
     * @return mixed
     */
    public function getOrdinateUuidsWithoutSelf($module) {
        $userId = Yii::$app->user->getIdentity()->getId();
        $departments = Department::getDepartmentUuidsFromUserId($userId);
        $uuids = Yii::$app->authManager->getOrdinateFromUserId(
            $userId, $module,$departments);
        $roles = array_keys(Yii::$app->authManager->getRolesByUser($userId));
        if(in_array(RoleManager::ceo, $roles)) {
            array_push($uuids, $userId);
        }
        return $uuids;
    }


    /**
     * 从数据库记录数组里面获取我们想要的那个字段的值
     * @param $records 是从数据库里面获取到记录数组
     * @param $key 是数据表里面的字段名字
     * @param $reverse 是否反转，就是将健和值反转
     * @param array $defaultValue 默认值
     * @return array
     */
    public function getAppointedValue($records, $key, $reverse = false,$defaultValue = []) {
        $_return = $defaultValue;
        if (empty($records)) {
            return $_return;
        }
        foreach($records as $index => $record) {
            if ($reverse) {
                $_return[$record[$key]] = $index;
            } else {
                $_return[$index] = $record[$key];
            }
        }
        return $_return;
    }

    /**
     * 将数据库里面查询出来的数组转化一下
     * 这个函数只是适用于只有两个字段
     * 可以将其转化成dropDownList希望的样式
     * [
     *  0=>[
     *          'uuid'=>'uuid0',
     *          'name'=>'name0'
     *      ]
     * 1=>[
     *          'uuid'=>'uuid1',
     *          'name'=>'name1'
     *      ]
     * ]
     * 转化成下面这样
     * [
     *      'uuid0'=>'name0'
     *      'uuid1'=>'name1'
     * ]
     */
    public function transformForDropDownList($records, $index, $value, $postfix = null) {
        $_return = [];
        foreach($records as $record) {
            if(!isset($record[$index])) {
                continue;
            }
            $_return[$record[$index]] = $record[$value] . (!empty($postfix)?'---' . $record[$postfix] : '');
        }
        return $_return;
    }

    // 用在groupcap中过滤重复的信息
    public function filterRepeatFieldAsArray($indexs, $values) {
        if (empty($values)) {
            return null;
        }
        $_return  = [];
        $indexList = explode(",",$indexs);
        $valueList = explode(",",$values);
        for($i = 0; $i < count($valueList); $i++) {
            $_return[$indexList[$i]] = $valueList[$i];
        }
        return $_return;
    }
}