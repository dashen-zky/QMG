<?php
use backend\models\AjaxLinkPage;
use yii\helpers\Url;
use yii\bootstrap\Html;
use backend\modules\hr\recruitment\models\CandidateConfig;
$config = new CandidateConfig();
?>
<div class="candidate-list-container">
<?= Html::beginForm(['/recruitment/recruitment-candidate/drop-recruit-and-candidate-relation'], 'post', [
    'class' => 'form-horizontal RecruitmentCandidateForm',
])?>
<a href="#" class="select-all">全选</a>
<table class="table">
    <thead>
    <tr>
        <th>选择</th>
        <th>#</th>
        <th>姓名</th>
        <th>电话</th>
        <th>邮箱</th>
        <th>状态</th>
        <th class="col-md-4">备注</th>
        <th>简历</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($candidateList['list'] as $item) :?>
        <tr>
            <td>
                <input value="<?= $item['candidate_uuid']?>"
                       name="RecruitmentCandidateForm[candidate_uuid][]"
                       type="checkbox"
                       class="candidate-uuid">
            </td>
            <td><?= $item['candidate_id']?></td>
            <td>
                <a url="<?= Url::to([
                    '/recruitment/recruitment-candidate/show-candidate',
                    'candidate_uuid'=>$item['candidate_uuid'],
                    'recruit_uuid'=>$recruit_uuid,
                ])?>" class="show" href="#">
                    <?= $item['candidate_name']?>
                </a>
            </td>
            <td><?= $item['candidate_phone']?></td>
            <td><?= $item['candidate_email']?></td>
            <td><?= $config->getAppointed('status', $item['status'])?></td>
            <td><?= $item['candidate_remarks']?></td>
            <td>
                <?php if(isset($item['candidate_resume']) && !empty($item['candidate_resume'])) :?>
                    <?php
                    // 将attachment字段解析出来
                    $item['candidate_resume'] = unserialize($item['candidate_resume']);
                    ?>
                    <?php foreach($item['candidate_resume'] as $key=>$path) :?>
                        <div>
                            <a href="<?= Url::to([
                                '/recruitment/candidate/resume-download',
                                'path'=>$path,
                                'file_name'=>$key,
                            ])?>"><?= $key?></a>
                        </div>
                    <?php endforeach?>
                <?php endif?>
            </td>
        </tr>
    <?php endforeach?>
    </tbody>
</table>
<?php
if(isset($ser_filter) && !empty($ser_filter)) {
    $pageParams = [
        'pagination' => $candidateList['pagination'],
        'ser_filter'=>$ser_filter,
    ];
} else {
    $pageParams = [
        'pagination' => $candidateList['pagination'],
    ];
}
?>
<?= AjaxLinkPage::widget($pageParams); ?>
<input hidden name="RecruitmentCandidateForm[recruit_uuid]" value="<?= $recruit_uuid?>">
<?php if(!empty($candidateList['list'])) :?>
<span class="col-md-4"></span>
<span class="col-md-4">
    <span class="col-md-6" style="float: left">
    <input type="submit" value="删除候选人"
           class="form-control btn-primary">
    </span>
    <span class="col-md-6" style="float: right">
    <input type="button" value="通知面试" url="<?= Url::to([
        '/recruitment/recruitment-candidate/notify-interview'
    ])?>"
           class="notify-interview-submit form-control btn-primary">
    </span>
</span>
<span class="col-md-4"></span>
<?php endif;?>
<?php Html::endForm();?>
</div>
<?php
$Js = <<<Js
$(function() {
    $('.list .pagination').on('click', 'li', function() {
        pagination($(this));
    });
    
    $('.candidate-list-container').on('click','.select-all',function() {
        var container = $(this).parents('.candidate-list-container');
        $.each(container.find('table .candidate-uuid'), function() {
            var checked = $(this).attr('checked');
            if(checked != 'checked') {
                $(this).attr('checked', 'checked');
            }
        });
    });
    
    $('.RecruitmentCandidateForm').on('click', '.notify-interview-submit', function() {
        var form = $(this).parents('form');
        form.attr('action', $(this).attr('url'));
        form.submit();
    });
});
Js;
$this->registerJs($Js, \yii\web\View::POS_END);
?>
