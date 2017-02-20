<?php
use backend\models\AjaxLinkPage;
use yii\helpers\Url;
use yii\bootstrap\Html;
use backend\modules\hr\recruitment\models\CandidateConfig;
$config = new CandidateConfig();
?>
<div class="candidate-list-container">
    <table class="table">
        <thead>
        <tr>
            <th>#</th>
            <th>姓名</th>
            <th>联系方式</th>
            <th>应聘岗位</th>
            <th>预约时间</th>
            <th>状态</th>
            <th>候选人</th>
            <th>简历</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($candidateList['list'] as $item) :?>
            <tr>
                <td><?= $item['id']?></td>
                <td>
                    <a url="<?= Url::to([
                        '/recruitment/interview/show-candidate',
                        'candidate_uuid'=>$item['candidate_uuid'],
                        'recruit_uuid'=>$item['recruit_uuid'],
                    ])?>" class="show" href="#">
                        <?= $item['candidate_name']?>
                    </a>
                </td>
                <td>
                    <div><?= $item['candidate_phone']?></div>
                    <div><?= $item['candidate_email']?></div>
                </td>
                <td><?= $item['position_name']?></td>
                <td><?= (isset($item['interview_time']) && $item['interview_time'] != 0)?
                        date('Y-m-d H:i',$item['interview_time']):null?></td>
                <td><?= $config->getAppointed('status', $item['status'])?></td>
                <td><?= $item['interview_name']?></td>
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
                <td>
                    <?php if($item['status'] == CandidateConfig::StatusNotifyInterView) :?>
                    <div class="btn-group m-r-5 m-b-5">
                        <a href="javascript:;" data-toggle="dropdown" class="btn btn-success dropdown-toggle" aria-expanded="false">
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="<?= Url::to([
                                    '/recruitment/interview/hire',
                                    'id'=>$item['id'],
                                ])?>">录用</a></li>
                            <li><a href="<?= Url::to([
                                    '/recruitment/interview/dis-hire',
                                    'id'=>$item['id'],
                                ])?>">不录用</a></li>
                            <li><a href="<?= Url::to([
                                    '/recruitment/interview/push-to-talent',
                                    'id'=>$item['id'],
                                ])?>">人才库</a></li>
                            <li><a href="<?= Url::to([
                                    '/recruitment/interview/push-to-black-list',
                                    'id'=>$item['id'],
                                ])?>">黑名单</a></li>
                        </ul>
                    </div>
                    <?php endif;?>
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
