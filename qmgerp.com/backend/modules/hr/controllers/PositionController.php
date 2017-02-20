<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3 0003
 * Time: 下午 6:07
 */

namespace backend\modules\hr\controllers;

use backend\modules\hr\models\Department;
use backend\modules\hr\models\DepartmentForm;
use backend\modules\hr\models\DepartmentRelation;
use backend\modules\hr\models\Position;
use Yii;
use backend\modules\hr\models\PositionForm;
use yii\base\Response;
use yii\web\Controller;
use yii\helpers\Json;
use yii\base\Security;
use backend\models\CompressHtml;

class PositionController extends Controller
{
    const PositionList = '职位列表';
    const EditPosition = "编辑职位";
    public $position;

    public function actionIndex() {
        $positionModel = new PositionForm();
        // 获取跟部门，作为department的初始值
        $position = new Position();
        $positionList = $position->allPositionList();
        $department = new Department();
        $root = $department->getDepartmentsForDropDownList(1);

        $data = [
            'model'=>$positionModel,
            'positionList' => $positionList,
            'formData'=>[
                'department'=>[
                    1=>$root,
                ]
            ],
            'filter_form_data'=>[
                'department'=> [
                    1=>$root,
                ]
            ],
        ];

        return $this->render('index', $data);
    }

    public function actionAdd() {
        $formData = Yii::$app->request->post('PositionForm');
        $position = new Position();
        if ($position->insertRecord($formData)) {
            $this->redirect(['index']);
        } else {

        }
    }

    public function actionDelete() {
        $uuid = Yii::$app->request->get('uuid');
        $position = new Position();
        if($position->deleteRecordByUuid($uuid)) {
            $this->redirect(['index']);
        } else {
            $this->render('/site/error',[
                'errorMessage'=>"删除职位失败，请点击返回按钮返回",
                'backUrl'=>'/hr/employee/index',
            ]);
        }
    }

    public function actionDepartmentList() {
        $uuid = Yii::$app->request->get('uuid');
        $departmentMap = new DepartmentRelation();
        return CompressHtml::compressHtml($departmentMap->getChildrenForDropDownList($uuid));
    }


    public function actionEdit() {
        $uuid = Yii::$app->request->get('uuid');
        $position = new Position();
        $formData = $position->getRecordByUuid($uuid);
        return CompressHtml::compressHtml($this->renderPartial('position-form',[
            'formData'=>$formData,
            'model'=>new PositionForm(),
            'action'=>['/hr/position/update']
        ]));
    }

    public function actionUpdate() {
        $formData = Yii::$app->request->post('PositionForm');
        $position = new Position();
        $result = $position->updateRecord($formData);
        if($result || $result === 0) {
            $this->redirect(['index']);
        } else {
            $this->render('/site/error',[
                'errorMessage'=>"修改职位失败，点击返回按钮返回！！！",
                'backUrl'=>'/hr/position/index',
            ]);
        }
    }

    public function actionListFilter() {
        $position = new Position();
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        if(!isset($filter['department']) || empty($filter['department'])) {
            return null;
        }
        $position->clearEmptyField($filter['department']);
        $positionList = $position->listFilter($filter);
        $model = new PositionForm();
        $department = new Department();
        $department_1 = $department->getDepartmentsForDropDownList(1);
        if(isset($filter['department'][1])) {
            $department_2 = $department->getDepartmentsForDropDownList(2, $filter['department'][1]);
        }

        if(isset($filter['department'][2])) {
            $department_3 = $department->getDepartmentsForDropDownList(3, $filter['department'][2]);
        }

        $data = [
            'model'=>$model,
            'positionList' => $positionList,
            'formData'=>[
                'department'=>[
                    1=>$department_1,
                ]
            ],
            'ser_filter'=>serialize($filter),
            'filter_form_data'=>[
                'department'=> [
                    1=>$department_1,
                    2=>isset($department_2)?$department_2:[],
                    3=>isset($department_3)?$department_3:[],
                ],
                'department_level_1'=>isset($filter['department'][1])?$filter['department'][1]:0,
                'department_level_2'=>isset($filter['department'][2])?$filter['department'][2]:0,
                'department_level_3'=>isset($filter['department'][3])?$filter['department'][3]:0,
            ],
        ];

        return $this->render('index', $data);
    }
}