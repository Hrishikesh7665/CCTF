const form = document.getElementById('formAuthenticationLdap');

sessionCustomClear('notificationHashes');

// Add event listener for form submission
form.addEventListener('submit', function (event) {
    // Prevent the default form submission behavior
    event.preventDefault();
    const captchaValue = $("#captcha").val().trim();

    const passwordValue = document.getElementById("password").value;

    if (captchaValue.length !== 6) {
        showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
        reloadCaptcha();
        return false;
    }

    let isAjaxInProgress = false;

    document.getElementsByClassName('btn-primary')[0].setAttribute("disabled", "disabled");
    showSpinner();
    $.ajax({
        type: 'POST',
        url: '/api/new_registration',
        data: {
            captcha: captchaValue,
            password: passwordValue,
            registerNewLdapUser: true
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
            document.getElementsByClassName('btn-primary')[0].removeAttribute("disabled");
            hideSpinner();
            if (response.status) {
                if (response.message === 'Successfully registered') {
                    document.getElementsByClassName('btn-primary')[0].setAttribute("disabled", "disabled");
                    showToast(9500, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Congratulation', 'Your account successfully created. For security purposes, you will be able to log in after 15 minutes. You Will Redirect Shortly.');
                    closeCaptchaModal();
                    setTimeout(function () {
                        location.replace('/login');
                    }, 9800);
                }
            } else {
                // Handle response when status is false
                if (response.error === 'Invalid Captcha') {
                    showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
                    reloadCaptcha();
                } else if (response.error === 'Wrong Password') {
                    showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Wrong Password', 'Password verification failed. Please try again with a valid password');
                    reloadCaptcha();
                } else {
                    showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', response.error, response.message);
                    reloadCaptcha();
                    closeCaptchaModal();
                }
            }
        },
        error: function (xhr, status, error) {
            // Reset the flag on error to allow making another AJAX call
            isAjaxInProgress = false;
            // Handle error
            document.getElementsByClassName('btn-primary')[0].removeAttribute("disabled");
            hideSpinner();
            // console.error("Error: " + error);
        }
    });
});
