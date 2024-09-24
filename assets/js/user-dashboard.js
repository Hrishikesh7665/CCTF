// Get DOM elements
const challengeIdElement = document.getElementById("challenge-id");
const challengeTitleElement = document.getElementById("challenge-title");
const challengeDescElement = document.getElementById("challenge-desc");
const flagField = document.getElementById("flag");
const cIDField = document.getElementById("cid");
const flagButton = document.getElementById("flag-submit");
const modalElement = document.getElementById('modal-display-challenge');
const user_count = document.getElementById('user_count');
const user_solve = document.getElementById('user_solve');
const user_score = document.getElementById('user_score');

const myModal = new bootstrap.Modal(modalElement, {
    keyboard: false
});

// Function to close modal
function closeModal() {
    flagField.disabled = true;
    flagButton.disabled = true;
    flagButton.innerHTML = 'SUBMIT';
    challengeIdElement.innerHTML = "";
    challengeTitleElement.innerHTML = "";
    challengeDescElement.innerHTML = "";
    flagField.value = "";
    cIDField.value = "";
    myModal.hide();
}

// Function to open question
function openQuestion(questionID) {
    // Send data using POST method
    showSpinner();
    $.ajax({
        type: 'POST',
        url: '/api/get_challenge',
        data: {
            'cid': questionID
        },
        dataType: 'json',
        success: function (response) {
            hideSpinner();
            challengeIdElement.innerHTML = "Challenge #" + questionID;
            cIDField.value = questionID;
            challengeTitleElement.innerHTML = response.title;
            challengeDescElement.innerHTML = response.description;
            if (response.isSolved) {
                flagField.disabled = true;
                flagButton.disabled = true;
                flagButton.innerHTML = 'Solved';
                flagField.value = "";
                cIDField.value = "";
            } else {
                flagField.disabled = false;
                flagButton.disabled = false;
                flagButton.innerHTML = 'SUBMIT';
                flagField.value = "";
            }
            myModal.show();
        },
        error: function (xhr, status, error) {
            hideSpinner();
            alert('Error occurred while fetching the challenge.');
        }
    });
}

// Bind submit event outside of flagSubmit function
$(document).ready(function () {
    $("#captcha-form").submit(function (event) {
        event.preventDefault();
        const captchaValue = $("#captcha").val().trim();

        if (captchaValue.length !== 6) {
            showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
            reloadCaptcha();
            return false;
        }

        const chID = $("#cid").val().trim();
        const flagValue = $("#flag").val().trim();

        if (flagValue.length <= 5 || chID === "") {
            return;
        }
        showSpinner();
        $.ajax({
            type: "POST",
            url: "/api/solve_challenge",
            data: {
                'flag': flagValue,
                'cid': chID,
                'user_id': user_id,
                'captcha': captchaValue
            },
            dataType: 'json',
            success: function (response) {
                hideSpinner();
                switch (response.status) {
                    case 200:
                        showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
                        reloadCaptcha();
                        break;
                    case 201:
                        flagField.value = "";
                        showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Duplicate Flag', 'You have already found this flag.');
                        closeCaptchaModal();
                        closeModal();
                        break;
                    case 203:
                        flagField.value = "";
                        showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Try Again!!', 'Oops!! Wrong Flag');
                        closeCaptchaModal();
                        break;
                    case 202:
                        user_score.innerText = response.message.user_score;
                        user_solve.innerText = response.message.user_solve + ' / ' + response.message.challenges_count;
                        user_count.innerText = response.message.user_rank + ' / ' + response.message.users_count;
                        closeCaptchaModal();
                        closeModal();
                        startAnimation();
                        showToast(5000, 'mdi-party-popper', 'animate__jello', 'text-success', 'Congratulation', 'Awesome!!, You Found the Correct Flag');
                        $('[data-id]').each(function () {
                            const dataId = $(this).data('id');
                            const spanElement = $(this).find('.points');
                            if (dataId == parseInt(chID)) {
                                spanElement.addClass('solved').removeClass('points');
                            }
                        });
                        break;
                }
            },
            error: function (xhr, status, error) {
                hideSpinner();
                // console.log(xhr);
                // console.log(status);
                // console.log(error);
            },
        });
    });
});

function flagSubmit() {
    const chID = $("#cid").val().trim();
    const flagValue = $("#flag").val().trim();

    if (flagValue.length <= 5 || chID === "") {
        return;
    }

    $('#captcha-modal-display').modal('show');
}

$("#flag").on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        e.preventDefault();
        flagSubmit();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Check if the modal should be displayed (first time login)
    if (!sessionStorage.getItem('ctfInstructionsShown')) {
        // Show the modal
        $('#instructions-modal-display').modal('show');

        // Set session storage item to indicate modal has been shown
        sessionStorage.setItem('ctfInstructionsShown', 'true');
    }
});


document.getElementById('intoModal-close').addEventListener('click', function (event) {
    event.preventDefault();
    $('#instructions-modal-display').modal('hide');
});