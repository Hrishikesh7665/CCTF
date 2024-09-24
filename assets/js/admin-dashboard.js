"use strict";

const startTimeInput = document.querySelector(".start-time");
const endTimeInput = document.querySelector(".end-time");
const timeButton = document.getElementById("updateTime");
const serverTimeElement = document.getElementById('server-time');
const serverTime = php_server_time / 1000;

startTimeInput.value = formatTimestamp(ctf_start_js);
endTimeInput.value = formatTimestamp(ctf_end_js);

let php_server_time_local = php_server_time;

// Define color variables
const primaryColor = "#836AF9";
const secondaryColor = "#ffe800";
const accentColor = "#28dac6";
const warningColor = "#ffcf5c";
const backgroundColor = "#EDF1F4";
const linkColor = "#2B9AFF";
const highlightColor = "#84D0FF";

// Declare variables for color styles
let bgColor, titleColor, textColor, borderColor, bodyColor;

// Initialize chart
const barChartElement = document.getElementById("barChart");
const polarChartElement = document.getElementById("polarChart");
const lineAreaChartElement = document.getElementById("lineAreaChart");
const lineChartElement = document.getElementById("lineChart");

let apiUrl = '/api/get_stat';

// Determine color styles based on dark or light theme
if (isDarkStyle) {
	bgColor = config.colors_dark.cardColor;
	titleColor = config.colors_dark.headingColor;
	textColor = config.colors_dark.textMuted;
	bodyColor = config.colors_dark.bodyColor;
	borderColor = config.colors_dark.borderColor;
} else {
	bgColor = config.colors.cardColor;
	titleColor = config.colors.headingColor;
	textColor = config.colors.textMuted;
	bodyColor = config.colors.bodyColor;
	borderColor = config.colors.borderColor;
}


function calculateStepSize(min, max) {
	const range = max - min;

	// Calculate the order of magnitude of the range
	const orderOfMagnitude = Math.floor(Math.log10(range));

	// Calculate backgroundColor rounded step size using the order of magnitude
	const multiplier = Math.pow(10, orderOfMagnitude - 1);
	const roughStepSize = Math.ceil(range / multiplier) * multiplier;

	return roughStepSize;
}


function initialize_barChart(data) {
	let yMin = Math.min(...data.values) - 5;
	let yMax = Math.max(...data.values) + 5;

	new Chart(barChartElement, {
		type: "bar",
		data: {
			labels: data.labels,
			datasets: [
				{
					data: data.values,
					backgroundColor: highlightColor,
					borderColor: "transparent",
					maxBarThickness: 15,
					borderRadius: { topRight: 15, topLeft: 15 },
				},
			],
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			animation: { duration: 1000 },
			plugins: {
				tooltip: {
					rtl: isRtl,
					backgroundColor: bgColor,
					titleColor: titleColor,
					bodyColor: bodyColor,
					borderWidth: 1,
					borderColor: borderColor,
				},
				legend: { display: false },
			},
			scales: {
				x: {
					grid: { color: borderColor, drawBorder: false, borderColor: borderColor },
					ticks: { color: textColor },
				},
				y: {
					min: yMin < 0 ? 0 : yMin,
					max: Math.ceil(Math.round(yMax) / 10) * 10,
					grid: { color: borderColor, drawBorder: false, borderColor: borderColor },
					// ticks: { stepSize: yStepSize, color: textColor },
					ticks: { color: textColor },
				},
			},
		},
	});
}

function initialize_polarChart(data) {
	new Chart(polarChartElement, {
		type: "polarArea",
		data: {
			labels: data.labels,
			datasets: [{
				label: "Total Enrolled",
				backgroundColor: data.hexs,
				// [primaryColor, secondaryColor, "#FF8132", "#299AFF", "#4F5D70", accentColor],
				data: data.values,
				borderWidth: 0
			}]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			animation: {
				duration: 1000
			},
			scales: {
				r: {
					ticks: {
						display: false,
						color: textColor
					},
					grid: {
						display: false
					}
				}
			},
			plugins: {
				tooltip: {
					backgroundColor: bgColor,
					titleColor: titleColor,
					bodyColor: bodyColor,
					borderWidth: 1,
					borderColor: borderColor
				},
				legend: {
					position: "right",
					labels: {
						usePointStyle: true,
						padding: 25,
						boxWidth: 8,
						boxHeight: 8,
						color: bodyColor,
						font: {
							family: "Inter"
						}
					}
				}
			}
		}
	});
}

function initialize_lineAreaChart(data) {
	const maxStudentValue = Math.max(...data.values.Student_view);
	const maxEmpValue = Math.max(...data.values.Employee_view);

	let max0 = (maxStudentValue > maxEmpValue) ? maxStudentValue : maxEmpValue;

	let max =  Math.ceil(Math.round(max0) / 10) * 10;

	new Chart(lineAreaChartElement, {
		type: "line",
		data: {
			labels: data.labels,
			datasets: [
				{
					label: "Student Solved",
					data: data.values.Student_solve,
					tension: 0,
					fill: true,
					backgroundColor: "rgba(34, 139, 34, 0.5)",
					pointStyle: "circle",
					borderColor: "transparent",
					pointRadius: 2.8,
					pointHoverRadius: 5,
					pointHoverBorderWidth: 5,
					pointBorderColor: "transparent",
					pointHoverBackgroundColor: "rgba(34, 139, 34, 0.5)",
					pointHoverBorderColor: bgColor,
				},
				{
					label: "Employee Solved",
					data: data.values.Employee_solve,
					tension: 0,
					fill: true,
					backgroundColor: "rgba(0, 0, 255, 0.5)",
					pointStyle: "circle",
					borderColor: "transparent",
					pointRadius: 2.8,
					pointHoverRadius: 5,
					pointHoverBorderWidth: 5,
					pointBorderColor: "transparent",
					pointHoverBackgroundColor: "rgba(0, 0, 255, 0.5)",
					pointHoverBorderColor: bgColor,
				},
				{
					label: "Student Viewed",
					data: data.values.Student_view,
					tension: 0,
					fill: true,
					backgroundColor: "rgba(144, 238, 144, 0.5)",
					pointStyle: "circle",
					borderColor: "transparent",
					pointRadius: 2.8,
					pointHoverRadius: 5,
					pointHoverBorderWidth: 5,
					pointBorderColor: "transparent",
					pointHoverBackgroundColor: "rgba(144, 238, 144, 0.5)",
					pointHoverBorderColor: bgColor,
				},
				{
					label: "Employee Viewed",
					data: data.values.Employee_view,
					tension: 0,
					fill: true,
					backgroundColor: "rgba(173, 216, 230, 0.5)",
					pointStyle: "circle",
					borderColor: "transparent",
					pointRadius: 2.8,
					pointHoverRadius: 5,
					pointHoverBorderWidth: 5,
					pointBorderColor: "transparent",
					pointHoverBackgroundColor: "rgba(173, 216, 230, 0.5)",
					pointHoverBorderColor: bgColor,
				},
			],		
		},
		options: {
			responsive: !0,
			maintainAspectRatio: !1,
			plugins: {
				legend: { position: "top", rtl: isRtl, align: "start", labels: { usePointStyle: !0, padding: 35, boxWidth: 6, boxHeight: 6, color: bodyColor, font: { family: "Inter" } } },
				tooltip: { rtl: isRtl, backgroundColor: bgColor, titleColor: titleColor, bodyColor: bodyColor, borderWidth: 1, borderColor: borderColor },
			},
			scales: { x: { grid: { color: "transparent", borderColor: borderColor }, ticks: { color: textColor } }, y: { min: 0, max: max, grid: { color: "transparent", borderColor: borderColor }, ticks: { stepSize: (max / 2), color: textColor } } },
		},
	});
	
}

function initialize_lineChart(data) {
	const maxValue = Math.max(...data.values.avgTimes, ...data.values.lowestTimes) + 50;
	const minValue = Math.min(...data.values.avgTimes, ...data.values.lowestTimes) - 50;
	const lowestUserNames = data.values.lowestUserName;

	new Chart(lineChartElement, {
		type: "bar",
		data: {
			labels: data.labels,
			datasets: [
				{ label: "Average Time Taken", data: data.values.avgTimes, backgroundColor: linkColor, borderColor: "transparent", maxBarThickness: 14 },
				{ label: "Lowest Time Taken", data: data.values.lowestTimes, backgroundColor: accentColor, borderColor: "transparent", maxBarThickness: 14 },
			],
		},
		options: {
			indexAxis: "y",
			responsive: !0,
			maintainAspectRatio: !1,
			animation: { duration: 1000 },
			elements: { bar: { borderRadius: { topRight: 15, bottomRight: 15 } } },
			plugins: {
				tooltip: { rtl: isRtl, backgroundColor: bgColor, titleColor: titleColor, bodyColor: bodyColor, borderWidth: 1, borderColor: borderColor, callbacks: {
                    label: function(tooltipItem) {
                        const datasetLabel = tooltipItem.dataset.label || '';
                        const dataPoint = tooltipItem.raw;
                        if (datasetLabel === "Average Time Taken") {
                            return `${datasetLabel} in minutes: ${dataPoint}`;
                        } else if (datasetLabel === "Lowest Time Taken") {
							const userName = lowestUserNames[tooltipItem.dataIndex];
                            return `${datasetLabel} in seconds: ${dataPoint} (${userName})`;
                        }
                    }
                    
                } },
				legend: { position: "top", align: "end", rtl: isRtl, labels: { font: { family: "Inter" }, usePointStyle: !0, padding: 20, boxWidth: 6, boxHeight: 6, color: bodyColor } },
			},
			scales: { x: { min: minValue < 0 ? 0 : minValue, max: Math.ceil(Math.round(maxValue) / 10) * 10, grid: { color: borderColor, borderColor: borderColor, drawBorder: !1 }, ticks: { color: textColor } }, y: { grid: { borderColor: borderColor, display: !1, drawBorder: !1 }, ticks: { color: textColor } } },
		},
	});
}

function validateTimestamp(serverTime, inputTime) {
	const isValidUnixTimestamp = (timestamp) => /^\d{10}$/.test(timestamp) && new Date(timestamp * 1000).getTime() === parseInt(timestamp) * 1000;
	const parsedTime = parseFloat(inputTime);
	if (isNaN(parsedTime)) {
		return {
			isValidTimestamp: false,
			isAfterServerTime: false
		};
	}
	if (parsedTime < 0) {
		return {
			isValidTimestamp: false,
			isAfterServerTime: false
		};
	}
	const numericServerTime = parseFloat(serverTime);
	return {
		isValidTimestamp: true,
		isAfterServerTime: parsedTime >= numericServerTime,
	};
}

function countdownToCTF(from_timestamp, currentTime) {
	let timeRemaining = from_timestamp - currentTime;
	let days = Math.floor(timeRemaining / (60 * 60 * 24));
	let hours = Math.floor((timeRemaining % (60 * 60 * 24)) / (60 * 60));
	let minutes = Math.floor((timeRemaining % (60 * 60)) / 60);
	let seconds = Math.floor(timeRemaining % 60);

	days = (days < 10) ? '0' + days : days;
	hours = (hours < 10) ? '0' + hours : hours;
	minutes = (minutes < 10) ? '0' + minutes : minutes;
	seconds = (seconds < 10) ? '0' + seconds : seconds;

	document.getElementById("remaining-time").innerText = "(" + days + ":" + hours + ":" + minutes + ":" + seconds + ")";
}

function updateServerTime() {
	php_server_time_local += 1000; // Add 1 second to the server time
	let date = new Date(php_server_time_local);

	if (ctf_state === 'upcoming') {
		if (ctf_start_js / 1000 < php_server_time_local / 1000) {
			location.replace(location.href);
		}
		countdownToCTF(ctf_start_js / 1000, php_server_time_local / 1000);
	} else if (ctf_state === 'going') {
		if (ctf_end_js / 1000 < php_server_time_local / 1000) {
			location.replace(location.href);
		}
		countdownToCTF(ctf_end_js / 1000, php_server_time_local / 1000);
	}

	let months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
	let month = months[date.getMonth()];

	let day = date.getDate();
	let year = date.getFullYear();

	let hours = date.getHours();
	let minutes = date.getMinutes();
	let seconds = date.getSeconds();
	let ampm = hours >= 12 ? 'PM' : 'AM';
	hours = hours % 12;
	hours = hours ? hours : 12; // Handle midnight (0 hours)
	minutes = minutes < 10 ? '0' + minutes : minutes;
	seconds = seconds < 10 ? '0' + seconds : seconds;

	let formattedTime = month + ' ' + day + ', ' + year + ' ' + hours + ':' + minutes + ':' + seconds + ' ' + ampm;

	serverTimeElement.textContent = formattedTime;
}

function toggleReadonly(input) {
	input.disabled = false;
}

function updateCompTime() {
	const startTimeString = Number(startTimeInput.value);
	const endTimeString = Number(endTimeInput.value);
	let updateTracker = {};

	const startTimeValidation = validateTimestamp(serverTime, startTimeString);
	const endTimeValidation = validateTimestamp(serverTime, endTimeString);

	if (!startTimeInput.disabled) {
		if (!startTimeValidation.isValidTimestamp) {
			showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Competition Details Updating Error !!', 'Invalid time selected for start time, please try again');
			return false;
		} else if (!startTimeValidation.isAfterServerTime) {
			showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Competition Details Updating Error !!', 'Invalid time selected for start time, please try again');
			return false;
		} else {
			updateTracker.startTime = startTimeString;
		}
	}

	if (!endTimeInput.disabled) {
		if (!endTimeValidation.isValidTimestamp) {
			showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Competition Details Updating Error !!', 'Invalid time selected for end time, please try again');
			return false;
		} else if (!endTimeValidation.isAfterServerTime) {
			showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Competition Details Updating Error !!', 'Invalid time selected for end time, please try again');
			return false;
		} else {
			updateTracker.endTime = endTimeString;
		}
	}


	if (Object.keys(updateTracker).length != 0) {
		showSpinner();
		$.ajax({
			url: '/api/updateCompTime',
			method: 'POST',
			data: updateTracker,
			dataType: 'json',
			success: function (response) {
				hideSpinner();
				if (response.status) {
					showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Competition Details Updated', 'Competition timings updated successfully');
					setTimeout(function () {
						location.replace(location.href);
					}, 5200);
				} else {
					showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Competition Details Updating Error !!', response.message + ', please try again');
				}
			},
			error: function (xhr, status, error) {
				hideSpinner();
				// console.error('AJAX request failed:', error);
			}
		});
	}
}

$(document).ready(function () {
	document.querySelectorAll(".chartjs").forEach(function (element) {
		element.height = element.dataset.height;
	});

	fetch(apiUrl, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json', },
	}).then(response => response.json()).then(data => {
		if (data['showMinimalData']){
			let loginData = data['logUserStat'];
			let centerData = data['centerStat'];
			initialize_barChart(loginData);
			initialize_polarChart(centerData);
		}else{
			let loginData = data['logUserStat'];
			let centerData = data['centerStat'];
			let challengeData = data['challengeStats'];
			let challengeAvgLowestData = data['challengeAvgLowestStats'];
			initialize_barChart(loginData);
			initialize_polarChart(centerData);
			initialize_lineAreaChart(challengeData);
			initialize_lineChart(challengeAvgLowestData);
		}
	}).catch(error => {
		// No data to render charts
		// console.log('Error fetching data:', error);
	});

	if (startTimeInput && endTimeInput) {
		document.querySelectorAll('.input-group-text').forEach(icon => {
			icon.addEventListener('click', function () {
				timeButton.disabled = false;
				const input = this.closest('.input-group').querySelector('input');
				toggleReadonly(input);
				if (!input.disabled) {
					input._flatpickr = flatpickr(input, {
						enableTime: true,
						defaultDate: new Date(ctf_start_js),
						monthSelectorType: "static",
						altInput: true,
						altFormat: "j-m-Y h:i K",
						dateFormat: "U",
						minDate: "today",
						time_24hr: false
					});
				}
			});
		});
	}

	$('input[type=radio][name=registration_state]').change(function () {
		var state = $(this).val();
		showSpinner();
		$.ajax({
			url: '/api/updateCompTime',
			method: 'POST',
			data: {
				registration: state
			},
			dataType: 'json',
			success: function (response) {
				hideSpinner();
				// console.log(response)
				if (response.status) {
					showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Competition Details Updated', 'Competition registration status updated successfully');
				} else {
					showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Competition Details Updating Error !!', response.message + ', please try again');
				}
			},
			error: function (xhr, status, error) {
				hideSpinner();
				// console.error('Error:', error);
			}
		});
	});

	updateServerTime();
	setInterval(updateServerTime, 1000);
});