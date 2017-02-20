<?php
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
use yii\helpers\Url;
use backend\models\ListIndex;
use backend\modules\fin\models\account\models\FINAccountForm;
?>
<!-- begin panel -->
<?= $this->render('@webroot/../views/site/panel-header',[
    'title'=>'账户列表',
    'panelClass'=>'fin-account-list'
])?>
<?php Pjax::begin(); ?>
<table class="table table-striped">
    <thead>
    <tr>
        <th>#</th>
        <th>名字</th>
        <th>类型</th>
        <th>开户行</th>
        <th>账号</th>
        <?php if(!isset($operator) || (isset($operator) && $operator)) :?>
        <th>操作</th>
        <?php endif?>
    </tr>
    </thead>
    <tbody>
    <?php
    $i = 1;
    $model = new FINAccountForm();
    ?>
    <?php foreach ($finAccountList['list'] as $item) :?>
        <tr>
            <td><?= ListIndex::listIndex($i)?></td>
            <td><?= $item['name']?></td>
            <td><?= $model->getType($item['type'])?></td>
            <td><?= $item['bank_of_deposit']?></td>
            <td><?= $item['account']?></td>
            <td>
        <?php if(!isset($operator) || (isset($operator) && $operator)) :?>
                <div class="btn-group m-r-5 m-b-5">
                    <a href="javascript:;" data-toggle="dropdown" class="btn btn-success dropdown-toggle" aria-expanded="false">
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="<?= Url::to([
                                $delUrl,
                                'uuid'=>$item['uuid'],
                                'object_uuid'=>isset($object_uuid)?$object_uuid:'',
                            ])?>">删除</a></li>
                    </ul>
                </div>
            <?php endif?>
            </td>
        </tr>
        <?php $i++?>
    <?php endforeach?>
    </tbody>
</table>
<?= LinkPager::widget(['pagination' => $finAccountList['pagination']]); ?>
<?php Pjax::end(); ?>
<?= $this->render('@webroot/../views/site/panel-footer')?>
<!-- end panel -->