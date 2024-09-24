const termsCheckbox = document.getElementById('tc-checkbox');
const finalCheckbox = document.getElementById('finalCheckbox');
const welcomeModalElement = document.getElementById('welcome-modal');
const studentCheckbox = document.getElementById("studentCheckbox");
const employeeCheckbox = document.getElementById("employeeCheckbox");
const studentLabel = document.getElementById("studentLabel");
const employeeLabel = document.getElementById("employeeLabel");
const confirmButton = document.getElementById("confirm-btn");
const inputPasswordField = document.getElementById("multiStepsPass");
const inputConfirmPasswordField = document.getElementById("multiStepsConfirmPass");
const selectDesignationField = document.getElementById("multiStepsDesignation");
const select2Elements = $(".select2");
const multiStepsContainer = document.querySelector("#multiStepsRegistration");
const welcomeModal = new bootstrap.Modal(welcomeModalElement, {
    keyboard: false
});

sessionCustomClear('notificationHashes');

let userProfession = "";

function closeTab() {
    window.location.href = 'about:blank';
}

function toggleRole(role) {
    employeeLabel.classList.remove('blink_me');
    studentLabel.classList.remove('blink_me');
    let removeSelect = document.getElementById("removeSelect");
    if (removeSelect) {
        // removeSelect.parentNode.removeChild(removeSelect);
        removeSelect.innerHTML = 'of <a class="text-decoration-none" href="https://www.cdac.in">CDACINDIA</a>.';
    }

    studentLabel.classList.remove('selected-label');
    employeeLabel.classList.remove('selected-label');
    if (role === 'student') {
        employeeLabel.style.textDecoration = "line-through";
        employeeLabel.style.textDecorationThickness = "2px";

        studentLabel.classList.add('selected-label');
        studentLabel.style.textDecoration = "none";
        studentCheckbox.checked = true;
        employeeCheckbox.checked = false;
    } else if (role === 'employee') {
        studentLabel.style.textDecoration = "line-through";
        studentLabel.style.textDecorationThickness = "2px";

        employeeLabel.classList.add('selected-label');
        employeeLabel.style.textDecoration = "none";
        studentCheckbox.checked = false;
        employeeCheckbox.checked = true;
    }
    if (termsCheckbox.checked && (studentCheckbox.checked || employeeCheckbox.checked)) {
        confirmButton.removeAttribute("disabled");
    } else {
        confirmButton.setAttribute("disabled", "disabled");
    }
}

function toggleTC() {
    if (termsCheckbox.checked && (studentCheckbox.checked || employeeCheckbox.checked)) {
        confirmButton.removeAttribute("disabled");
    } else {
        confirmButton.setAttribute("disabled", "disabled");
    }
}

function toggleFinalCheckbox() {
    if (finalCheckbox.checked) {
        document.getElementsByClassName('btn-submit')[0].removeAttribute("disabled");
    } else {
        document.getElementsByClassName('btn-submit')[0].setAttribute("disabled", "disabled");
    }
}

function checkDisabledAttribute() {
    var buttons = document.querySelectorAll('.btn-submit[disabled], .btn-primary[disabled]');
    if (buttons.length > 0) {
        return true;
    } else {
        return false;
    }
}

document.addEventListener("DOMContentLoaded", function (event) {
    $(function () {
        select2Elements.length &&
            select2Elements.each(function () {
                var element = $(this);
                select2Focus(element);
                element.wrap('<div class="position-relative"></div>');
                element.select2({
                    dropdownParent: element.parent()
                });
            });
    });


    confirmButton.addEventListener("click", function () {

        if (termsCheckbox.checked && (studentCheckbox.checked || employeeCheckbox.checked)) {
            if (employeeCheckbox.checked) {
                inputPasswordField.parentNode.parentNode.parentNode.style.display = "none";
                inputConfirmPasswordField.parentNode.parentNode.parentNode.style.display = "none";
                userProfession = 'employee';
            } else if (studentCheckbox.checked) {
                multiStepsDesignation.parentNode.parentNode.parentNode.style.display = "none";
                userProfession = 'student';
            }
            welcomeModal.hide();
        } else {
            return false;
        }
        if (null !== multiStepsContainer) {
            const multiStepsForm = multiStepsContainer.querySelector("#multiStepsForm");
            const accountDetailsValidation = multiStepsForm.querySelector("#accountDetailsValidation");
            const personalInfoValidation = multiStepsForm.querySelector("#personalInfoValidation");
            const btnNextList = [].slice.call(multiStepsForm.querySelectorAll(".btn-next"));
            const btnPrevList = [].slice.call(multiStepsForm.querySelectorAll(".btn-prev"));

            let stepperInstance = new Stepper(multiStepsContainer, {
                linear: !0
            });

            const accountDetailsValidator = FormValidation.formValidation(accountDetailsValidation, {
                fields: {
                    multiStepsFullName: {
                        validators: {
                            notEmpty: {
                                message: "Please enter username"
                            },
                            stringLength: {
                                min: 4,
                                max: 40,
                                message: "The name must be more than 4 and less than 40 characters long"
                            },
                            regexp: {
                                regexp: /^[a-zA-Z. ]{4,}(?: [a-zA-Z]+)+$/,
                                message: "The name can only consist of alphabet, dot and space"
                            },
                        },
                    },
                    multiStepsEmail: {
                        validators: {
                            notEmpty: {
                                message: "Please enter email address"
                            },
                            emailAddress: {
                                message: "The value is not a valid email address"
                            },
                            regexp: {
                                regexp: (userProfession === 'employee') ?
                                    /@cdac\.in$/ : /@(?:(?:google|gmail|yahoo|outlook|hotmail|rediffmail|icloud|protonmail|live)\.com)$/,
                                message: (userProfession === 'employee') ?
                                    "Only CDAC email addresses are allowed" : "Only email addresses with domains google, yahoo, live, outlook, hotmail, rediffmail, icloud, protonmail are allowed"
                            }
                        }
                    },
                    multiStepsPass: {
                        validators: {
                            notEmpty: {
                                message: "Please enter password"
                            },
                            stringLength: {
                                min: 8,
                                message: "The password must be at least 8 characters long"
                            },
                            regexp: {
                                regexp: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/,
                                message: "The password must contain at least one lowercase letter, one uppercase letter, one number, one special character, and be at least 8 characters long"
                            }
                        }
                    },
                    multiStepsConfirmPass: {
                        validators: {
                            notEmpty: {
                                message: "Confirm Password is required"
                            },
                            identical: {
                                compare: function () {
                                    return accountDetailsValidation.querySelector('[name="multiStepsPass"]').value;
                                },
                                message: "The password and its confirm are not the same",
                            },
                        },
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5({
                        eleValidClass: "",
                        rowSelector: ".col-sm-6"
                    }),
                    autoFocus: new FormValidation.plugins.AutoFocus(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                },
                init: (formInstance) => {
                    formInstance.on("plugins.message.placed", function (event) {
                        event.element.parentElement.classList.contains("input-group") && event.element.parentElement.insertAdjacentElement("afterend", event.messageElement);
                    });
                },
            }).on("core.form.valid", function () {
                stepperInstance.next();
            });

            const personalInfoValidator = FormValidation.formValidation(personalInfoValidation, {
                fields: {
                    multiStepsMobile: {
                        validators: {
                            notEmpty: {
                                message: "Please enter your mobile number"
                            },
                            stringLength: {
                                min: 10,
                                max: 10,
                                message: "Mobile number must need to be 10 digit"
                            }
                        },
                    },
                    multiStepsCenter: {
                        validators: {
                            notEmpty: {
                                message: "Please select your CDAC Center"
                            }
                        },
                    },
                    multiStepsDesignation: {
                        validators: {
                            notEmpty: {
                                message: "Please select your Designation"
                            }
                        },
                    }
                },

                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5({
                        eleValidClass: "",
                        rowSelector: ".col-sm-6"
                    }),
                    autoFocus: new FormValidation.plugins.AutoFocus(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                },
            }).on("core.form.valid", function () {
                let tableCells = document.querySelectorAll('.table tbody td:nth-child(2)');
                tableCells[0].innerText = document.getElementById('multiStepsFullName').value;
                tableCells[1].innerText = document.getElementById('multiStepsEmail').value;
                tableCells[2].innerText = document.getElementById('multiStepsMobile').value;
                tableCells[3].innerText = capitalizeFirstLetter(userProfession);
                tableCells[4].innerText = (document.getElementById('multiStepsDesignation').value == '') ? "-" : document.getElementById('multiStepsDesignation').value;
                tableCells[5].innerText = document.getElementById('multiStepsCenter').value;
                stepperInstance.next();
            });

            if (userProfession === 'employee') {
                accountDetailsValidator.disableValidator('multiStepsPass', 'notEmpty');
                accountDetailsValidator.disableValidator('multiStepsConfirmPass', 'notEmpty');
            }
            if (userProfession === 'student') {
                personalInfoValidator.disableValidator('multiStepsDesignation', 'notEmpty');
            }

            btnNextList.forEach((btnNext) => {
                btnNext.addEventListener("click", (event) => {
                    switch (stepperInstance._currentIndex) {
                        case 0:
                            accountDetailsValidator.validate();
                            break;
                        case 1:
                            personalInfoValidator.validate();
                            break;
                        case 2:
                            $('#captcha-modal-display').modal('show');
                            break;
                    }
                });
            }),
                btnPrevList.forEach((btnPrev) => {
                    btnPrev.addEventListener("click", (event) => {
                        switch (stepperInstance._currentIndex) {
                            case 2:
                            case 1:
                                stepperInstance.previous();
                        }
                    });
                });
        }
    });

    $("#captcha-form").submit(function (event) {
        event.preventDefault();
        const captchaValue = $("#captcha").val().trim();

        if (captchaValue.length !== 6) {
            showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
            reloadCaptcha();
            document.getElementsByClassName('btn-submit')[0].setAttribute("disabled", "disabled");
            document.getElementsByClassName('btn-primary')[3].setAttribute("disabled", "disabled");
            return false;
        }

        document.getElementsByClassName('btn-submit')[0].setAttribute("disabled", "disabled");
        document.getElementsByClassName('btn-primary')[3].setAttribute("disabled", "disabled");
        showSpinner();

        // Define a flag to track AJAX request status
        let isAjaxInProgress = false;

        // Your AJAX call
        $.ajax({
            type: "POST",
            url: "/api/new_registration",
            data: {
                captcha: captchaValue,
                userFullName: document.getElementById('multiStepsFullName').value,
                userEmail: document.getElementById('multiStepsEmail').value,
                userPhoneNumber: document.getElementById('multiStepsMobile').value,
                userProfession: userProfession,
                userDesignation: document.getElementById('multiStepsDesignation').value,
                userPassword: document.getElementById('multiStepsPass').value,
                userCenter: document.getElementById('multiStepsCenter').value,
                checkOnly: true
            },
            dataType: 'json',
            beforeSend: function () {
                // Check if an AJAX call is already in progress
                if (isAjaxInProgress) {
                    // If an AJAX call is already in progress, abort this one
                    return false;
                }
                // Set the flag to indicate that an AJAX call is in progress
                isAjaxInProgress = true;
            },
            success: function (response) {
                // Reset the flag on success to allow making another AJAX call
                isAjaxInProgress = false;

                // console.log(response);

                // Handle response
                document.getElementsByClassName('btn-submit')[0].removeAttribute("disabled");
                document.getElementsByClassName('btn-primary')[3].removeAttribute("disabled");
                hideSpinner();
                if (response.status) {
                    if (response.message === 'Employee Successfully Checked') {
                        document.getElementsByClassName('btn-submit')[0].setAttribute("disabled", "disabled");
                        document.getElementsByClassName('btn-primary')[3].setAttribute("disabled", "disabled");
                        showToast(6000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Congratulation', 'Your Account Details Are Verified. You Will Redirect Shortly for LDAP verification.');
                        closeCaptchaModal();
                        setTimeout(function () {
                            location.replace(location.href);
                        }, 6200);
                    } else if (response.message === 'Email Sent') {
                        showToast(6000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Congratulation', 'Your Account Details Are Verified. You Will Receive An Email For Further Steps Shortly.');
                        closeCaptchaModal();
                        setTimeout(function () {
                            deleteAllCookies();
                            location.replace("/login");
                        }, 9000);
                    }
                } else {
                    // Handle response when status is false
                    if (response.error === 'Invalid Captcha') {
                        showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
                        reloadCaptcha();
                    } else if (response.message === 'Email Not Sent') {
                        showToast(18000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Pending Activation', 'An activation link has been sent to your email address. Kindly check your inbox, and don\'t forget to look in your spam folder as well. If you haven\'t received the email, please wait for 15 minutes before trying again. For further assistance, feel free to contact us at iss-kol@cdac.in.');
                        closeCaptchaModal();
                        setTimeout(function () {
                            deleteAllCookies();
                            location.replace(location.href);
                        }, 19000);
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
                document.getElementsByClassName('btn-submit')[0].removeAttribute("disabled");
                document.getElementsByClassName('btn-primary')[3].removeAttribute("disabled");
                hideSpinner();
                // console.error(xhr);
                // console.error(status);
                // console.error(error);
            },
        });

    });

    welcomeModal.show();
});