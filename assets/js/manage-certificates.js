function generateAllCertAjax(captchaValue) {
    showSpinner();
    let isAjaxInProgress = false;
    $.ajax({
        url: '/api/manageCert',
        type: 'POST',
        data: {
            captcha: captchaValue,
            action: 'generateCertificates'
        },
        dataType: 'json',
        beforeSend: function () {
            if (isAjaxInProgress) {
                return false;
            }
            isAjaxInProgress = true;
        },
        success: function (response) {
            console.log(response);
            isAjaxInProgress = false;
            hideSpinner();
            if (response.status) {
                if (response.message === 'Successful') {
                    showToast(6000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Certificates Generated Successfully', 'Participation certificate(s) successfully generated. This Page Will Reload Shortly to display those certificates.');
                    setTimeout(function () {
                        location.reload();
                    }, 6200);
                }
            } else {
                if (response.error == 'Invalid Captcha') {
                    showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
                } else if (response.error == 'No User') {
                    showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'No eligible users for certificates', 'No participants meet the qualifications for receiving a participation certificate');
                } else {
                    showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', response.message);
                }
            }
        },
        error: function (xhr, status, error) {
            // Reset the flag on error to allow making another AJAX call
            isAjaxInProgress = false;
            hideSpinner();
            console.log(error);
        }
    });
}

function generateAllCert() {
    $('#captcha-modal-display').modal('show');
    reloadCaptcha();
    document.getElementById('captcha-form').addEventListener('submit', function (event) {
        if (document.getElementById('captcha').value.length == 6) {
            let captchaValue = document.getElementById('captcha').value;
            generateAllCertAjax(captchaValue);
            closeCaptchaModal();
        } else {
            showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
            reloadCaptcha();
            return false;
        }
    });
}

function makeAjaxMailCert(name, email, certificate, captchaValue) {
    showSpinner();
    let isAjaxInProgress = false;
    $.ajax({
        type: 'POST',
        url: '/api/manageCert',
        data: {
            captcha: captchaValue,
            userName: name,
            userEmail: email,
            userCertificate: certificate,
            action: 'emailCertificate'
        },
        dataType: 'json',
        beforeSend: function () {
            if (isAjaxInProgress) {
                return false;
            }
            isAjaxInProgress = true;
        },
        success: function (response) {
            // console.log(response);
            isAjaxInProgress = false;
            hideSpinner();
            if (response.status) {
                showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Email Sent', 'Certificate email sent successfully to ' + email);
            } else {
                if (response.error == 'Invalid Captcha') {
                    showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
                } else if (response.error == 'File Error') {
                    showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Error', 'Certificate File not found on the system.');
                }
            }
        },
        error: function (xhr, status, error) {
            // Reset the flag on error to allow making another AJAX call
            isAjaxInProgress = false;
            hideSpinner();
            // console.log(error);
        }
    });
}

function mailCertificate(name, email, certificate) {
    $('#captcha-modal-display').modal('show');
    reloadCaptcha();
    document.getElementById('captcha-form').addEventListener('submit', function (event) {
        if (document.getElementById('captcha').value.length == 6) {
            let captchaValue = document.getElementById('captcha').value;
            makeAjaxMailCert(name, email, certificate, captchaValue);
            closeCaptchaModal();
        } else {
            showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
            reloadCaptcha();
            return false;
        }
    });
}


function closeFileUploadModal() {
    reloadCaptcha2();
    $('#fileUpload-Modal').modal('hide');
    $('#captcha').val('');
    $('#formFile').val('');
}

function reloadCaptcha2() {
    document.getElementById('captcha2').value = '';
    let captchaImage = document.getElementById('captchaImage2');
    captchaImage.src = '/captcha?' + new Date().getTime(); // Add timestamp to force browser to reload image
}

function certUpload(type, email) {
    $('#fileUpload-Modal').modal('show');
    reloadCaptcha2();
    document.getElementById('file-upload-form').addEventListener('submit', function (event) {
        event.preventDefault();
        if (document.getElementById('captcha2').value.length == 6) {
            let fileInput = document.getElementById('formFile');
            let filePath = fileInput.value;
            let allowedExtensions = /(\.pdf)$/i;
            if (!allowedExtensions.exec(filePath)) {
                showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid File', 'Please upload file with .pdf extension only');
                fileInput.value = '';
                reloadCaptcha2();
                return false;
            } else {

                // For user email
                let userEmailParameterName = 'useremail';
                let userEmailParameterValue = email;
                let userEmailHiddenField = document.createElement('input');
                userEmailHiddenField.type = 'hidden';
                userEmailHiddenField.name = userEmailParameterName;
                userEmailHiddenField.value = userEmailParameterValue;
                this.appendChild(userEmailHiddenField);

                // For certificate type
                let certTypeParameterName = 'certType';
                let certTypeParameterValue = type;
                let certTypeHiddenField = document.createElement('input');
                certTypeHiddenField.type = 'hidden';
                certTypeHiddenField.name = certTypeParameterName;
                certTypeHiddenField.value = certTypeParameterValue;
                this.appendChild(certTypeHiddenField);

                showSpinner();
                this.submit();
                return false;
            }
        } else {
            showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
            reloadCaptcha2();
            return false;
        }
    });
}