<div class="panel-body">
<?= $this->render('form', [
    'action' => ['/stamp/export-stamp/add'],
    'formData'=> $formData,
    'series_validate_error' => $series_validate_error,
]);?>
</div>
