"use strict";
document.addEventListener("DOMContentLoaded", function (e) {
    if (document.getElementById('uploadedAvatar').src.split('/').pop() == 'defaultAvatar.png') {
        document.getElementById('button-reset').disabled = true;
    }
    $(function () {
        var e = $(".select2");
        e.length &&
            e.each(function () {
                var e = $(this);
                select2Focus(e), e.wrap('<div class="position-relative"></div>'), e.select2({ dropdownParent: e.parent() });
            });
    });
})

function handleFileChange(input) {
    const file = input.files[0];
    const imageTypeRegex = /^image\/(jpg|jpeg|png)$/;

    if (file && imageTypeRegex.test(file.type)) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        if (file.size <= maxSize) {
            const formData = new FormData();
            formData.append('avatar', file);
            showSpinner();
            // Make AJAX call
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/api/avatar_manager', true);

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        hideSpinner();
                        // console.log(xhr.responseText);
                        const response = JSON.parse(xhr.responseText);
                        // Handle the response from the server if needed
                        if (response.status === true) {
                            document.querySelectorAll('.userAvatar').forEach(avatar => avatar.src = response.message);
                            document.getElementById('button-reset').disabled = false;
                            showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Success Message', 'Your image has been uploaded successfully.');
                        } else if (response.message === 'Invalid file type.') {
                            showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Image File', 'Please select a JPG, JPEG, or PNG file.');
                        } else if (response.message === 'Error uploading file.') {
                            showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Upload Error', 'There was an error updating your profile picture. Please try again.');
                        } else if (response.message === 'Error updating database.') {
                            showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Upload Error', 'There was an error updating your profile picture. Please try again.');
                        }

                    } else {
                        // Handle the error
                        // console.error('Error uploading file:', xhr.status);
                        hideSpinner();
                    }
                }
            };

            xhr.send(formData);
        } else {
            showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Image File', 'File size exceeds the maximum limit (5MB).');
        }
    } else {
        showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Image File', 'Please select a JPG, JPEG, or PNG file.');
    }
}


function resetImage() {
    if (document.getElementById('uploadedAvatar').src.split('/').pop() == 'defaultAvatar.png') {
        document.getElementById('button-reset').disabled = true;
        return false;
    }

    $('#captcha-modal-display').modal('show');
    document.getElementById('captcha-form').addEventListener('submit', function (event) {
        event.preventDefault();
        if (document.getElementById('captcha').value.length != 6) {
            showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
            reloadCaptcha();
            return false;
        }

        // Prepare data to send
        const formData = new FormData();
        formData.append('resetimage', 'true');
        formData.append('captcha', document.getElementById('captcha').value);
        showSpinner();
        // Make AJAX call to reset the image
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/api/avatar_manager', true);
        // console.log('test');

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                // console.log(xhr.responseText);
                if (xhr.status === 200) {
                    hideSpinner();
                    const response = JSON.parse(xhr.responseText);
                    // Handle the response from the server if needed
                    if (response.status === true) {
                        document.querySelectorAll('.userAvatar').forEach(avatar => avatar.src = response.message);
                        showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Success Message', 'Your profile picture has been reset successfully.');
                        document.getElementById('button-reset').disabled = true;
                        closeCaptchaModal();
                    } else if (response.message == 'Captcha not valid.') {
                        showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
                        reloadCaptcha();
                    } else {
                        // console.log(response);
                        showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Reset Error', 'There was an error resetting your profile picture. Please try again.');
                        reloadCaptcha();
                    }
                }
                else {
                    hideSpinner();
                    // console.error('Error resetting image:', xhr.status);
                }
            }
        };

        // Send the form data
        xhr.send(formData);

        // Reset the input file element
        const uploadInput = document.getElementById('upload');
        uploadInput.value = null;

        document.body.focus();
    });
}


function validateForm() {
    let userName = document.getElementById('userName').value.trim();
    let phoneNumber = document.getElementById('phoneNumber').value.trim();
    let profession = document.getElementById('profession').value;
    let designation = document.getElementById('designation').value;
    let location = document.getElementById('location').value;

    // Validate User Name
    if (userName === '') {
        showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Username', 'Please enter a valid username.');
        return false;
    }

    if (userName.length < 4 || userName.length > 40) {
        showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Username', 'The name must be more than 4 and less than 40 characters long.');
        return false;
    }

    if (!/^[a-zA-Z. ]{4,}$/.test(userName)) {
        showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Username', 'The name can only consist of alphabets, dots, and spaces.');
        return false;
    }

    // Validate Phone Number
    if (phoneNumber === '' || isNaN(phoneNumber) || phoneNumber.length !== 10) {
        showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Phone Number', 'Please enter a valid 10-digit Phone Number.');
        return false;
    }

    // Validate Profession
    if (profession === '') {
        showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Profession Selection', 'Please select a Profession.');
        return false;
    }

    // Validate Designation
    if (profession === 'employee' && designation === '') {
        showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Designation Selection', 'Please select a Designation.');
        return false;
    }

    // Validate Location
    if (location === '') {
        showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid CDAC Center Selection', 'Please select a CDAC Center.');
        return false;
    }

    return true;
}

document.getElementById('formAccountSettings').addEventListener('submit', function (event) {
    event.preventDefault();
    if (validateForm()) {
        // Create a hidden input element
        let hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'updateDetails'; // Name of the parameter
        hiddenInput.value = true; // Value of the parameter

        // Append the hidden input to the form
        document.getElementById('formAccountSettings').appendChild(hiddenInput);

        // Show captcha modal
        $('#captcha-modal-display').modal('show');
        // Set up captcha form submission on modal submit
        document.getElementById('captcha-form').addEventListener('submit', function (event) {
            event.preventDefault();
            if (document.getElementById('captcha').value.length == 6) {
                submitFormWithCaptcha('formAccountSettings');
            } else {
                showToast(5500, 'mdi-alert', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
                reloadCaptcha();
            }
        });
    }
});


document.getElementById("email").setAttribute('autocomplete', 'off');
document.getElementById("userName").setAttribute('autocomplete', 'off');
document.getElementById("phoneNumber").setAttribute('autocomplete', 'off');

function toggleDesignationDropdown() {
    let professionDropdown = $("#profession");

    let designationDropdown = $("#designation");

    if (professionDropdown.val() === "student") {
        designationDropdown.val(null).trigger('change');
        designationDropdown.prop('disabled', true).select2({
            disabled: true
        });
    } else {
        designationDropdown.prop('disabled', false).select2({
            disabled: false
        });
    }
}