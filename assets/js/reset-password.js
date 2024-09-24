"use strict";

const resendSpan = document.getElementById("resendSpan");

try {
    document.getElementById('email').setAttribute('autocomplete', 'off');
    document.getElementById('captcha').setAttribute('autocomplete', 'off');
} catch (error) {}

function checkTimeValidity() {
    currentTimestamp++;
    let currentTimeMillis = currentTimestamp * 1000;
    if (currentTimeMillis > exp) {
        clearInterval(intervalID);
        deleteAllCookies();
        document.getElementsByClassName('btn-primary')[0].setAttribute("disabled", "disabled");
        showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'OTP Expired!!', 'OTP Expired, Please Try Again.');
        resendSpan.innerHTML = "<a href='/reset-password'>Resend</a>"
    } else {
        let remainingTimeMillis = exp - currentTimeMillis;
        let remainingMinutes = Math.floor(remainingTimeMillis / (1000 * 60));
        let remainingSeconds = Math.floor((remainingTimeMillis % (1000 * 60)) / 1000);

        let formattedMinutes = remainingMinutes < 10 ? "0" + remainingMinutes : remainingMinutes;
        let formattedSeconds = remainingSeconds < 10 ? "0" + remainingSeconds : remainingSeconds;

        resendSpan.innerText = "Can resend in " + formattedMinutes + ":" + formattedSeconds;
    }
}

try{
    const passResetForm = document.getElementById('formChangePass');
    passResetForm.addEventListener('submit', function (event) {
        try{clearInterval(intervalID);} catch (error) {}
        event.preventDefault();
        updatePassword();
    });
} catch (error) { }

function updatePassword() {
    const password = $('#password').val();
    const confirmPassword = $('#confirm-password').val();

    // Check if passwords match
    if (password !== confirmPassword) {
        showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Password Mismatch', 'Passwords do not match.');
        return false;
    }

    // Check if password meets length requirement
    if (password.length < 8) {
        showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Password', 'Password must be at least 8 characters long.');
        return false;
    }

    // Check if password contains at least one lowercase letter
    if (!/[a-z]/.test(password)) {
        showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Password', 'Password must contain at least one lowercase letter.');
        return false;
    }

    // Check if password contains at least one uppercase letter
    if (!/[A-Z]/.test(password)) {
        showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Password', 'Password must contain at least one uppercase letter.');
        return false;
    }

    // Check if password contains at least one digit
    if (!/\d/.test(password)) {
        showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Password', 'Password must contain at least one digit.');
        return false;
    }

    // Check if password contains at least one special character
    if (!/[@$!%*?&]/.test(password)) {
        showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Password', 'Password must contain at least one special character.');
        return false;
    }

    document.getElementsByClassName('btn-primary')[0].setAttribute("disabled", "disabled");
    showSpinner();
    let isAjaxInProgress = false;
    $.ajax({
        type: 'POST',
        url: '/api/resetPassword',
        data: {
            password: password,
            confirmPassword: confirmPassword,
            changePassword: true,
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
            if (!response.status) {
                if (response.error === "Match failed") {
                    document.getElementsByClassName('btn-primary')[0].removeAttribute("disabled");
                    showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Password Did\'t Match', 'The password and confirm password does not match.');
                } else if (response.error === "Complexity") {
                    document.getElementsByClassName('btn-primary')[0].removeAttribute("disabled");
                    showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Password Complexity', 'The password must contain at least one lowercase letter, one uppercase letter, one number, one special character, and be at least 8 characters long.');
                } else if (response.error === "Reused") {
                    document.getElementsByClassName('btn-primary')[0].removeAttribute("disabled");
                    showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Old Password', 'Please refrain from using a previous password for your new one.');
                } else if (response.error === "Session Old") {
                    showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Old Session', 'You session is too old please try again');
                    deleteAllCookies();
                    setTimeout(function () {
                        location.replace(location.href);
                    }, 5500);
                } else {
                    showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Error', 'Error');
                    deleteAllCookies();
                    setTimeout(function () {
                        location.replace(location.href);
                    }, 5500);
                }
            } else {
                showToast(5800, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Successful', 'Your password reset successfully. You will be redirected to the login screen shortly.');
                setTimeout(function () {
                    location.replace('/login');
                }, 5900);
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
}


function checkOTP(otpValue) {
    if (otpValue.length != 6) {
        return false;
    }
    document.getElementsByClassName('btn-primary')[0].setAttribute("disabled", "disabled");
    showSpinner();
    let isAjaxInProgress = false;
    $.ajax({
        type: 'POST',
        url: '/api/resetPassword',
        data: {
            otp: otpValue,
            verifyOTP: true,
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
            if (!response.status) {
                if (response.error === "Invalid") {
                    document.getElementsByClassName('btn-primary')[0].removeAttribute("disabled");
                    showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid OTP', 'You provided an invalid OTP. Please try again with a valid OTP');
                } else if (response.error === "Expired") {
                    showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid OTP', 'You provided OTP is expired. Please try again');
                    // clearInterval(intervalID);
                    deleteAllCookies();
                    setTimeout(function () {
                        location.replace(location.href);
                    }, 6000);
                }
            } else {
                showToast(5800, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Successful', 'OTP verified successfully. You will be redirected to the next screen shortly.');
                setTimeout(function () {
                    location.replace(location.href);
                }, 5900);
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
}

try {
    // Function to handle numeral inputs
    function handleNumeralInputs(event, inputElement) {
        const maxLength = parseInt(inputElement.getAttribute("maxlength"));

        // Check if input is a digit
        if (/^\d$/.test(event.key)) {
            // Move focus to next input if current input is filled
            if (inputElement.nextElementSibling && inputElement.value.length === maxLength) {
                inputElement.nextElementSibling.focus();
            }
        } else if (event.key === "Backspace") {
            // Move focus to previous input if Backspace is pressed
            if (inputElement.previousElementSibling) {
                inputElement.previousElementSibling.focus();
            }
        }
    }

    // Prevent '-' keypress in inputs
    function preventMinusKeyPress(event) {
        if (event.key === "-") {
            event.preventDefault();
        }
    }

    // Iterate through numeral inputs
    document.querySelectorAll(".numeral-mask-wrapper .numeral-mask").forEach(inputElement => {
        inputElement.addEventListener("keyup", event => handleNumeralInputs(event, inputElement));
        inputElement.addEventListener("keypress", preventMinusKeyPress);
    });

    // Form validation
    const formElement = document.querySelector("#twoStepsForm");
    if (formElement) {
        FormValidation.formValidation(formElement, {
            fields: {
                otp: {
                    validators: {
                        notEmpty: {
                            message: "Please enter OTP"
                        }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    eleValidClass: "",
                    rowSelector: ".mb-3"
                }),
                submitButton: new FormValidation.plugins.SubmitButton(),
                // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                autoFocus: new FormValidation.plugins.AutoFocus(),
            },
        });

        // Update OTP value when all numeral inputs are filled
        const numeralMaskElements = formElement.querySelectorAll(".numeral-mask");
        function updateOtpValue() {
            let allFilled = true;
            let otpValue = "";
            numeralMaskElements.forEach(element => {
                if (element.value === "") {
                    allFilled = false;
                    formElement.querySelector('[name="otp"]').value = "";
                }
                otpValue += element.value;
            });
            if (allFilled) {
                formElement.querySelector('[name="otp"]').value = otpValue;
                checkOTP(otpValue);
            }
        }

        // Attach event listener to numeral inputs for updating OTP value
        numeralMaskElements.forEach(element => {
            element.addEventListener("keyup", updateOtpValue);
        });
    }
} catch (error) { }


document.addEventListener("DOMContentLoaded", function(e) {
    // document.addEventListener("DOMContentLoaded", function(e) {
    $('#formAuthentication').submit(function(event) {
        event.preventDefault();

        const email = $('#email').val();
        const emailRegex = /@(?:(?:google|gmail|yahoo|outlook|hotmail|rediffmail|icloud|protonmail|live)\.com)$/;
        const captcha = $('#captcha').val();

        if (!emailRegex.test(email)) {
            showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Email', 'Invalid email address. Please enter an valid email address and try again');
            reloadCaptcha();
            return false;
        }

        if (captcha.length !== 6) {
            showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
            reloadCaptcha();
            return false;
        }
        document.getElementsByClassName('btn-primary')[0].setAttribute("disabled", "disabled");
        showSpinner();
        // Define a flag to track AJAX request status
        let isAjaxInProgress = false;
        $.ajax({
            type: 'POST',
            url: '/api/resetPassword',
            data: {
                email: email,
                captcha: captcha,
                generateOTP: true,
            },
            dataType: 'json',
            beforeSend: function() {
                if (isAjaxInProgress) {
                    return false;
                }
                isAjaxInProgress = true;
            },
            success: function(response) {
                // console.log(response);
                isAjaxInProgress = false;
                hideSpinner();
                if (!response.status) {
                    document.getElementsByClassName('btn-primary')[0].removeAttribute("disabled");
                    if (response.error === "Invalid Captcha") {
                        showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
                    } else if (response.error === "Invalid Email") {
                        showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Email', 'Invalid email address. Please enter a valid email address and try again');
                    } else if (response.error === "Not Found") {
                        showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Email', 'Provided email address doesn\'t associated with any registered user account. Please enter a valid email address and try again');
                    } else if (response.error === "limit") {
                        showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'OTP Generation Limit Reached', response.message + 'Please try again after some times');
                    } else if (response.error === "Account Status") {
                        showToast(6000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Account Type or Status', 'For security purposes, password resets are only available once your account has been activated by an admin. Please try again later');
                    }
                    reloadCaptcha();
                } else if (response.status && response.message === "Mail sent") {
                    showToast(6000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Email Send Successfully', 'An email containing an OTP code has been sent to your email address. You will be redirected to the next screen shortly.');
                    setTimeout(function() {
                        location.replace(location.href);
                    }, 6200);
                }
            },
            error: function(xhr, status, error) {
                // Reset the flag on error to allow making another AJAX call
                isAjaxInProgress = false;
                // Handle error
                document.getElementsByClassName('btn-primary')[0].removeAttribute("disabled");
                hideSpinner();
                // console.error("Error: " + error);
            }
        });
    });
});