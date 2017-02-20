<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use yii\helpers\Url;
use backend\modules\rbac\model\PermissionManager;
use backend\modules\daily\models\regulation\Regulation;
use backend\modules\rbac\model\RBACManager;
use backend\modules\daily\models\regulation\RegulationConfig;
$config = new RegulationConfig();
?>
<!-- begin panel -->
<div class="panel panel-body">
<?php Pjax::begin(); ?>
    <?= $this->render('list-filter-form',[
        'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
    ]);?>
<?php
$Js = <<<Js
$(function() {
    //查看文档
    $('.regulation-list').on('click', '.show-regulation', function() {
        var td = $(this).parents('td');
        var content = td.find('.regulation-content')[0].innerHTML;
        var modal = $('.regulation-modal');
        var title = $(this).html();
        var path = td.find('.regulation-path')[0].innerHTML;
        modal.find('.modal-title').html(title);
        modal.find('.modal-body').html(content);
        modal.find('.attachment').html(path);
        modal.modal('show');
    });
    
    $(".datetimepicker").datetimepicker({
        lang:"ch",           //语言选择中文
        format:"Y-m-d",      //格式化日期H:i
        i18n:{
          // 以中文显示月份
          de:{
            months:["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月",],
            // 以中文显示每周（必须按此顺序，否则日期出错）
            dayOfWeek:["日","一","二","三","四","五","六"]
          }
        }
        // 显示成年月日，时间--
    });
})
Js;
$this->registerJs($Js, \yii\web\View::POS_END);
?>
<div class="panel-body regulation-list">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>编号</th>
            <th class="col-md-3">标题</th>
            <th>标签</th>
            <th>类型</th>
            <th class="col-md-3">摘要</th>
            <th>更新日期</th>
            <th>文档状态</th>
            <?php if($operator):?>
            <th>操作</th>
            <?php endif?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($list['list'] as $item) :?>
            <tr>
                <td><?= $item['code']?></td>
                <td>
                    <span class="regulation-path" style="display: none">
                        <?php if(!empty($item['path'])) :?>
                        <?php $item['path'] = unserialize($item['path']); ?>
                        <?php foreach($item['path'] as $key=>$path) :?>
                            <div style="float: left; margin-right: 30px">
                                <a href="<?= Url::to([
                                    '/daily/regulation/attachment-download',
                                    'path'=>$path,
                                    'file_name'=>$key,
                                ])?>"><?= $key?></a>
                            </div>
                        <?php endforeach?>
                        <?php endif;?>
                    </span>
                    <span class="regulation-content" style="display: none;"><?= $item['content']?></span>
                    <a class="show-regulation" href="#"><?= $item['title']?></a>
                </td>
                <td><?= $item['tags']?></td>
                <td>
                    <?= Regulation::getType($item['type'])?>
                </td>
                <td><?= $item['abstract']?></td>
                <td><?= ($item['update_time'] != 0)?date("Y-m-d",$item['update_time']):''?></td>
                <td><?= $config->getAppointed('enable', $item['enable'])?></td>
            <?php if($operator):?>
                    <td>
                        <?php
                        if(Yii::$app->authManager->canAccess(PermissionManager::EditRegulation, [
                            'manager_uuid'=> explode(',', $item['created_uuid'] . ',' . $item['editor_uuid']),
                            'module'=>RBACManager::Administer,
                        ]) || Yii::$app->user->getIdentity()->getUserName() == 'admin') {?>
                        <div>
                            <a href="<?= Url::to([
                                '/daily/regulation/edit',
                                'uuid'=>$item['uuid'],
                                'tab'=>'edit-regulation',
                            ])?>">编辑</a>
                        </div>
                        <?php }?>
                        <?php if(Regulation::canDisable($item['created_uuid'], $item['enable'])) :?>
                            <div>
                                <a href="<?= Url::to([
                                    '/daily/regulation/disable',
                                    'uuid'=>$item['uuid']
                                ])?>">作废</a>
                            </div>
                        <?php endif;?>
                        <?php if(Yii::$app->authManager->canAccess(PermissionManager::DelRegulation)) :?>
                        <div>
                            <a href="<?= Url::to([
                                '/daily/regulation/del',
                                'uuid'=>$item['uuid']
                            ])?>">删除</a>
                        </div>
                        <?php endif?>
                    </td>
            <?php endif?>
            </tr>
        <?php endforeach?>
        </tbody>
    </table>
    <?php
    if(isset($ser_filter) && !empty($ser_filter)) {
        $pageParams = [
            'pagination' => $list['pagination'],
            'ser_filter'=>$ser_filter,
        ];
    } else {
        $pageParams = [
            'pagination' => $list['pagination'],
        ];
    }
    ?>
    <?= MyLinkPage::widget($pageParams); ?>
</div>
<?php Pjax::end(); ?>
<?= $this->render('show')?>
</div>
<!-- end panel -->
