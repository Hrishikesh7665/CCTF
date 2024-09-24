"use strict";

$(function () {
    // Variables declaration
    let borderColor, bodyBg, headingColor;
    // Setting variables based on style configuration
    if (isDarkStyle) {
        borderColor = config.colors_dark.borderColor;
        bodyBg = config.colors_dark.bodyBg;
        headingColor = config.colors_dark.headingColor;
    } else {
        borderColor = config.colors.borderColor;
        bodyBg = config.colors.bodyBg;
        headingColor = config.colors.headingColor;
    }

    // Selectors
    let dataTableUsers = $(".datatables-users");
    let userViewAccountUrl = "/admin-zone/user-activity";
    let statusLabels = {
        'true': { title: "Active", class: "bg-label-success" },
        'false': { title: "Inactive", class: "bg-label-warning" }
    };

    // DataTable initialization
    if (dataTableUsers.length) {
        let dataTable = dataTableUsers.DataTable({
            ajax: {
                url: '/api/get_user_list',
                type: 'POST'
            },
            // method: 'POST',
            columns: [
                { data: "" },
                { data: "id" },
                { data: "full_name" },
                { data: "email" },
                { data: "profession" },
                { data: "designation" },
                { data: "location" },
                { data: "number" },
                { data: "role" },
                { data: "last_login" },
                { data: "status" },
                { data: "action" }
            ],
            columnDefs: [
                {
                    className: "control",
                    searchable: false,
                    orderable: false,
                    responsivePriority: 2,
                    targets: 0,
                    render: function (data, type, row, meta) {
                        return "";
                    }
                },
                {
                    targets: 1,
                    orderable: false,
                    render: function () {
                        return '<input type="checkbox" class="dt-checkboxes form-check-input">';
                    },
                    checkboxes: { selectAllRender: '<input type="checkbox" class="form-check-input">' },
                    responsivePriority: 4
                },
                {
                    targets: 2,
                    responsivePriority: 4,
                    render: function (data, type, row, meta) {
                        var fullName = row.full_name;
                        var avatar = row.avatar;
                        var userId = row.id;
                        var imageFolder = "/assets/img/avatars/" + avatar;
                        return (
                            '<div class="d-flex justify-content-start align-items-center user-name"><div class="avatar-wrapper"><div class="avatar avatar-sm me-3">' +
                            (avatar != "defaultAvatar.png"
                                ? '<img src="' + imageFolder + '" alt="Avatar" class="rounded-circle" onclick="$.fn.downloadPic(\'' + fullName + '\', this.src)">'
                                : '<img src="' + imageFolder + '" alt="Avatar" class="rounded-circle" style="cursor: default">') +
                            '</div></div><div class="d-flex flex-column"><a href="javascript:void(0)" class="text-heading text-truncate" onclick="$.fn.viewActivity(\'' + userId + '\')"><span class="fw-medium">' +
                            fullName +
                            "</span></a></div></div>"
                        );
                    }
                },
                {
                    targets: 3,
                    render: function (data, type, row, meta) {
                        return "<span>" + row.email + "</span>";
                    }
                },
                {
                    targets: 4,
                    render: function (data, type, row, meta) {
                        return "<span>" + row.profession + "</span>";
                    }
                },
                {
                    targets: 5,
                    render: function (data, type, row, meta) {
                        return "<span>" + row.designation + "</span>";
                    }
                },
                {
                    targets: 6,
                    render: function (data, type, row, meta) {
                        return "<span>" + row.location + "</span>";
                    }
                },
                {
                    targets: 7,
                    render: function (data, type, row, meta) {
                        var role = row.role;
                        return "<span class='text-truncate d-flex align-items-center'>" +
                            {
                                User: '<i class="mdi mdi-account-outline mdi-20px text-primary me-2"></i>',
                                Admin: '<i class="mdi mdi-laptop mdi-20px text-danger me-2"></i>'
                            }[role] + role + "</span>";
                    }
                },
                {
                    targets: 8,
                    render: function (data, type, row, meta) {
                        return "<span>" + row.number + "</span>";
                    }
                },
                {
                    targets: 9,
                    render: function (data, type, row, meta) {
                        var status = row.status;
                        return '<span class="badge rounded-pill ' + statusLabels[status].class + '" text-capitalized>' + statusLabels[status].title + "</span>";
                    }
                },
                {
                    targets: 10,
                    render: function (data, type, row, meta) {
                        return "<span>" + row.last_login + "</span>";
                    }
                },
                {
                    targets: -1,
                    title: "Actions",
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        var userId = row.id;
                        var adminState = row.role; //Admin or User
                        var statusState = row.status; //true or false
                        var isAdminChecked = adminState === 'Admin' ? 'checked' : '';
                        var isActiveChecked = statusState === 'true' ? 'checked' : '';

                        return '<div>' +
                            '<label class="switch switch-info">' +
                            '<input type="checkbox" id="roleSwitch" class="switch-input" ' + isAdminChecked + ' onclick="$.fn.changeRole(\'' + userId + '\', this.checked)" />' +
                            '<span class="switch-toggle-slider">' +
                            '<span class="switch-on"></span>' +
                            '<span class="switch-off"></span>' +
                            '</span>' +
                            '<span class="switch-label">isAdmin</span>' +
                            '</label>' +
                            '<label class="switch switch-info">' +
                            '<input type="checkbox" id="stateSwitch" class="switch-input" ' + isActiveChecked + ' onclick="$.fn.changeState(\'' + userId + '\', this.checked)" />' +
                            '<span class="switch-toggle-slider">' +
                            '<span class="switch-on"></span>' +
                            '<span class="switch-off"></span>' +
                            '</span>' +
                            '<span class="switch-label">isActive</span>' +
                            '</label>' +
                            '</div>';
                    }
                }
            ],
            // order: [[2, "asc"]],
            dom: '<"row mx-1"<"col-md-2 d-flex align-items-center justify-content-md-start justify-content-center"<"dt-action-buttons"B>>' +
                '<"col-md-10"<"d-flex align-items-center justify-content-md-end justify-content-center"<"me-3"f><"add-new">>>>t' +
                '<"row mx-1"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: { sLengthMenu: "Show _MENU_", search: "", searchPlaceholder: "Search.." },
            buttons: [
                {
                    extend: "collection",
                    className: "btn btn-label-secondary dropdown-toggle waves-effect waves-light",
                    text: '<i class="mdi mdi-export-variant me-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
                    buttons: [
                        // { extend: "print", text: '<i class="mdi mdi-printer-outline me-1"></i>Print', className: "dropdown-item", exportOptions: { columns: [2, 3, 4, 5, 6, 8, 9, 10] }, filename: "CTF Users Details"  },
                        { extend: "csv", text: '<i class="mdi mdi-file-document-outline me-1"></i>Csv', className: "dropdown-item", exportOptions: { columns: [2, 3, 4, 5, 6, 8, 9, 10] }, filename: "CTF Users Details"  },
                        { extend: "pdf", text: '<i class="mdi mdi-file-pdf-box me-1"></i>Pdf', className: "dropdown-item", exportOptions: { columns: [2, 3, 4, 5, 6, 8, 9, 10] }, filename: "CTF Users Details"  },
                        { extend: "copy", text: '<i class="mdi mdi-content-copy me-1"></i>Copy', className: "dropdown-item", exportOptions: { columns: [2, 3, 4, 5, 6, 8, 9, 10] }, filename: "CTF Users Details" }
                    ]
                }
            ],
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            return "Details of " + row.data().full_name;
                        }
                    }),
                    type: "column",
                    renderer: function (api, rowIdx, columns) {
                        return $('<table class="table"/><tbody />').append(
                            $.map(columns, function (col, i) {
                                return col.title !== "" ? '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '"><td>' + col.title + ":</td><td>" + col.data + "</td></tr>" : "";
                            })
                        );
                    }
                }
            },
            initComplete: function () {

                let userProfessionSelect = $('<select id="userProfession" class="form-select text-capitalize"><option value=""> Select Profession </option></select>')
                    .appendTo(".user_profession")
                    .on("change", function () {
                        let profession = $.fn.dataTable.util.escapeRegex($(this).val());
                        dataTable.column(4)
                            .search(profession ? "^" + profession + "$" : "", true, false)
                            .draw();
                    });

                let userDesignationSelect = $('<select id="userDesignation" class="form-select text-capitalize"><option value=""> Select Designation </option></select>')
                    .appendTo(".user_designation")
                    .on("change", function () {
                        let designation = $.fn.dataTable.util.escapeRegex($(this).val());
                        dataTable.column(5)
                            .search(designation ? "^" + designation + "$" : "", true, false)
                            .draw();
                    });

                let userLocationSelect = $('<select id="userLocation" class="form-select text-capitalize"><option value=""> Select Location </option></select>')
                    .appendTo(".user_location")
                    .on("change", function () {
                        let location = $.fn.dataTable.util.escapeRegex($(this).val());
                        dataTable.column(6)
                            .search(location ? "^" + location + "$" : "", true, false)
                            .draw();
                    });

                let userRoleSelect = $('<select id="userRole" class="form-select text-capitalize"><option value=""> Select Role </option></select>')
                    .appendTo(".user_role")
                    .on("change", function () {
                        let role = $.fn.dataTable.util.escapeRegex($(this).val());
                        dataTable.column(7)
                            .search(role ? "^" + role + "$" : "", true, false)
                            .draw();
                    });

                let userStatusSelect = $('<select id="userStatus" class="form-select text-capitalize"><option value=""> Select Account State </option></select>')
                    .appendTo(".user_status")
                    .on("change", function () {
                        let status = $.fn.dataTable.util.escapeRegex($(this).val());
                        dataTable.column(9)
                            .search(status ? "^" + status + "$" : "", true, false)
                            .draw();
                    });

                dataTable.column(4).data().unique().sort().each(function (value) {
                    userProfessionSelect.append('<option value="' + value + '" class="text-capitalize">' + value + "</option>");
                });

                dataTable.column(5).data().unique().sort().each(function (value) {
                    userDesignationSelect.append('<option value="' + value + '" class="text-capitalize">' + value + "</option>");
                });

                dataTable.column(6).data().unique().sort().each(function (value) {
                    userLocationSelect.append('<option value="' + value + '" class="text-capitalize">' + value + "</option>");
                });

                dataTable.column(8).data().unique().sort().each(function (value) {
                    userRoleSelect.append('<option value="' + value + '" class="text-capitalize">' + value + "</option>");
                });

                dataTable.column(10).data().unique().sort().each(function (value) {
                    if (value == 'true') {
                        value = 'active';
                    } else if (value == 'false') {
                        value = 'inactive';
                    }
                    userStatusSelect.append('<option value="' + value + '" class="text-capitalize">' + value + "</option>");
                });

            }
        });

        // Delete record event
        $(".datatables-users tbody").on("click", ".delete-record", function () {
            dataTable.row($(this).parents("tr")).remove().draw();
        });

        // Timeout for adjusting UI after DataTable initialization
        setTimeout(() => {
            $(".dataTables_filter .form-control").removeClass("form-control-sm");
            $(".dataTables_length .form-select").removeClass("form-select-sm");
        }, 300);

        $.fn.viewActivity = function (val) {
            let form = document.createElement("form");
            form.method = "post";
            form.action = userViewAccountUrl;
            // form.target = "_blank";

            let userIdValue = document.createElement("input");
            userIdValue.type = "hidden";
            userIdValue.name = "uid";
            userIdValue.value = val;
            form.appendChild(userIdValue);

            document.body.appendChild(form);
            form.submit();
        };

        $.fn.downloadPic = function (val, img) {
            var link = document.createElement('a');
            link.href = img;
            link.download = val;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };

        $.fn.changeRole = function (val, state) {
            showSpinner();
            $.ajax({
                url: '/api/update_user',
                method: 'POST',
                dataType: 'json',
                data: { userID: val, value: state, target: "role" },
                success: function (response) {
                    hideSpinner();
                    if (response.status === 200) {
                        showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Operation Successful', 'User role has been updated successfully');
                    } else {
                        showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Failed!!', 'Failed to update user role. Please try again');
                    }
                },
                error: function (xhr, status, error) {
                    hideSpinner();
                    showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Error!!', 'Unexpected error occurred. Please try again');
                }
            });
            dataTable.ajax.reload();
        };

        $.fn.changeState = function (val, state) {
            showSpinner();
            $.ajax({
                url: '/api/update_user',
                method: 'POST',
                dataType: 'json',
                data: { userID: val, value: state, target: "state" },
                success: function (response) {
                    hideSpinner();
                    if (response.status === 200) {
                        showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Operation Successful', 'User account status has been updated successfully');
                    } else {
                        showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Failed!!', 'Failed to update user account status. Please try again');
                    }
                },
                error: function (xhr, status, error) {
                    hideSpinner();
                    showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Error!!', 'Unexpected error occurred. Please try again');
                    // console.error('Error:', error);
                }
            });
            dataTable.ajax.reload();
        };

    }
});
