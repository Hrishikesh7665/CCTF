"use strict";
const addCenterModal = document.getElementById('modal-add-center');
const centerTitle = document.getElementById('centerName');
const modalButton = document.getElementById('addCenter');
const modalTitle = document.getElementById('modal-title');
const customAttribute = document.getElementById('setName');

// Excel Modal for uploading excel file
const excelFileInput = document.getElementById('excelFile');
const excelModal = new bootstrap.Modal(document.getElementById('modal-upload-excel'), {
    keyboard: false
});

function showExcelModal() {
    excelFileInput.value = "";
    excelModal.show();
}

function excelCancel() {
    excelFileInput.value = "";
    excelModal.hide();
}

excelFileInput.addEventListener('change', function () {
    const allowedExtensions = ['xls', 'xlsx', 'csv', 'tsv'];
    const fileName = excelFileInput.value.split('\\').pop();
    const fileExtension = fileName.split('.').pop().toLowerCase();

    if (!allowedExtensions.includes(fileExtension)) {
        showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid File', 'Only Excel (.xls,.xlsx,.csv,.tsv) files are allowed');
        excelFileInput.value = ""; // Clear the input
    }
});

document.getElementById("uploadExcelForm").addEventListener("submit", function (event) {
    // Prevent the default form submission
    event.preventDefault();

    if (excelFileInput.value == "") {  // Fixed the typo here
        showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid File', 'Please select an Excel (.xls,.xlsx,.csv,.tsv) file');
        return;
    } else {
        excelModal.hide();
        $('#captcha-modal-display').modal('show');
    }
});

document.getElementById('captcha-form').addEventListener('submit', function (event) {
    event.preventDefault();
    if (document.getElementById('captcha').value.length != 6) {
        showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
        reloadCaptcha();
        return false;
    }

    // Create and add the captcha input field
    let captchaValue = document.createElement("input");
    captchaValue.type = "hidden"; // Set input type to hidden
    captchaValue.name = "captcha"; // Set input name to identify it when form is submitted
    captchaValue.value = document.getElementById('captcha').value; // Get the captcha value from the element
    
    let extraParam = document.createElement("input");
    extraParam.type = "hidden";
    extraParam.name = "action";
    extraParam.value = "uploadExcel";
    
    const form = document.getElementById('uploadExcelForm');
    form.appendChild(captchaValue);
    form.appendChild(extraParam);
    
    form.submit();
});

const centerModal = new bootstrap.Modal(addCenterModal, {
    keyboard: false
});

function showAddCenterModal() {
    modalButton.innerHTML = "Add New CDAC Center";
    modalTitle.innerHTML = "Add New CDAC Center";
    customAttribute.value = "";
    centerModal.show();
}

function centerCancel() {
    centerTitle.value = "";
    centerModal.hide();
}

document.getElementById("addNewCenter").addEventListener("submit", function (event) {
    // Prevent the default form submission
    event.preventDefault();

    const form = event.target;
    const centerInput = document.getElementById('centerName');
    const centerValue = centerInput.value.trim();

    let msg = '';

    if (centerValue === '') {
        msg = 'Please enter a center name,';
    }

    if (msg != '') {
        showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Incomplete Center Name!!', msg + ' and try again');
        return;
    }

    if (modalButton.innerHTML === "Add New CDAC Center") {
        let extraParam = document.createElement("input");
        extraParam.type = "hidden";
        extraParam.name = "addNewCenter";
        extraParam.value = "addNewCenter";
        form.appendChild(extraParam);
        form.submit();
    } else if (modalButton.innerHTML === "Save Center Name") {
        let extraParam = document.createElement("input");
        let extraParam2 = document.createElement("input");
        extraParam.type = "hidden";
        extraParam.name = "editCenter";
        extraParam.value = "editCenter";
        extraParam2.type = "hidden";
        extraParam2.name = "setName";
        extraParam2.value = customAttribute.value;
        form.appendChild(extraParam);
        form.appendChild(extraParam2);
        form.submit();
    }
});

function editCenter(cid, value) {
    modalButton.innerHTML = "Save Center Name";
    modalTitle.innerHTML = "Update Center Name";
    customAttribute.value = cid;
    centerTitle.value = value;
    centerModal.show();
}

function deleteCenter(cid) {
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
        let centerValue = document.createElement("input");
        centerValue.type = "hidden"; // Set input type to hidden
        centerValue.name = "centerID"; // Set input name to identify it when form is submitted
        centerValue.value = cid; // Set the challenge value
        form.appendChild(centerValue);

        // Append the form to the body and submit it
        document.body.appendChild(form);
        form.submit();
    });
}