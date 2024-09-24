"use strict";

const forms = document.querySelectorAll('form[id^="updateChallenge"]');
const editorElements = document.querySelectorAll(".editor");
const cardContainers = document.querySelectorAll('.cardContainer');
const addChallengeModal = document.getElementById('modal-add-challenge');
const challengeModal = new bootstrap.Modal(addChallengeModal, {
    keyboard: false
});
const challengeDes = `#new-challenge-snow-editor`;

document.addEventListener("DOMContentLoaded", function () {

    editorElements.forEach((editorElement, index) => {
        let editorId = editorElement.querySelector("[id^='snow-editor']").id;
        let toolbarId = editorElement.querySelector("[id^='snow-toolbar']").id;
        new Quill("#" + editorId, {
            modules: {
                formula: true,
                toolbar: "#" + toolbarId
            },
            theme: "snow"
        });
    });

    new Quill(challengeDes, {
        bounds: challengeDes,
        modules: {
            formula: true,
            toolbar: `#new-challenge-snow-toolbar`
        },
        theme: "snow"
    });

    // Loop through each cardContainer and add a click event listener
    cardContainers.forEach(cardContainer => {
        cardContainer.addEventListener('click', () => {
            // Get the parent div's custom attribute value
            const categoryId = cardContainer.parentElement.getAttribute('data-categoryID');
            document.getElementById("challengeCategory").value = categoryId;
            challengeModal.show();
        });
    });

    document.getElementById("addNewChallenge").addEventListener("submit", function (event) {
        // Prevent the default form submission
        event.preventDefault();

        const form = event.target;
        // Fetch form inputs
        const challengeCategoryValue = document.getElementById("challengeCategory").value;
        const titleInput = document.getElementById('challengeTitle');
        const scoreInput = document.getElementById('challengeScore');
        const descriptionInput = document.getElementById('new-challenge-snow-editor');
        const flagInput = document.getElementById('challengeFlag');
        const titleValue = titleInput.value.trim();
        const scoreValue = scoreInput.value.trim();
        const flagValue = flagInput.value.trim();

        // Get the Quill editor instance
        const quill = new Quill(descriptionInput);

        // Get the Delta object representing the contents of the editor
        const delta = quill.getContents();

        // Convert Delta object to HTML
        const tempCont = document.createElement("div");
        (new Quill(tempCont)).setContents(delta);
        const descriptionHTML = tempCont.getElementsByClassName("ql-editor")[0].innerHTML;

        let msg = '';

        if (challengeCategoryValue == '') {
            msg = 'Please select challenge category,';
        }

        if (titleValue === '') {
            msg = 'Please enter a challenge name,';
        }

        if (scoreValue === '') {
            msg = 'Please enter a score for solving the challenge,';
        }

        if (descriptionHTML.replace(/(<([^>]+)>)/gi, "").replace(/[^\w\s]/gi, "").trim() === '') {
            msg = 'Please enter the challenge description,';
        }

        if (flagValue === '') {
            msg = 'Please enter the correct flag,';
        }

        if (msg != '') {
            showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Incomplete Challenge!!', msg + ' and try again');
            return;
        }

        let extraParam = document.createElement("input");
        let extraParam2 = document.createElement("input");
        extraParam.type = "hidden";
        extraParam.name = "description";
        extraParam.value = descriptionHTML;
        extraParam2.type = "hidden";
        extraParam2.name = "addNewChallenge";
        extraParam2.value = 'addNewChallenge';
        form.appendChild(extraParam);
        form.appendChild(extraParam2);
        form.submit();
    });

    forms.forEach(function (form) {
        form.addEventListener("submit", function (event) {
            event.preventDefault();

            const challengeId = form.getAttribute('id').replace('updateChallenge', '');
            const titleInput = document.getElementById('questions' + challengeId);
            const scoreInput = document.getElementById('score' + challengeId);
            const descriptionInput = document.getElementById('snow-editor' + challengeId);
            const flagInput = document.getElementById('flag' + challengeId);
            const categoryValue = document.getElementById('category' + challengeId).value.trim();
            const titleValue = titleInput.value.trim();
            const scoreValue = scoreInput.value.trim();
            const flagValue = flagInput.value.trim();

            // Get the Quill editor instance
            const quill = new Quill(descriptionInput);

            // Get the Delta object representing the contents of the editor
            const delta = quill.getContents();

            // Convert Delta object to HTML
            const tempCont = document.createElement("div");
            (new Quill(tempCont)).setContents(delta);
            const descriptionHTML = tempCont.getElementsByClassName("ql-editor")[0].innerHTML;

            let msg = '';
            if (titleValue === '') {
                msg = 'Please enter a challenge name,';
            }

            if (scoreValue === '') {
                msg = 'Please enter a score for solving the challenge,';
            }

            if (categoryValue == '') {
                msg = 'Please select a category,';
            }

            if (descriptionHTML.replace(/(<([^>]+)>)/gi, "").replace(/[^\w\s]/gi, "").trim() === '') {
                msg = 'Please enter the challenge description,';
            }

            if (flagValue === '') {
                msg = 'Please enter the correct flag,';
            }

            if (msg != '') {
                showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Incomplete Challenge!!', msg + ' and try again');
                return;
            }
            let extraParam = document.createElement("input");
            let extraParam2 = document.createElement("input");
            extraParam.type = "hidden";
            extraParam.name = "description";
            extraParam.value = descriptionHTML;
            extraParam2.type = "hidden";
            extraParam2.name = "updateChallenge";
            extraParam2.value = "updateChallenge";
            form.appendChild(extraParam);
            form.appendChild(extraParam2);
            form.submit();
        });
    });

});

function deleteChallenge(cid) {
    $('#captcha-modal-display').modal('show');
    document.getElementById('captcha-form').addEventListener('submit', function (event) {
        event.preventDefault();
        if (document.getElementById('captcha').value.length != 6) {
            showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
            reloadCaptcha();
            return false;
        }

        // Create the form element
        let form = document.createElement("form");
        form.method = "post"; // Use direct assignment for method and action
        form.action = window.location.href;

        // Create and add the captcha input field
        let captchaValue = document.createElement("input");
        captchaValue.type = "hidden"; // Set input type to hidden
        captchaValue.name = "captcha"; // Set input name to identify it when form is submitted
        captchaValue.value = document.getElementById('captcha').value; // Get the captcha value from the element
        form.appendChild(captchaValue);

        // Create and add the challenge input field
        let challengeValue = document.createElement("input");
        challengeValue.type = "hidden"; // Set input type to hidden
        challengeValue.name = "challenge"; // Set input name to identify it when form is submitted
        challengeValue.value = cid; // Set the challenge value
        form.appendChild(challengeValue);

        // Append the form to the body and submit it
        document.body.appendChild(form);
        form.submit();


    });
}

function challengeCancel() {
    document.getElementById("challengeCategory").value = "";
    document.getElementById('challengeTitle').value = "";
    document.getElementById('challengeScore').value = "";
    document.getElementById('challengeFlag').value = "";
    const quill = new Quill('#new-challenge-snow-editor');
    quill.setText('');
    challengeModal.hide();
}

console.clear();