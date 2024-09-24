<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(404);
    include($_SERVER["DOCUMENT_ROOT"]."/404.html");
    exit();
}
?>

<!-- toast -->
<div class="bs-toast toast toast-ex animate__animated my-2 fade hide" id="liveToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5500">
    <div class="toast-header">
        <i class="mdi mdi-home me-2"></i>
        <div class="me-auto fw-medium"></div>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
    </div>
</div>