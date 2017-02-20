<?php
use yii\helpers\Html;
?>
<!-- #modal-without-animation -->
<div class="modal fade scroll refuse-reason-modal" style="width: 35%; margin: 80px auto;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">不通过理由</h4>
        </div>
        <div class="modal-body">
            <?= Html::beginForm(['/crm/project/done-assess-refused'], 'post', [
                'class' => 'form-horizontal',
                'data-parsley-validate' => "true",
            ])?>
            <input hidden name="uuid" class="uuid">
            <table class="table">
                <tr>
                    <td>
                        <?= Html::textarea('refuse_reason',
                            null,[
                                'class'=>'form-control',
                                'rows'=>5
                            ])?>
                    </td>
                </tr>
                <tr>
                    <td></td>
                </tr>
            </table>
            <span class="col-md-12">
            <span class="col-md-4"></span>
            <span class="col-md-4">
                <?= Html::submitButton('提交', [
                    'class'=>'form-control btn-primary submit'
                ])?>
            </span>
            <span class="col-md-4"></span>
        </span>
            <?= Html::endForm()?>
        </div>
        <div class="modal-footer">
        </div>
    </div>
</div>