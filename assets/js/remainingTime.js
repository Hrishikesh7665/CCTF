// Set the countdown date and server time
let countDownDate = ctf_end_js;
let serverTime = php_server_time ;

// Function to update countdown timer
function countdown() {
    // Update server time
    serverTime += 1000;

    // Calculate the time remaining
    let timeLeft = countDownDate - serverTime;

    // Ensure timeLeft doesn't go negative
    timeLeft = Math.max(timeLeft, 0);

    // Calculate days, hours, minutes, and seconds
    const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
    const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

    // Update the HTML elements with the countdown values

    document.getElementById("days").textContent = days + "D : ";
    document.getElementById("hours").textContent = hours + "H : ";
    document.getElementById("mins").textContent = minutes + "M : ";
    document.getElementById("secs").textContent = seconds + "S";

    // Redirect if countdown is finished
    if (timeLeft <= 0) {
        // document.querySelectorAll('.ctf-username')[1].style.display = 'none';
        location.reload();
    }
}

let php_server_time2 = php_server_time;
function updateCountdown() {
    const now2 = php_server_time2;
    php_server_time2 += 1000;

    const distance = ctf_start_js - now2;

    // Time calculations for days, hours, minutes and seconds
    const days2 = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours2 = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes2 = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds2 = Math.floor((distance % (1000 * 60)) / 1000);

    try {
        document.getElementById("upcoming-days").innerHTML = days2;
        document.getElementById("upcoming-hours").innerHTML = hours2;
        document.getElementById("upcoming-minutes").innerHTML = minutes2;
        document.getElementById("upcoming-seconds").innerHTML = seconds2;
    } catch (this_error) {}

    if (distance < 0) {
        location.reload();
    }
}