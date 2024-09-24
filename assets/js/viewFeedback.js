"use strict";
let borderColor, bodyBg, headingColor;
headingColor = (isDarkStyle ? (borderColor = config.colors_dark.borderColor, bodyBg = config.colors_dark.bodyBg, config.colors_dark) : (borderColor = config.colors.borderColor, bodyBg = config.colors.bodyBg, config.colors)).headingColor;

$(function () {
    let dataTable, dataTableElement = $("#PlatformFeedbackTable");
    dataTableElement.length && (dataTable = dataTableElement.DataTable({
        ajax: {
            url: '/api/get_feedbacks',
            type: 'POST',
            data: {
                requestedData: 'platform-feedbacks',
            },
        },
        columns: [{
            data: "reviewer"
        }, {
            data: "review"
        }, {
            data: "date"
        },],
        columnDefs: [{
            targets: 0,
            responsivePriority: 1,
            render: function (data, type, full, meta) {
                var reviewerName = full.reviewer,
                    reviewerEmail = full.email,
                    reviewerAvatar = full.avatar;
                return '<div class="d-flex justify-content-start align-items-center customer-name"><div class="avatar-wrapper me-3"><div class="avatar avatar-sm">' + (reviewerAvatar ? '<img src="' + assetsPath + "img/avatars/" + reviewerAvatar + '" alt="Avatar" class="rounded-circle">' : '<span class="avatar-initial rounded-circle bg-label-' + ["success", "danger", "warning", "info", "dark", "primary", "secondary"][Math.floor(6 * Math.random())] + '">' + (reviewerAvatar = (((reviewerAvatar = (reviewerName = full.reviewer).match(/\b\w/g) || []).shift() || "") + (reviewerAvatar.pop() || "")).toUpperCase()) + "</span>") + '</div></div><div class="d-flex flex-column"><span class="fw-medium">' + reviewerName + '</span><small class="text-nowrap">' + reviewerEmail + "</small></div></div>"
            }
        },
        {
            targets: 1,
            responsivePriority: 2,
            render: function (data, type, full, meta) {
                var reviewRating = full.review,
                    userFeedback = full.feedback,
                    ratingsContainer = $('<div class="read-only-ratings ps-0 mb-2"></div>');
                return ratingsContainer.rateYo({
                    rating: reviewRating,
                    readOnly: !0,
                    starWidth: "20px",
                    spacing: "3px",
                    starSvg: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,2 L15.09,8.09 L22,9.9 L17,14 L18.18,20 L12,17.5 L5.82,20 L7,14 L2,9.9 L8.91,8.09 L12,2 Z" /></svg>'
                }), "<div>" + ratingsContainer.prop("outerHTML") + '<span class="mb-1 text-capitalize fw-medium">' + userFeedback + '</span></div>'
            }
        },
        {
            targets: 2,
            responsivePriority: 3,
            render: function (data, type, full, meta) {
                return '<div><span class="mb-1 text-capitalize fw-medium">' + full.advice + '</span></div>';
            }
        },
        {
            targets: 3,
            render: function (data, type, full, meta) {
                return '<span class="text-nowrap">' + full.time + "</br>" + full.date + "</span>"
            }
        }
        ],
        dom: '<"card-header d-flex align-items-md-center flex-wrap"<"me-5 ms-n2"f><"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-end align-items-md-center justify-content-md-end pt-0 gap-3 flex-wrap"l<"review_filter"> <"mx-0 me-md-n3 mt-sm-0"B>>>t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: {
            sLengthMenu: "_MENU_",
            search: "",
            searchPlaceholder: "Search Review"
        },
        buttons: [{
            extend: "collection",
            className: "btn btn-primary dropdown-toggle me-3 waves-effect waves-light",
            text: '<i class="mdi mdi-export-variant me-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
            buttons: [{
                extend: "print",
                text: '<i class="mdi mdi-printer-outline me-1" ></i>Print',
                className: "dropdown-item",
                exportOptions: {
                    columns: [1, 2, 3, 4],
                    format: {
                        body: function (data, type, row) {
                            var extractedData;
                            return data.length <= 0 ? data : (data = $.parseHTML(data), extractedData = "", $.each(data, function (index, item) {
                                void 0 !== item.classList && item.classList.contains("customer-name") ? extractedData += item.lastChild.firstChild.textContent : void 0 === item.innerText ? extractedData += item.textContent : extractedData += item.innerText
                            }), extractedData)
                        }
                    }
                },
                customize: function (doc) {
                    $(doc.document.body).css("color", headingColor).css("border-color", borderColor).css("background-color", bodyBg), $(doc.document.body).find("table").addClass("compact").css("color", "inherit").css("border-color", "inherit").css("background-color", "inherit")
                }
            },
            {
                extend: "csv",
                text: '<i class="mdi mdi-file-document-outline me-1" ></i>Excel',
                className: "dropdown-item",
                exportOptions: {
                    columns: [1, 2, 3, 4],
                    format: {
                        body: function (data, type, row) {
                            var extractedData;
                            return data.length <= 0 ? data : (data = $.parseHTML(data), extractedData = "", $.each(data, function (index, item) {
                                void 0 !== item.classList && item.classList.contains("customer-name") ? extractedData += item.lastChild.firstChild.textContent : void 0 === item.innerText ? extractedData += item.textContent : extractedData += item.innerText
                            }), extractedData)
                        }
                    }
                }
            }
            ]
        }]
    }))
});

$(function () {
    let dataTable, dataTableElement = $("#QuestionFeedbackTable");
    dataTableElement.length && (dataTable = dataTableElement.DataTable({
        ajax: {
            url: '/api/get_feedbacks',
            type: 'POST',
            data: {
                requestedData: 'questions-feedbacks',
            },
        },
        columns: [{
            data: "challenge"
        }, {
            data: "reviewer"
        }, {
            data: "review"
        }, {
            data: "date"
        },],
        columnDefs: [{
            targets: 0,
            render: function (data, type, full, meta) {
                var challengeName = full.challenge,
                    challengeImage = full.challenge_image;
                return '<div class="d-flex justify-content-start align-items-center customer-name"><div class="avatar-wrapper"><div class="avatar me-3 rounded-2 bg-label-secondary">' + (challengeImage ? '<img src="' + assetsPath + "img/ecommerce-images/" + challengeImage + ' class="rounded-2">' : '<span class="avatar-initial rounded bg-label-' + ["success", "danger", "warning", "info", "dark", "primary", "secondary"][Math.floor(6 * Math.random())] + '">' + (challengeImage = (((challengeImage = (challengeName = full.challenge).match(/\b\w/g) || []).shift() || "") + (challengeImage.pop() || "")).toUpperCase()) + "</span>") + '</div></div><div class="d-flex flex-column"><span class="text-nowrap text-heading fw-medium">' + challengeName + "</span></a></div></div>"
            }
        },
        {
            targets: 1,
            responsivePriority: 1,
            render: function (data, type, full, meta) {
                var reviewerName = full.reviewer,
                    reviewerEmail = full.email,
                    reviewerAvatar = full.avatar;
                return '<div class="d-flex justify-content-start align-items-center customer-name"><div class="avatar-wrapper me-3"><div class="avatar avatar-sm">' + (reviewerAvatar ? '<img src="' + assetsPath + "img/avatars/" + reviewerAvatar + '" alt="Avatar" class="rounded-circle">' : '<span class="avatar-initial rounded-circle bg-label-' + ["success", "danger", "warning", "info", "dark", "primary", "secondary"][Math.floor(6 * Math.random())] + '">' + (reviewerAvatar = (((reviewerAvatar = (reviewerName = full.reviewer).match(/\b\w/g) || []).shift() || "") + (reviewerAvatar.pop() || "")).toUpperCase()) + "</span>") + '</div></div><div class="d-flex flex-column"><span class="fw-medium">' + reviewerName + '</span><small class="text-nowrap">' + reviewerEmail + "</small></div></div>"
            }
        }, {
            targets: 2,
            responsivePriority: 2,
            render: function (data, type, full, meta) {
                var reviewRating = full.review,
                    userFeedback = full.feedback,
                    ratingsContainer = $('<div class="read-only-ratings ps-0 mb-2"></div>');
                return ratingsContainer.rateYo({
                    rating: reviewRating,
                    readOnly: !0,
                    starWidth: "20px",
                    spacing: "3px",
                    starSvg: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,2 L15.09,8.09 L22,9.9 L17,14 L18.18,20 L12,17.5 L5.82,20 L7,14 L2,9.9 L8.91,8.09 L12,2 Z" /></svg>'
                }), "<div>" + ratingsContainer.prop("outerHTML") + '<span class="mb-1 text-capitalize fw-medium">' + userFeedback + '</span></div>'
            }
        }, {
            targets: 3,
            render: function (data, type, full, meta) {
                return '<span class="text-nowrap">' + full.time + "</br>" + full.date + "</span>"
            }
        }
        ],
        dom: '<"card-header d-flex align-items-md-center flex-wrap"<"me-5 ms-n2"f><"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-end align-items-md-center justify-content-md-end pt-0 gap-3 flex-wrap"l<"review_filter"> <"mx-0 me-md-n3 mt-sm-0"B>>>t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: {
            sLengthMenu: "_MENU_",
            search: "",
            searchPlaceholder: "Search Review"
        },
        buttons: [{
            extend: "collection",
            className: "btn btn-primary dropdown-toggle me-3 waves-effect waves-light",
            text: '<i class="mdi mdi-export-variant me-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
            buttons: [{
                extend: "print",
                text: '<i class="mdi mdi-printer-outline me-1" ></i>Print',
                className: "dropdown-item",
                exportOptions: {
                    columns: [1, 2, 3, 4],
                    format: {
                        body: function (data, type, row) {
                            var extractedData;
                            return data.length <= 0 ? data : (data = $.parseHTML(data), extractedData = "", $.each(data, function (index, item) {
                                void 0 !== item.classList && item.classList.contains("customer-name") ? extractedData += item.lastChild.firstChild.textContent : void 0 === item.innerText ? extractedData += item.textContent : extractedData += item.innerText
                            }), extractedData)
                        }
                    }
                },
                customize: function (doc) {
                    $(doc.document.body).css("color", headingColor).css("border-color", borderColor).css("background-color", bodyBg), $(doc.document.body).find("table").addClass("compact").css("color", "inherit").css("border-color", "inherit").css("background-color", "inherit")
                }
            },
            {
                extend: "csv",
                text: '<i class="mdi mdi-file-document-outline me-1" ></i>Excel',
                className: "dropdown-item",
                exportOptions: {
                    columns: [1, 2, 3, 4],
                    format: {
                        body: function (data, type, row) {
                            var extractedData;
                            return data.length <= 0 ? data : (data = $.parseHTML(data), extractedData = "", $.each(data, function (index, item) {
                                void 0 !== item.classList && item.classList.contains("customer-name") ? extractedData += item.lastChild.firstChild.textContent : void 0 === item.innerText ? extractedData += item.textContent : extractedData += item.innerText
                            }), extractedData)
                        }
                    }
                }
            }
            ]
        }]
    }))
});