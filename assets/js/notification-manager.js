const demoInput = document.getElementById('demo');
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('notificationForm');
            const quill = new Quill("#full-editor", {
                bounds: "#full-editor",
                placeholder: "Type notification description......",
                modules: {
                    formula: true,
                    toolbar: [
                        [{
                            font: []
                        }, {
                            size: []
                        }],
                        ["bold", "italic", "underline", "strike"],
                        [{
                            color: []
                        }, {
                            background: []
                        }],
                        [{
                            script: "super"
                        }, {
                            script: "sub"
                        }],
                        [{
                            header: "1"
                        }, {
                            header: "2"
                        }, "blockquote", "code-block"],
                        [{
                            list: "ordered"
                        }, {
                            list: "bullet"
                        }, {
                            indent: "-1"
                        }, {
                            indent: "+1"
                        }],
                        [{
                            direction: "rtl"
                        }],
                        ["link", "formula", "image"], // Removed "image" and "video" options
                        ["clean"]
                    ]
                },
                theme: "snow"
            });

            const selectRole = $('#multicol-role');
            if (selectRole.length) {
                selectRole.wrap('<div class="position-relative"></div>').select2({
                    placeholder: "Select Intended Group (Role)",
                    dropdownParent: selectRole.parent(),
                    multiple: true
                });
            }

            $("#selectState").select2({
                placeholder: "Select Notification State",
                dropdownParent: $("#selectState").parent(),
                minimumResultsForSearch: Infinity
            });

            const activeTime = document.getElementById('act-dt');
            flatpickr(activeTime, {
                enableTime: true,
                monthSelectorType: "static",
                dateFormat: "j-m-Y h:i K",
                minDate: "today",
                time_24hr: false
            });

            const expireTime = document.getElementById('exp-dt');
            flatpickr(expireTime, {
                enableTime: true,
                monthSelectorType: "static",
                dateFormat: "j-m-Y h:i K",
                minDate: "today",
                time_24hr: false
            });

            // Function to reset the form
            function resetForm() {
                // Reset the form fields
                form.reset();

                // Clear the Quill editor content
                quill.setContents([]); // Clear editor content

                // Reset the select2 elements
                $('#multicol-role').val('').trigger('change'); // Clear multiple select2
                $('#selectState').val('').trigger('change'); // Clear single select2
            }

            $('#selectState').val('').trigger('change');

            if (selected_state != null) {
                $('#selectState').val(selected_state).trigger('change');
            }

            let quillContent = null;

            quill.on('text-change', function() {
                quillContent = document.getElementsByClassName("ql-editor")[0].innerHTML;
                demoInput.value = quillContent.replace(/(<([^>]+)>)/gi, "").replace(/[^\w\s]/gi, "").trim();
            });

            const formValidationInstance = FormValidation.formValidation(
                form, {
                    fields: {
                        notificationTitle: {
                            validators: {
                                notEmpty: {
                                    message: 'The notification title is required'
                                }
                            }
                        },
                        'act-dt': {
                            validators: {
                                notEmpty: {
                                    message: 'Active time is required'
                                },
                                date: {
                                    format: 'DD-MM-YYYY h:mm A',
                                    message: 'The value is not a valid date'
                                }
                            }
                        },
                        'exp-dt': {
                            validators: {
                                notEmpty: {
                                    message: 'Expire time is required'
                                },
                                date: {
                                    format: 'DD-MM-YYYY h:mm A',
                                    message: 'The value is not a valid date'
                                }
                            }
                        },
                        'multicol-role': {
                            validators: {
                                notEmpty: {
                                    message: 'Please select at least one role'
                                }
                            }
                        },
                        selectState: {
                            validators: {
                                notEmpty: {
                                    message: 'Please select a notification state'
                                }
                            }
                        },
                        demo: {
                            validators: {
                                notEmpty: {
                                    message: 'The notification description is required'
                                }
                            }
                        }
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5({
                            rowSelector: '.form-floating',
                            eleValidClass: ''
                        }),
                        submitButton: new FormValidation.plugins.SubmitButton(),
                        autoFocus: new FormValidation.plugins.AutoFocus(),
                        defaultSubmit: new FormValidation.plugins.DefaultSubmit()
                    }
                }
            );

            formValidationInstance.on('core.form.valid', function() {
                showSpinner();

                var form = document.querySelector('form');
                var formData = new FormData(form);
                formData.delete('demo');

                var hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'quillContent';
                hiddenInput.value = document.getElementsByClassName("ql-editor")[0].innerHTML;

                form.appendChild(hiddenInput);

                form.submit();
            });
        });