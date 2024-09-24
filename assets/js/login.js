
// Get form elements students
const studentEmailInput = document.getElementById("student-email");
const studentPasswordInput = document.getElementById("student-password");


// Get form elements employees

const employeeEmailInput = document.getElementById("employee-email");
const employeePasswordInput = document.getElementById("employee-password");


// Tabs
const studentTab = document.getElementById('student-tab');
const employeeTab = document.getElementById('employee-tab');

// Forms
const formStudent = document.getElementById("formStudentAuthentication");
const formEmployee = document.getElementById("formEmployeeAuthentication");

studentEmailInput.setAttribute('autocomplete', 'off');
employeeEmailInput.setAttribute('autocomplete', 'off');


function reloadCaptcha(name) {
    document.getElementById(name).src = 'captcha?' + new Date().getTime();
    if (name === 'student-captchaImage'){
        document.getElementById('student-captcha').value = '';
    }else if (name === 'employee-captchaImage'){
        document.getElementById('employee-captcha').value = '';
    }
}

document.addEventListener("DOMContentLoaded", function () {
    sessionCustomClear('notificationHashes');

    function isValidEmail(email) {
        // You can add a more robust email validation logic if needed
        return /\S+@\S+\.\S+/.test(email);
    }

    function clearForm(form) {
        const inputs = form.querySelectorAll('input');
        inputs.forEach(input => {
            input.value = '';
        });
    }

    function handleSubmitStudent(event) {
        // Prevent the default form submission
        event.preventDefault();

        // Validate email format
        if (!isValidEmail(studentEmailInput.value)) {
            showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Email Address', 'Please enter a valid email address.');
            return;
        }
        let extraParam = document.createElement("input");
        extraParam.type = "hidden";
        extraParam.name = "login";
        extraParam.value = true;
        formStudent.appendChild(extraParam);

        document.getElementsByClassName('btn-primary')[0].setAttribute("disabled", "disabled");
        showSpinner();
        formStudent.submit();
    }

    function handleSubmitEmployee(event) {
        // Prevent the default form submission
        event.preventDefault();

        // Validate email format
        if (!isValidEmail(employeeEmailInput.value)) {
            showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Email Address', 'Please enter a valid email address.');
            return;
        }

        if (employeeEmailInput.value.indexOf("@cdac.in") === -1) {
            showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Email Address', 'LDAP login requires an email from @cdac.in domain.');
            return;
        }
        let extraParam = document.createElement("input");
        extraParam.type = "hidden";
        extraParam.name = "login-ldap";
        extraParam.value = true;
        formEmployee.appendChild(extraParam);

        document.getElementsByClassName('btn-primary')[1].setAttribute("disabled", "disabled");
        showSpinner();
        formEmployee.submit();
    }

    studentTab.addEventListener('click', function() {
        clearForm(formEmployee);
        reloadCaptcha('student-captchaImage');
    });

    employeeTab.addEventListener('click', function() {
        clearForm(formStudent);
        reloadCaptcha('employee-captchaImage');
    });

    formStudent.addEventListener("submit", handleSubmitStudent);
    formEmployee.addEventListener("submit", handleSubmitEmployee);
});