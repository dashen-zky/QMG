<div class="panel panel-body">
<?= $this->render('form', [
    'action' => ['/stamp/import-stamp/add'],
    'formData'=> $formData,
    'series_validate_error' => $series_validate_error,
]);?>
</div>
