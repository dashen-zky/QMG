<?php
namespace backend\modules\wom_data\controllers;
use backend\modules\wom_data\models\SupplierDataExchange;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * Supplier Controller
 */
class SupplierSoapController extends Controller
{
    public function actions()
    {
        return [
            'soap' => [
                'class' => 'conquer\services\WebServiceAction',
                'wsdlUrl'=>\Yii::$app->getRequest()->getHostInfo() . \Yii::$app->urlManager->createUrl([
                    '/wom_data/supplier/soap'
                ]),
                'serviceUrl'=>\Yii::$app->getRequest()->getHostInfo() . Url::to([
                    '/wom_data/supplier/soap',
                ]) . '&ws=1',
                'classMap' => [
                    'MyClass' => 'app\controllers\MyClass'
                ],
            ],
        ];
    }

    /**
     * @param mixed $formData
     * @return mixed
     * @soap
     */
    public function addSupplier($formData)
    {
        $supplier = new SupplierDataExchange();
        return $supplier->insertRecord($formData);
    }

    /**
     * @param array $formData
     * @return mixed
     * @soap
     */
    public function updateSupplier($formData) {
        $supplier = new SupplierDataExchange();
        return $supplier->updateRecord($formData);
    }
}