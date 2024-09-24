<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(404);
    include($_SERVER["DOCUMENT_ROOT"]."/404.html");
    exit();
}

function basicModal(){
    echo <<<HTML
        <!-- modals -->
        <div class="modal fade" id="modal-display-challenge" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between align-items-start">
                        <h4 class="modal-title underline2 align-self-start" id="challenge-id"></h4>
                        <button type="button" class="btn-close custom-close-btn align-self-start" onclick="closeModal()" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-start mb-4">
                            <h5 class="mb-2 pb-1" id="challenge-title"></h5>
                            <p id="challenge-desc"></p>
                        </div>

                        <form id="flagSubmit" class="row g-3" onsubmit="return false">
                            <div class="col-12">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input id="flag" name="flag" class="form-control" type="text" placeholder="Flag" aria-describedby="flag" autocomplete="off" required />
                                            <label for="flag">Flag</label>
                                            <input id="cid" name="cid" class="form-control" type="hidden" value="" />
                                        </div>
                                    </div>
                                </div>
                            <div class="col-12 text-center">
                                <button type="button" id="flag-submit" class="btn btn-primary" onclick="flagSubmit()">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    HTML;
}

function captchaModal(){
    echo <<<HTML
        <!-- captcha modal -->
        <div class="modal fade" id="captcha-modal-display" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Verification</h5>
                        <button type="button" class="btn-close" aria-label="Close" onclick="closeCaptchaModal()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 text-center">
                            <label for="captcha" class="fw-bold">Please verify that you're human</label>
                            <div class="d-flex justify-content-center align-items-center">
                                <img src="/captcha" alt="Captcha" id="captchaImage" class="me-2">
                                <button type="button" onclick="reloadCaptcha()" class="btn btn-link">
                                    <i class="bi bi-arrow-clockwise"></i> Refresh
                                </button>
                            </div>
                        </div>
                        <form id="captcha-form" class="row g-3" onsubmit="return false">
                            <div class="col-12">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline mb-3">
                                        <input type="text" class="form-control" id="captcha" name="captcha" placeholder="Enter the captcha" autocomplete="off" required>
                                        <label for="captcha">Captcha</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <button type="button" class="btn btn-secondary" onclick="closeCaptchaModal()">Back</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    HTML;
}


function IntroModal(){
    echo <<<HTML
        <!-- intro modal -->
        <!-- modal-dialog modal-dialog-centered -->
        <div class="modal fade" id="instructions-modal-display" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- <div class="modal-header">
                        <h5 class="modal-title" style="text-align: center; width: 100%;">CTF Platform Instructions</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div> -->
                    <div class="modal-body">
                        <div class="mb-3">
                            <img src="/assets/img/HowToPlay.png" class="img-fluid" alt="How to Play the CTF">
                        </div>
                        <div class="mb-3 text-center">
                            <div class="d-flex justify-content-center align-items-center">
                                <a href="/assets/pdf/CTF_Manual.pdf" download class="btn btn-outline-primary btn-lg">
                                    <i class="bi bi-download"></i> Download Manual
                                </a>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <a id="intoModal-close" href="javascript:void(0);" style="text-decoration: none;" onmouseover="this.style.textDecoration='none';" onmouseout="this.style.textDecoration='none';">
                                Thanks, I know how to play CTF!
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    HTML;
}


?>