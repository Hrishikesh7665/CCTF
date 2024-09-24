"use strict";
const addDesignationModal = document.getElementById('modal-add-designation');
const designationTitle = document.getElementById('designationName');
const modalButton = document.getElementById('addDesignation');
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

const designationModal = new bootstrap.Modal(addDesignationModal, {
    keyboard: false
});

function showAddDesignationModal() {
    modalButton.innerHTML = "Add New Designation";
    modalTitle.innerHTML = "Add New Designation";
    customAttribute.value = "";
    designationModal.show();
}

function designationCancel() {
    designationTitle.value = "";
    designationModal.hide();
}

document.getElementById("addNewDesignation").addEventListener("submit", function (event) {
    // Prevent the default form submission
    event.preventDefault();

    const form = event.target;
    const designationInput = document.getElementById('designationName');
    const designationValue = designationInput.value.trim();

    let msg = '';

    if (designationValue === '') {
        msg = 'Please enter a designation name,';
    }

    if (msg != '') {
        showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Incomplete Designation Name!!', msg + ' and try again');
        return;
    }

    if (modalButton.innerHTML === "Add New Designation") {
        let extraParam = document.createElement("input");
        extraParam.type = "hidden";
        extraParam.name = "addNewDesignation";
        extraParam.value = "addNewDesignation";
        form.appendChild(extraParam);
        form.submit();
    } else if (modalButton.innerHTML === "Save Designation Name") {
        let extraParam = document.createElement("input");
        let extraParam2 = document.createElement("input");
        extraParam.type = "hidden";
        extraParam.name = "editDesignation";
        extraParam.value = "editDesignation";
        extraParam2.type = "hidden";
        extraParam2.name = "setName";
        extraParam2.value = customAttribute.value;
        form.appendChild(extraParam);
        form.appendChild(extraParam2);
        form.submit();
    }
});

function editDesignation(cid, value) {
    modalButton.innerHTML = "Save Designation Name";
    modalTitle.innerHTML = "Update Designation Name";
    customAttribute.value = cid;
    designationTitle.value = value;
    designationModal.show();
}

function deleteDesignation(cid) {
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
        let designationValue = document.createElement("input");
        designationValue.type = "hidden"; // Set input type to hidden
        designationValue.name = "designationID"; // Set input name to identify it when form is submitted
        designationValue.value = cid; // Set the challenge value
        form.appendChild(designationValue);

        // Append the form to the body and submit it
        document.body.appendChild(form);
        form.submit();
    });
}