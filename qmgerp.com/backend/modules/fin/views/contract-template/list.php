<?php
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
use backend\models\ListIndex;
use yii\helpers\Url;
?>
<?= $this->render('@webroot/../views/site/panel-header',[
    'title'=>'合同模板列表',
])?>
    <div class="panel-body">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>模板名称</th>
                <th>附件</th>
                <th>操作</th>
            </tr>
            </thead>

            <tbody>
            <?php $i = 1;?>
            <?php foreach ($contractTemplateList['list'] as $item) :?>
                <tr>
                    <td><?= ListIndex::listIndex($i)?></td>
                    <td><?= $item['name']?></td>
                    <td>
                        <a href="<?= Url::to(['/fin/contract-template/attachment-download',
                            'path'=>$item['path']])?>">下载附件</a>
                    </td>
                    <td>
                        <div class="btn-group m-r-5 m-b-5">
                            <a href="javascript:;" data-toggle="dropdown" class="btn btn-success dropdown-toggle" aria-expanded="false">
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="<?= Url::to(['/fin/contract-template/edit','uuid'=>$item['uuid']])?>">编辑</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php $i++?>
            <?php endforeach?>
            </tbody>
        </table>
        <?= LinkPager::widget(['pagination' => $contractTemplateList['pagination']]); ?>
    </div>
<?= $this->render('@webroot/../views/site/panel-footer')?>