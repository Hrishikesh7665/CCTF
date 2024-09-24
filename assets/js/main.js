"use strict";
const endpoint = window.location.pathname;
const pageName = endpoint.split('/').pop();

function formatDateTime(mysqlDatetime) {
    // Create a Date object from the MySQL DATETIME string
    const date = new Date(mysqlDatetime.replace(' ', 'T') + 'Z'); // Adding 'T' and 'Z' to parse correctly

    // Define arrays for day names and month names
    const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    // Get the components of the date
    const hours = date.getHours();
    const minutes = date.getMinutes();
    const dayName = dayNames[date.getDay()];
    const monthName = monthNames[date.getMonth()];
    const day = date.getDate();
    const year = date.getFullYear();

    // Convert hours to 12-hour format and determine AM/PM
    const hour12 = hours % 12 || 12;
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const minuteStr = minutes < 10 ? '0' + minutes : minutes;

    // Construct the formatted string
    return `${hour12}:${minuteStr} ${ampm} ${dayName} ${monthName} ${day}, ${year}`;
}

function sessionCustomClear(key) {
    if (sessionStorage.getItem(key) !== null) {
        sessionStorage.removeItem(key);
    }
}

let isRtl = window.Helpers.isRtl(),
    isDarkStyle = window.Helpers.isDarkStyle(),
    menu,
    animate,
    isHorizontalLayout = !1;
document.getElementById("layout-menu") && (isHorizontalLayout = document.getElementById("layout-menu").classList.contains("menu-horizontal")),
    (function () {
        function e() {
            var e = document.querySelector(".layout-page");
            e && (0 < window.pageYOffset ? e.classList.add("window-scrolled") : e.classList.remove("window-scrolled"));
        }
        "undefined" != typeof Waves &&
            (Waves.init(),
                Waves.attach(".btn[class*='btn-']:not(.position-relative):not([class*='btn-outline-']):not([class*='btn-label-'])", ["waves-light"]),
                Waves.attach("[class*='btn-outline-']:not(.position-relative)"),
                Waves.attach("[class*='btn-label-']:not(.position-relative)"),
                Waves.attach(".pagination .page-item .page-link"),
                Waves.attach(".dropdown-menu .dropdown-item"),
                Waves.attach(".light-style .list-group .list-group-item-action"),
                Waves.attach(".dark-style .list-group .list-group-item-action", ["waves-light"]),
                Waves.attach(".nav-tabs:not(.nav-tabs-widget) .nav-item .nav-link"),
                Waves.attach(".nav-pills .nav-item .nav-link", ["waves-light"]),
                Waves.attach(".menu-vertical .menu-item .menu-link.menu-toggle")),
            setTimeout(() => {
                e();
            }, 200),
            (window.onscroll = function () {
                e();
            }),
            setTimeout(function () {
                window.Helpers.initCustomOptionCheck();
            }, 1e3),
            document.querySelectorAll("#layout-menu").forEach(function (e) {
                (menu = new Menu(e, {
                    orientation: isHorizontalLayout ? "horizontal" : "vertical",
                    closeChildren: !!isHorizontalLayout,
                    showDropdownOnHover: localStorage.getItem("templateCustomizer-" + templateName + "--ShowDropdownOnHover")
                        ? "true" === localStorage.getItem("templateCustomizer-" + templateName + "--ShowDropdownOnHover")
                        : void 0 === window.templateCustomizer || window.templateCustomizer.settings.defaultShowDropdownOnHover,
                })),
                    window.Helpers.scrollToActive((animate = !1)),
                    (window.Helpers.mainMenu = menu);
            }),
            document.querySelectorAll(".layout-menu-toggle").forEach((e) => {
                e.addEventListener("click", (e) => {
                    if ((e.preventDefault(), window.Helpers.toggleCollapsed(), config.enableMenuLocalStorage && !window.Helpers.isSmallScreen()))
                        try {
                            localStorage.setItem("templateCustomizer-" + templateName + "--LayoutCollapsed", String(window.Helpers.isCollapsed()));
                            var t,
                                a = document.querySelector(".mainConfiguration-layouts-options");
                            a && ((t = window.Helpers.isCollapsed() ? "collapsed" : "expanded"), a.querySelector(`input[value="${t}"]`).click());
                        } catch (e) { }
                });
            }),
            window.Helpers.swipeIn(".drag-target", function (e) {
                window.Helpers.setCollapsed(!1);
            }),
            window.Helpers.swipeOut("#layout-menu", function (e) {
                window.Helpers.isSmallScreen() && window.Helpers.setCollapsed(!0);
            });
        let t = document.getElementsByClassName("menu-inner"),
            a = document.getElementsByClassName("menu-inner-shadow")[0];
        0 < t.length &&
            a &&
            t[0].addEventListener("ps-scroll-y", function () {
                this.querySelector(".ps__thumb-y").offsetTop ? (a.style.display = "block") : (a.style.display = "none");
            });
        var n,
            o = document.querySelector(".dropdown-style-switcher"),
            s = localStorage.getItem("templateCustomizer-" + templateName + "--Style") || (window.templateCustomizer?.settings?.defaultStyle ?? "light"),
            o =
                (window.templateCustomizer &&
                    o &&
                    ([].slice.call(o.children[1].querySelectorAll(".dropdown-item")).forEach(function (e) {
                        e.addEventListener("click", function () {
                            var e = this.getAttribute("data-theme");
                            "light" === e ? window.templateCustomizer.setStyle("light") : "dark" === e ? window.templateCustomizer.setStyle("dark") : window.templateCustomizer.setStyle("system");
                        });
                    }),
                        (o = o.querySelector("i")),
                        "light" === s
                            ? (o.classList.add("mdi-weather-sunny"), new bootstrap.Tooltip(o, { title: "Light Mode", fallbackPlacements: ["bottom"] }))
                            : "dark" === s
                                ? (o.classList.add("mdi-weather-night"), new bootstrap.Tooltip(o, { title: "Dark Mode", fallbackPlacements: ["bottom"] }))
                                : (o.classList.add("mdi-monitor"), new bootstrap.Tooltip(o, { title: "System Mode", fallbackPlacements: ["bottom"] }))),
                    "system" === (n = s) && (n = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"),
                    [].slice.call(document.querySelectorAll("[data-app-" + n + "-img]")).map(function (e) {
                        var t = e.getAttribute("data-app-" + n + "-img");
                        e.src = assetsPath + "img/" + t;
                    }),
                    "undefined" != typeof i18next &&
                    "undefined" != typeof i18NextHttpBackend &&
                    i18next
                        .use(i18NextHttpBackend)
                        .init({ lng: window.templateCustomizer ? window.templateCustomizer.settings.lang : "en", debug: !1, fallbackLng: "en", backend: { loadPath: assetsPath + "json/locales/{{lng}}.json" }, returnObjects: !0 })
                        .then(function (e) {
                            l();
                        }),
                    document.getElementsByClassName("dropdown-language"));
        if (o.length) {
            var i = o[0].querySelectorAll(".dropdown-item");
            for (let e = 0; e < i.length; e++)
                i[e].addEventListener("click", function () {
                    let a = this.getAttribute("data-language"),
                        n = this.getAttribute("data-text-direction");
                    for (var e of this.parentNode.children)
                        for (var t = e.parentElement.parentNode.firstChild; t;) 1 === t.nodeType && t !== t.parentElement && t.querySelector(".dropdown-item").classList.remove("active"), (t = t.nextSibling);
                    this.classList.add("active"),
                        i18next.changeLanguage(a, (e, t) => {
                            if (
                                (window.templateCustomizer && window.templateCustomizer.setLang(a),
                                    "rtl" === n
                                        ? "true" !== localStorage.getItem("templateCustomizer-" + templateName + "--Rtl") && window.templateCustomizer && window.templateCustomizer.setRtl(!0)
                                        : "true" === localStorage.getItem("templateCustomizer-" + templateName + "--Rtl") && window.templateCustomizer && window.templateCustomizer.setRtl(!1),
                                    e)
                            )
                                return console.log("something went wrong loading", e);
                            l();
                        });
                });
        }
        function l() {
            var e = document.querySelectorAll("[data-i18n]"),
                t = document.querySelector('.dropdown-item[data-language="' + i18next.language + '"]');
            t && t.click(),
                e.forEach(function (e) {
                    e.innerHTML = i18next.t(e.dataset.i18n);
                });
        }
        s = document.querySelector(".dropdown-notifications-all");
        function r(e) {
            "show.bs.collapse" == e.type || "show.bs.collapse" == e.type ? e.target.closest(".accordion-item").classList.add("active") : e.target.closest(".accordion-item").classList.remove("active");
        }
        const d = document.querySelectorAll(".dropdown-notifications-read");
        s &&
            s.addEventListener("click", (e) => {
                d.forEach((e) => {
                    e.closest(".dropdown-notifications-item").classList.add("marked-as-read");
                });
            }),
            d &&
            d.forEach((t) => {
                t.addEventListener("click", (e) => {
                    t.closest(".dropdown-notifications-item").classList.toggle("marked-as-read");
                });
            }),
            document.querySelectorAll(".dropdown-notifications-archive").forEach((t) => {
                t.addEventListener("click", (e) => {
                    t.closest(".dropdown-notifications-item").remove();
                });
            }),
            [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(function (e) {
                return new bootstrap.Tooltip(e);
            });
        [].slice.call(document.querySelectorAll(".accordion")).map(function (e) {
            e.addEventListener("show.bs.collapse", r), e.addEventListener("hide.bs.collapse", r);
        });
        window.Helpers.setAutoUpdate(!0), window.Helpers.initPasswordToggle(), window.Helpers.initSpeechToText(), window.Helpers.navTabsAnimation(), window.Helpers.initNavbarDropdownScrollbar();
        let c = document.querySelector("[data-template^='horizontal-menu']");
        if (
            (c && (window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT ? window.Helpers.setNavbarFixed("fixed") : window.Helpers.setNavbarFixed("")),
                window.addEventListener(
                    "resize",
                    function (e) {
                        window.innerWidth >= window.Helpers.LAYOUT_BREAKPOINT &&
                            document.querySelector(".search-input-wrapper") &&
                            (document.querySelector(".search-input-wrapper").classList.add("d-none"), (document.querySelector(".search-input").value = "")),
                            c &&
                            (window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT ? window.Helpers.setNavbarFixed("fixed") : window.Helpers.setNavbarFixed(""),
                                setTimeout(function () {
                                    window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT
                                        ? document.getElementById("layout-menu") && document.getElementById("layout-menu").classList.contains("menu-horizontal") && menu.switchMenu("vertical")
                                        : document.getElementById("layout-menu") && document.getElementById("layout-menu").classList.contains("menu-vertical") && menu.switchMenu("horizontal");
                                }, 100)),
                            window.Helpers.navTabsAnimation();
                    },
                    !0
                ),
                !isHorizontalLayout &&
                !window.Helpers.isSmallScreen() &&
                ("undefined" != typeof TemplateCustomizer && (window.templateCustomizer.settings.defaultMenuCollapsed ? window.Helpers.setCollapsed(!0, !1) : window.Helpers.setCollapsed(!1, !1)), "undefined" != typeof config) &&
                config.enableMenuLocalStorage)
        )
            try {
                null !== localStorage.getItem("templateCustomizer-" + templateName + "--LayoutCollapsed") && window.Helpers.setCollapsed("true" === localStorage.getItem("templateCustomizer-" + templateName + "--LayoutCollapsed"), !1);
            } catch (e) { }
    })()


function showToast(delay, iconClass, animationClass, textClass, headerText, message) {
    const liveToast = document.getElementById('liveToast');
    const toastHeader = liveToast.querySelector('.toast-header');
    const iconElement = toastHeader.querySelector('i');
    const headerTextElement = toastHeader.querySelector('.fw-medium');
    const toastBody = liveToast.querySelector('.toast-body');

    // Remove all existing text classes from mdi icon
    iconElement.classList.forEach(className => {
        if (className.startsWith('mdi-text-')) {
            iconElement.classList.remove(className);
        }
    });

    // Update icon class
    iconElement.className = `mdi ${iconClass} me-2 ${textClass}`;

    // Remove previous animation classes except animate__animated
    const animationClasses = [...liveToast.classList];
    animationClasses.forEach(className => {
        if (className.startsWith('animate__') && className !== 'animate__animated') {
            liveToast.classList.remove(className);
        }
    });

    // Add new animation class
    if (animationClass) {
        liveToast.classList.add('animate__animated');
        liveToast.classList.add(animationClass);
    }

    // Update text class
    headerTextElement.className = `me-auto fw-medium ${textClass}`;

    // Update header text
    headerTextElement.textContent = headerText;

    // Update toast message
    toastBody.textContent = message;

    // Show the toast
    new bootstrap.Toast(liveToast, {
        delay: delay
    }).show();
}

function submitFormWithCaptcha(formId) {
    const captchaValue = document.getElementById('captcha').value;
    // if (formId === 'reset-image'){
    // }else{
    let hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'captcha';
    hiddenInput.value = captchaValue;
    document.getElementById(formId).appendChild(hiddenInput);
    document.getElementById(formId).submit();
    // }
}

function closeCaptchaModal() {
    reloadCaptcha();
    $('#captcha-modal-display').modal('hide');
    $('#captcha').val('');
}

function reloadCaptcha() {
    document.getElementById('captcha').value = '';
    let captchaImage = document.getElementById('captchaImage');
    captchaImage.src = '/captcha?' + new Date().getTime(); // Add timestamp to force browser to reload image
}

// function checkAccountState() {
// $.ajax({
// url: "/api/check_user_state",
// type: "POST",
// contentType: "application/json",
// dataType: 'json',
// success: function (responseData, textStatus, jqXHR) {
// if (responseData.status === '210') {
// if (responseData.notifications != null) {
// console.log(responseData.notifications.unviewed_count);
// if (responseData.notifications.unviewed_count > 0) {
// document.getElementById('notification-badge').classList.remove('d-none');
// document.getElementById('notification-count').classList.remove('d-none');
// document.getElementById('notification-count').innerText = responseData.notifications.unviewed_count;
// } else {
// document.getElementById('notification-badge').classList.remove('d-none');
// document.getElementById('notification-count').classList.remove('d-none');
// }
// const panelBody = document.getElementById('notification-panel-body');

// Loop through children in reverse to avoid index shifting issues
// while (panelBody.children.length > 1) {
// panelBody.removeChild(panelBody.lastChild);
// }

// Assuming responseData.notifications.notifications is your array of notifications
// const notifications = responseData.notifications.notifications;

// Generate the HTML for each notification
// const notificationsHTML = notifications.map(notification => `
// <li class="list-group-item list-group-item-action dropdown-notifications-item ${notification.viewed ? 'marked-as-read' : ''}">
// <div class="d-flex">
// <div class="flex-grow-1">
// <h6 class="small mb-1">${notification.title}</h6>
// <small class="mb-1 d-block text-body">You Received A New Notification</small>
// <small class="text-muted">1h ago</small>
// </div>
// <div class="flex-shrink-0 dropdown-notifications-actions">
// <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
// <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="ri-close-line"></span></a>
// </div>
// </div>
// </li>
// `).join('');


// Define the new HTML content using a template literal
// const newPanelHTML = `
// <li class="dropdown-notifications-list scrollable-container">
// <ul class="list-group list-group-flush">
// ${notificationsHTML}
// </ul>
// </li>
// <li class="border-top">
// <div class="d-grid p-4">
// <a class="btn btn-primary btn-sm d-flex" href="javascript:void(0);">
//    {/* <small class="align-middle">View all notifications</small> */}
// </a>
// </div>
// </li>
// `;


// Set the new HTML content
// panelBody.insertAdjacentHTML('beforeend', newPanelHTML);


// if (notifications.length == 0){
// document.getElementById('notification-badge').classList.add('d-none');
// document.getElementById('notification-count').classList.add('d-none');
// while (panelBody.children.length > 1) {
// panelBody.removeChild(panelBody.lastChild);
// }
// const newHtml = `
// <li class="dropdown-menu-body">
// <div class="text-center py-4">
// <i class="mdi mdi-information-outline mdi-36px text-muted mb-2"></i>
// <p class="mb-0 text-muted">No Notifications</p>
// </div>
// </li>
// `
// panelBody.insertAdjacentHTML('beforeend', newHtml);
// }

// }
// } else {
// window.location.replace('/');
// }
// },
// error: function (jqXHR, textStatus, errorThrown) {
// const cookies = document.cookie.split(";");
// for (let i = 0; i < cookies.length; i++) {
// const cookie = cookies[i];
// const eqPos = cookie.indexOf("=");
// const name = eqPos > -1 ? cookie.substring(0, eqPos) : cookie;
// document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
// }
// console.error("Error:", errorThrown);
// }
// });
// }

function checkAccountState() {
    $.ajax({
        url: "/api/check_user_state",
        type: "POST",
        contentType: "application/json",
        dataType: 'json',
        success: function (responseData) {
            if (responseData.status === '210') {
                const { notifications } = responseData;

                if (notifications) {
                    const notificationBadge = document.getElementById('notification-badge');
                    const notificationCount = document.getElementById('notification-count');
                    const panelBody = document.getElementById('notification-panel-body');

                    // Handle notification count and badge visibility
                    const unviewedCount = notifications.unviewed_count || 0;
                    if (unviewedCount > 0) {
                        notificationBadge.classList.remove('d-none');
                        notificationCount.classList.remove('d-none');
                        notificationCount.innerText = unviewedCount;
                    } else {
                        notificationBadge.classList.add('d-none');
                        notificationCount.classList.add('d-none');
                    }

                    // Clear existing notifications (keep the first child, if any)
                    while (panelBody.children.length > 1) {
                        panelBody.removeChild(panelBody.lastChild);
                    }

                    if (notifications.notifications.length > 0) {
                        const notificationsHTML = notifications.notifications.map(notification => `
                        <li class="list-group-item list-group-item-action dropdown-notifications-item">
                            <a href="/notificatiofffffn-view?nid=${notification.enID}" class="d-block text-decoration-none">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="small mb-1">${notification.title}</h6>
                                        <small class="mb-1 d-block text-body">Please check this notification to stay updated.</small>
                                        <small class="text-muted">${formatDateTime(notification.activeTime)}</small>
                                    </div>
                                    <span class="badge ${notification.viewed ? '' : 'bg-info rounded-pill'} ms-2">${notification.viewed ? '' : 'New'}</span>
                                </div>
                            </a>
                        </li>
                        `).join('');

                        const newPanelHTML = `
                            <li class="dropdown-notifications-list scrollable-container">
                                <ul class="list-group list-group-flush">
                                    ${notificationsHTML}
                                </ul>
                            </li>
                            <li class="border-top">
                                <div class="d-grid p-4">
                                    <a class="btn btn-primary btn-sm d-flex" href="/notice-board">
                                        <small class="align-middle">View all notifications</small>
                                    </a>
                                </div>
                            </li>
                        `;
                        panelBody.insertAdjacentHTML('beforeend', newPanelHTML);
                    } else {
                        notificationBadge.classList.add('d-none');
                        notificationCount.classList.add('d-none');

                        const noNotificationsHTML = `
                            <li class="dropdown-menu-body">
                                <div class="text-center py-4">
                                    <i class="mdi mdi-information-outline mdi-36px text-muted mb-2"></i>
                                    <p class="mb-0 text-muted">No Notifications</p>
                                </div>
                            </li>
                        `;
                        panelBody.insertAdjacentHTML('beforeend', noNotificationsHTML);
                    }

                    let storedHashes = JSON.parse(sessionStorage.getItem('notificationHashes')) || [];
                    const currentHashes = notifications.notifications.map(notification => notification.hashedID);
                    const newNotifications = currentHashes.filter(hash => !storedHashes.includes(hash));

                    if (newNotifications.length > 0) {
                        // alert(`You have ${newNotifications.length} new notification(s)!`);
                        showToast(
                            5000, 'mdi-check-circle', 'animate__shakeX', 'text-info', 'New Notifications!', `You’ve got ${newNotifications.length} new notification${newNotifications.length > 1 ? 's' : ''}!`);
                        storedHashes = [...new Set([...storedHashes, ...newNotifications])];
                        sessionStorage.setItem('notificationHashes', JSON.stringify(storedHashes));
                    }
                }
            } else {
                window.location.replace('/');
            }
        },
        error: function () {
            // Clear all cookies on error
            document.cookie.split(";").forEach(cookie => {
                const eqPos = cookie.indexOf("=");
                const name = eqPos > -1 ? cookie.substring(0, eqPos) : cookie;
                document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
            });
        }
    });
}



const inactivityTime = function () {
    let time;
    const logoutTime = 60 * 60 * 1000; // 60 minutes in milliseconds

    // Array of DOM Events
    const events = ['mousemove', 'keydown', 'mousedown', 'scroll', 'touchstart', 'click', 'keypress', 'keyup', 'focus', 'blur'];

    events.forEach(function (name) {
        document.addEventListener(name, resetTimer);
    });
    function logout() {
        location.href = '/logout';
    }
    function resetTimer() {
        clearTimeout(time);
        time = setTimeout(logout, logoutTime);
    }
};

function formatTimestamp(timestamp) {
    let date = new Date(timestamp);

    let day = date.getDate().toString().padStart(2, '0');
    let month = (date.getMonth() + 1).toString().padStart(2, '0');
    let year = date.getFullYear();

    let hour = date.getHours();
    let minute = date.getMinutes().toString().padStart(2, '0');

    let period = (hour >= 12) ? 'PM' : 'AM';
    hour = (hour % 12 === 0) ? 12 : hour % 12;

    let formattedDate = `${day}-${month}-${year} ${hour}:${minute} ${period}`;

    return formattedDate;
}

function isValidTimestamp(timestamp) {
    if (typeof timestamp !== 'number') {
        return false;
    }
    if (timestamp < 0 || isNaN(timestamp) || timestamp > (new Date().getTime() / 1000)) {
        return false;
    }
    return true;
}

function capitalizeFirstLetter(word) {
    return word.charAt(0).toUpperCase() + word.slice(1);
}

function deleteAllCookies() {
    var cookies = document.cookie.split(";");

    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        var eqPos = cookie.indexOf("=");
        var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
    }
}

// function setCookie(cookieName, expirationMinutes) {
// //If expirationMinutes is not defined or not a number, set it to 5 minutes
// if (typeof expirationMinutes !== 'number' || isNaN(expirationMinutes)) {
// expirationMinutes = 5; // Default to 5 minutes
// }

// var d = new Date();
// d.setTime(d.getTime() + (expirationMinutes * 60 * 1000));
// var expires = "expires=" + d.toUTCString();
// document.cookie = cookieName + "=;" + expires + ";path=/";
// }

const imgElement = document.querySelector('.login-logo-container-img');
if (imgElement) {
    imgElement.addEventListener('click', function () {
        window.location.href = '/';
    });
}

function downloadPDF(fileName, path) {
    var element = document.createElement('a');
    element.setAttribute('href', path);
    element.setAttribute('download', fileName);
    element.style.display = 'none';
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
}

function showSpinner() {
    document.getElementById('spinner-overlay').style.display = 'flex';
    document.body.classList.add('spin-lock');
}

function hideSpinner() {
    document.getElementById('spinner-overlay').style.display = 'none';
    document.body.classList.remove('spin-lock');
}

document.addEventListener("DOMContentLoaded", function () {
    if (pageName != 'login' && pageName != 'registration' && pageName != 'reset-password') {
        window.addEventListener('load', inactivityTime, true);
        checkAccountState();
        setInterval(checkAccountState, 90000);
    }
    hideSpinner();
});

document.addEventListener('contextmenu', function (event) {
    event.preventDefault();
    window.open('/error.html', '_blank');
});

// $('#captcha-modal-display').modal('hide');